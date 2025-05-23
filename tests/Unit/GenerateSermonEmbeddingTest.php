<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\GenerateSermonEmbedding;
use App\Models\Sermon;
use App\Services\EmbeddingService; // Placeholder
use Pgvector\Laravel\Vector;
use Illuminate\Foundation\Testing\RefreshDatabase; // Or use mocks entirely
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class GenerateSermonEmbeddingTest extends TestCase
{
    use RefreshDatabase; // Use this if you use factories that hit the DB. Otherwise, full mocking.

    protected MockInterface $embeddingServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock the EmbeddingService
        $this->embeddingServiceMock = Mockery::mock(EmbeddingService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_job_successfully_generates_and_saves_embedding(): void
    {
        // 1. Arrange
        $sermon = Sermon::factory()->create([
            'title' => 'Test Title',
            'content' => json_encode(['key' => 'value']), // content should be JSON string as stored
            'content_embedding' => null, // Ensure it's initially null
        ]);
        $expectedEmbeddingArray = array_fill(0, 384, 0.1);
        // The job expects $sermon->content to be a JSON string if it comes from the DB
        $expectedTextToEmbed = $sermon->title . ' ' . $sermon->content;


        $this->embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->once()
            ->with($expectedTextToEmbed)
            ->andReturn($expectedEmbeddingArray);

        // 2. Act
        $job = new GenerateSermonEmbedding($sermon->id);
        $job->handle($this->embeddingServiceMock);

        // 3. Assert
        $sermon->refresh(); // Re-fetch from DB
        $this->assertInstanceOf(Vector::class, $sermon->content_embedding);
        $this->assertEquals($expectedEmbeddingArray, $sermon->content_embedding->toArray());
    }

    public function test_job_handles_sermon_not_found(): void
    {
        // 1. Arrange
        $nonExistentSermonId = 999;
        
        // Expect the specific ModelNotFoundException to be thrown out of the handle method
        $this->expectException(ModelNotFoundException::class); 

        // Log::error will be called by the job's catch block
        Log::shouldReceive('error')->once()->with(Mockery::on(function($message) use ($nonExistentSermonId) {
            return str_contains($message, "Error generating sermon embedding for sermon ID: {$nonExistentSermonId}") &&
                   str_contains($message, 'No query results for model'); // Eloquent's default message
        }));

        // 2. Act
        $job = new GenerateSermonEmbedding($nonExistentSermonId);
        try {
            $job->handle($this->embeddingServiceMock);
        } finally {
            // 3. Assert
            // Ensure generateEmbedding was not called if sermon not found
            $this->embeddingServiceMock->shouldNotHaveReceived('generateEmbedding');
        }
    }

    public function test_job_handles_embedding_service_failure(): void
    {
        // 1. Arrange
        $sermon = Sermon::factory()->create(['content' => json_encode(['key' => 'value'])]);
        $expectedTextToEmbed = $sermon->title . ' ' . $sermon->content;
        $exceptionMessage = 'Embedding service API is down';

        $this->embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->once()
            ->with($expectedTextToEmbed)
            ->andThrow(new Exception($exceptionMessage));

        // Expect the specific Exception to be re-thrown by the job's handle method
        $this->expectException(Exception::class);
        $this->expectExceptionMessage($exceptionMessage);

        Log::shouldReceive('error')->once()->with(Mockery::on(function($message) use ($sermon, $exceptionMessage) {
            return str_contains($message, "Error generating sermon embedding for sermon ID: {$sermon->id}") &&
                   str_contains($message, $exceptionMessage);
        }));
        
        // 2. Act
        $job = new GenerateSermonEmbedding($sermon->id);
        try {
            $job->handle($this->embeddingServiceMock);
        } finally {
            // 3. Assert
            $sermon->refresh();
            $this->assertNull($sermon->content_embedding); // Should not have been updated
        }
    }

    public function test_job_handles_incorrect_embedding_dimensions(): void
    {
        // 1. Arrange
        $sermon = Sermon::factory()->create(['content' => json_encode(['key' => 'value'])]);
        $incorrectEmbeddingArray = array_fill(0, 100, 0.1); // Not 384 dimensions
        $expectedTextToEmbed = $sermon->title . ' ' . $sermon->content;
        $expectedExceptionMessage = "Embedding generation returned unexpected dimensions for sermon ID: {$sermon->id}";

        $this->embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->once()
            ->with($expectedTextToEmbed)
            ->andReturn($incorrectEmbeddingArray);

        // Expect the specific Exception to be re-thrown by the job's handle method
        $this->expectException(Exception::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        
        // This log comes from the job's main catch block when the specific exception is caught and re-thrown
        Log::shouldReceive('error')->once()->with(Mockery::on(function($message) use ($sermon, $expectedExceptionMessage) {
            return str_contains($message, "Error generating sermon embedding for sermon ID: {$sermon->id}") &&
                   str_contains($message, $expectedExceptionMessage);
        }));
        
        // 2. Act
        $job = new GenerateSermonEmbedding($sermon->id);
        try {
            $job->handle($this->embeddingServiceMock);
        } finally {
            // 3. Assert
            $sermon->refresh();
            $this->assertNull($sermon->content_embedding); // Should not have been updated
        }
    }

    public function test_job_handles_empty_embedding_returned_by_service(): void
    {
        // 1. Arrange
        $sermon = Sermon::factory()->create(['content' => json_encode(['key' => 'value'])]);
        $emptyEmbeddingArray = []; // Service returns empty
        $expectedTextToEmbed = $sermon->title . ' ' . $sermon->content;
        $expectedExceptionMessage = "Embedding generation returned empty for sermon ID: {$sermon->id}";

        $this->embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->once()
            ->with($expectedTextToEmbed)
            ->andReturn($emptyEmbeddingArray);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        Log::shouldReceive('error')->once()->with(Mockery::on(function($message) use ($sermon, $expectedExceptionMessage) {
            return str_contains($message, "Error generating sermon embedding for sermon ID: {$sermon->id}") &&
                   str_contains($message, $expectedExceptionMessage);
        }));

        // 2. Act
        $job = new GenerateSermonEmbedding($sermon->id);
        try {
            $job->handle($this->embeddingServiceMock);
        } finally {
            // 3. Assert
            $sermon->refresh();
            $this->assertNull($sermon->content_embedding);
        }
    }
}
