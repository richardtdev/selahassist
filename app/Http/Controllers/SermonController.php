<?php

namespace App\Http\Controllers;

use App\Models\Sermon;
use App\Models\Team;
use App\Jobs\GenerateSermonEmbedding; // Assuming this job will be created
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // For logging errors
use Illuminate\Validation\ValidationException; // For explicit catch if not using $request->validate()
use Exception; // For generic error handling

class SermonController extends Controller
{
    /**
     * Store a newly created sermon in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Team  $team  // Route model binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Team $team): JsonResponse
    {
        // T5.3: Implement team permissions
        // Ensure the authenticated user is part of the provided team.
        if (!Auth::user()->belongsToTeam($team)) {
            return response()->json(['message' => 'You do not belong to this team.'], 403);
        }

        // Further role/permission checks (e.g., can create sermon) might be needed here
        // once spatie/laravel-permission is fully configured by BE1 (Task T4).
        // Example:
        // if (!Auth::user()->hasTeamPermission($team, 'sermon:create')) {
        //     return response()->json(['message' => 'You do not have permission to create sermons for this team.'], 403);
        // }

        try {
            // T5.4: Implement request validation
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|json', // Content should be validated as JSON
                'sermon_template_id' => 'nullable|exists:sermon_templates,id', // Assumes sermon_templates table
                'scripture_reference' => 'nullable|string|max:255',
                'is_draft' => 'sometimes|boolean', // 'sometimes' means it's only validated if present
                'scheduled_date' => 'nullable|date',
            ]);

            // T5.5: Implement sermon data storage
            $sermon = $team->sermons()->create([
                'user_id' => Auth::id(),
                'title' => $validatedData['title'],
                'content' => $validatedData['content'], // Stored as JSON string
                'sermon_template_id' => $validatedData['sermon_template_id'] ?? null,
                'scripture_reference' => $validatedData['scripture_reference'] ?? null,
                'is_draft' => $validatedData['is_draft'] ?? true, // Default to true if not provided
                'scheduled_date' => $validatedData['scheduled_date'] ?? null,
                // 'content_embedding' will be populated by the GenerateSermonEmbedding job
            ]);

            // Dispatch the job to generate embeddings for the sermon content
            GenerateSermonEmbedding::dispatch($sermon->id);

            return response()->json([
                'message' => 'Sermon created successfully. Embedding generation is queued.',
                'sermon' => $sermon
            ], 201);

        } catch (ValidationException $e) {
            // Laravel's $request->validate() automatically throws this and returns a 422 response.
            // This catch block is more for explicit logging or custom response format if needed.
            Log::warning('Sermon creation validation failed.', [
                'team_id' => $team->id,
                'user_id' => Auth::id(),
                'errors' => $e->errors(),
                'request_data' => $request->except(['content']) // Avoid logging large content
            ]);
            // Let Laravel handle the response for ValidationException by re-throwing or not catching.
            // For this task, we'll let it be handled automatically.
            // If a custom response is strictly needed:
            // return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
            throw $e; // Re-throw to let Laravel handle the 422 response
        } catch (Exception $e) {
            // T5.6: Implement error handling
            Log::error('Sermon creation failed: ' . $e->getMessage(), [
                'team_id' => $team->id,
                'user_id' => Auth::id(),
                'request_data' => $request->except(['content']), // Avoid logging potentially large content
                'exception_trace' => $e->getTraceAsString() // Full trace for debugging
            ]);
            return response()->json(['message' => 'An error occurred while creating the sermon.'], 500);
        }
    }
}
