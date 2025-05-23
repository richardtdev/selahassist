<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Team;
use App\Models\Sermon;
use App\Jobs\GenerateSermonEmbedding;
use Illuminate\Support\Facades\Queue;

class SermonCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a default user and team
        $this->user = User::factory()->create();
        // Ensure the user has a personal team if Jetstream is configured that way
        // or create a new team and set them as owner.
        // The prompt has $this->user->ownedTeams()->create(...) which is correct.
        $this->team = Team::factory()->create(['user_id' => $this->user->id, 'name' => 'Test Team', 'personal_team' => false]);
        $this->user->switchTeam($this->team); // Ensure user is on the team context
    }

    public function test_authenticated_user_can_create_sermon_for_their_team(): void
    {
        Queue::fake();

        $sermonData = [
            'title' => 'Test Sermon Title',
            'content' => json_encode(['main_points' => ['Point 1', 'Point 2']]),
            // Add other optional fields if necessary for validation
        ];

        $response = $this->actingAs($this->user)
                         ->postJson("/api/teams/{$this->team->id}/sermons", $sermonData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['message' => 'Sermon created successfully. Embedding generation is queued.']);

        $this->assertDatabaseHas('sermons', [
            'team_id' => $this->team->id,
            'user_id' => $this->user->id,
            'title' => 'Test Sermon Title',
        ]);

        // Get the created sermon to check its ID for the job assertion
        $createdSermon = Sermon::where('title', 'Test Sermon Title')->first();
        $this->assertNotNull($createdSermon);

        Queue::assertPushed(GenerateSermonEmbedding::class, function ($job) use ($createdSermon) {
            return $job->sermonId === $createdSermon->id;
        });
    }

    public function test_user_cannot_create_sermon_for_a_team_they_do_not_belong_to(): void
    {
        Queue::fake();
        // Create another user and their team
        $otherUser = User::factory()->create();
        $otherTeam = Team::factory()->create(['user_id' => $otherUser->id, 'name' => 'Other Team', 'personal_team' => false]);
        
        $sermonData = [
            'title' => 'Unauthorized Sermon',
            'content' => json_encode(['text' => 'test content']),
        ];

        // Current user ($this->user) tries to post to $otherTeam
        $response = $this->actingAs($this->user)
                         ->postJson("/api/teams/{$otherTeam->id}/sermons", $sermonData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('sermons', ['title' => 'Unauthorized Sermon']);
        Queue::assertNotPushed(GenerateSermonEmbedding::class);
    }

    public function test_sermon_creation_fails_with_missing_title(): void
    {
        Queue::fake();
        $sermonData = [
            // title is missing
            'content' => json_encode(['text' => 'test content']),
        ];

        $response = $this->actingAs($this->user)
                         ->postJson("/api/teams/{$this->team->id}/sermons", $sermonData);

        $response->assertStatus(422) // Validation error
                 ->assertJsonValidationErrors(['title']);
        
        Queue::assertNotPushed(GenerateSermonEmbedding::class);
    }
    
    public function test_sermon_creation_fails_with_invalid_content_not_json(): void
    {
        Queue::fake();
        $sermonData = [
            'title' => 'Valid Title',
            'content' => 'This is not JSON content', // Invalid content
        ];

        $response = $this->actingAs($this->user)
                         ->postJson("/api/teams/{$this->team->id}/sermons", $sermonData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['content']);
        
        Queue::assertNotPushed(GenerateSermonEmbedding::class);
    }

    public function test_guest_user_cannot_create_sermon(): void
    {
        Queue::fake();
        // User is not authenticated (no $this->actingAs())
        $sermonData = [
            'title' => 'Guest Sermon',
            'content' => json_encode(['text' => 'test content']),
        ];

        $response = $this->postJson("/api/teams/{$this->team->id}/sermons", $sermonData);

        $response->assertStatus(401); 
        Queue::assertNotPushed(GenerateSermonEmbedding::class);
    }
}
