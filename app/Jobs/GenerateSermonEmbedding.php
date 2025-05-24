<?php

namespace App\Jobs;

use App\Models\Sermon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Pgvector\Laravel\Vector; // From ankane/laravel-pgvector
use Exception;
use App\Services\EmbeddingService; // Placeholder for actual embedding service client

class GenerateSermonEmbedding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $sermonId;

    /**
     * Create a new job instance.
     *
     * @param int $sermonId
     * @return void
     */
    public function __construct(int $sermonId)
    {
        $this->sermonId = $sermonId;
    }

    /**
     * Execute the job.
     *
     * @param EmbeddingService $embeddingService Placeholder for actual embedding service
     * @return void
     */
    public function handle(EmbeddingService $embeddingService): void
    {
        try {
            $sermon = Sermon::findOrFail($this->sermonId);

            // Prepare text for embedding
            // Ensure content is treated as a string. If it's already JSON, json_encode might double-encode.
            // Assuming $sermon->content is an array/object that needs to be JSON encoded for the text.
            // If $sermon->content is already a JSON string from the database, direct concatenation is fine.
            // For this task, assuming it's an array/object from the model accessor or validated data.
            $contentJson = is_string($sermon->content) ? $sermon->content : json_encode($sermon->content);
            $textToEmbed = $sermon->title . ' ' . $contentJson;

            Log::info("GenerateSermonEmbedding: Starting embedding generation for sermon ID: {$this->sermonId}. Text (first 100 chars): " . substr($textToEmbed, 0, 100));

            // Generate embedding (this part uses the placeholder EmbeddingService)
            $embeddingArray = $embeddingService->generateEmbedding($textToEmbed);

            if (empty($embeddingArray)) {
                Log::error("Embedding generation returned empty for sermon ID: {$this->sermonId}. Service might have failed.");
                // Throw an exception to allow job retries if configured
                throw new Exception("Embedding generation returned empty for sermon ID: {$this->sermonId}");
            }
            
            if (count($embeddingArray) !== 384) {
                Log::error("Embedding generation returned unexpected dimensions for sermon ID: {$this->sermonId}. Expected 384, got " . count($embeddingArray));
                // Throw an exception to allow job retries
                throw new Exception("Embedding generation returned unexpected dimensions for sermon ID: {$this->sermonId}");
            }

            // Convert array to Pgvector Vector type
            $vector = new Vector($embeddingArray);

            // Update sermon with the new embedding
            $sermon->update(['content_embedding' => $vector]);

            Log::info("Successfully generated and stored embedding for sermon ID: {$this->sermonId}");

        } catch (Exception $e) {
            Log::error("Error generating sermon embedding for sermon ID: {$this->sermonId} - " . $e->getMessage(), [
                // 'exception_class' => get_class($e), // Useful for specific exception types
                'sermon_id' => $this->sermonId,
                'trace' => $e->getTraceAsString() // Full trace can be very verbose, consider summarizing or removing for production logs
            ]);
            // Re-throw the exception to let Laravel's queue worker handle failure/retry logic
            // This is important for job visibility in Horizon/failed_jobs table
            throw $e;
        }
    }
}
