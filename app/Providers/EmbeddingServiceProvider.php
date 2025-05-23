<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EmbeddingService; // The placeholder

class EmbeddingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Register the placeholder EmbeddingService
        // The GenerateSermonEmbedding job depends on this service.
        // Its internal implementation (whether it uses HuggingFace client directly
        // or is a more elaborate wrapper) can be refined later.
        $this->app->singleton(EmbeddingService::class, function ($app) {
            // For now, the placeholder EmbeddingService doesn't take constructor arguments.
            // If it were to use the HuggingFace client directly, it might be instantiated here:
            // $huggingFaceClient = \Kambo\HuggingFace\HuggingFace::create();
            // return new EmbeddingService($huggingFaceClient);
            return new EmbeddingService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
