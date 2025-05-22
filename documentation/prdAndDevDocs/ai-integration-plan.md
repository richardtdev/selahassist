# SermonAssist AI Integration Plan

This document outlines the integration strategy between the Laravel SaaS platform and the AI systems (Bible Indexing System, News Monitoring System, and RAG System) for SermonAssist.

## 1. Architecture Overview

![Architecture Diagram]

```
[SermonAssist Laravel SaaS]
    | 
    | HTTPS API Calls (TLS 1.3)
    |
    |-----> [Bible Indexing System API]
    |         | Supabase PostgreSQL with pgvector
    |         | Indexed verses from multiple translations
    |
    |-----> [News Monitoring System API]
    |         | News articles and summaries
    |         | Daily updates with scripture connections
    |
    |-----> [RAG System API]
            | Semantic search capabilities
            | Response generation for theological queries
```

The Laravel SaaS will be a client of these separate AI systems, consuming their APIs to provide features to end users.

## 2. Integration Approach

### 2.1 API Gateway Strategy

1. **Centralized Client Services**
   
   The Laravel application will implement a set of client services that abstract the communication with the AI systems:

   ```php
   // app/Services/BibleIndexingService.php
   class BibleIndexingService
   {
       protected $httpClient;
       protected $apiKey;
       protected $baseUrl;
       
       public function __construct(HttpClient $client)
       {
           $this->httpClient = $client;
           $this->apiKey = config('services.bible_indexing.api_key');
           $this->baseUrl = config('services.bible_indexing.base_url');
       }
       
       public function getVerseByReference(string $translation, string $reference)
       {
           return $this->httpClient->withToken($this->apiKey)
               ->get("{$this->baseUrl}/verses", [
                   'translation' => $translation,
                   'reference' => $reference
               ])
               ->json();
       }
       
       public function searchVersesByTheme(string $theme, string $translation, int $limit = 5)
       {
           return $this->httpClient->withToken($this->apiKey)
               ->post("{$this->baseUrl}/rpc/search_verses_by_theme", [
                   'theme_text' => $theme,
                   'translation' => $translation,
                   'limit_count' => $limit
               ])
               ->json();
       }
   }
   ```

2. **Service Provider Registration**

   ```php
   // app/Providers/AIServiceProvider.php
   class AIServiceProvider extends ServiceProvider
   {
       public function register()
       {
           $this->app->singleton(BibleIndexingService::class, function ($app) {
               return new BibleIndexingService($app->make(HttpClient::class));
           });
           
           $this->app->singleton(NewsMonitoringService::class, function ($app) {
               return new NewsMonitoringService($app->make(HttpClient::class));
           });
           
           $this->app->singleton(RAGService::class, function ($app) {
               return new RAGService($app->make(HttpClient::class));
           });
       }
   }
   ```

3. **Facade for Simplified Access** (optional)

   ```php
   // app/Facades/Bible.php
   class Bible extends Facade
   {
       protected static function getFacadeAccessor()
       {
           return BibleIndexingService::class;
       }
   }
   ```

### 2.2 Caching Strategy

To optimize performance and reduce API calls, the Laravel application will implement a caching layer:

```php
// Inside BibleIndexingService
public function getVerseByReference(string $translation, string $reference)
{
    $cacheKey = "bible:{$translation}:{$reference}";
    
    return Cache::remember($cacheKey, now()->addDay(), function () use ($translation, $reference) {
        return $this->httpClient->withToken($this->apiKey)
            ->get("{$this->baseUrl}/verses", [
                'translation' => $translation,
                'reference' => $reference
            ])
            ->json();
    });
}
```

### 2.3 Error Handling

The services will implement comprehensive error handling:

```php
// Inside BibleIndexingService
public function getVerseByReference(string $translation, string $reference)
{
    try {
        // Cache and API call logic
    } catch (ConnectionException $e) {
        Log::error("Bible API connection error: {$e->getMessage()}", [
            'translation' => $translation,
            'reference' => $reference
        ]);
        
        // Return fallback data or throw a custom exception
        return $this->getFallbackVerse($translation, $reference);
    } catch (ClientException $e) {
        // Handle 4xx errors
    } catch (Exception $e) {
        // Handle unexpected errors
    }
}

protected function getFallbackVerse(string $translation, string $reference)
{
    // Return a cached version or a static fallback
}
```

## 3. API Endpoints

### 3.1 Bible Indexing System API

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/verses` | GET | Retrieve verses by reference | `translation`, `reference` |
| `/rpc/search_verses_by_theme` | POST | Search verses by theme | `theme_text`, `translation`, `limit_count` |

Sample response:
```json
{
  "reference": "John 3:16",
  "text": "For God so loved the world, that he gave his only begotten Son...",
  "translation": "KJV"
}
```

### 3.2 News Monitoring System API

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/articles` | GET | Get news articles | `source`, `limit`, `offset` |
| `/summaries/daily` | GET | Get today's summaries | `limit` |

Sample response:
```json
{
  "summary": "New research on biblical archaeology has uncovered evidence...",
  "themes": ["archaeology", "biblical history"],
  "scripture_connections": ["Genesis 11:31", "Joshua 6:1-5"],
  "source": "Christian Post",
  "published_at": "2025-05-16T14:30:00Z"
}
```

### 3.3 RAG System API

| Endpoint | Method | Description | Parameters |
|----------|--------|-------------|------------|
| `/rag/query` | POST | Ask theological question | `query`, `translation`, `max_results` |

Sample response:
```json
{
  "query": "What does the Bible say about faith?",
  "response": "Faith is central to Christianity. The Bible defines faith as...",
  "verses": [
    {"reference": "Hebrews 11:1", "text": "Now faith is...", "similarity": 0.95}
  ],
  "articles": [
    {"title": "Understanding Faith in Modern Times", "similarity": 0.82}
  ]
}
```

## 4. Feature Implementation

### 4.1 Rich Text Editor with Bible Integration

The Tiptap editor will have a custom extension for Bible verse insertion:

```javascript
// resources/js/Components/BibleVerseExtension.js
import { Extension } from '@tiptap/core'

export const BibleVerse = Extension.create({
  name: 'bibleVerse',
  
  addCommands() {
    return {
      insertBibleVerse: (attributes) => ({ chain }) => {
        return chain()
          .insertContent({
            type: 'bibleVerse',
            attrs: attributes,
          })
          .run()
      },
    }
  },
  
  // Implementation details...
})
```

User interface for verse selection:

```vue
<!-- resources/js/Components/BibleVerseSelector.vue -->
<template>
  <div class="bible-verse-selector">
    <div class="flex space-x-2">
      <select v-model="translation" class="form-select">
        <option value="KJV">King James Version</option>
        <option value="NIV">New International Version</option>
        <option value="NKJV">New King James Version</option>
      </select>
      
      <input 
        v-model="reference" 
        placeholder="John 3:16" 
        class="form-input"
        @keyup.enter="searchVerse"
      />
      
      <button @click="searchVerse" class="btn btn-primary">
        Insert Verse
      </button>
    </div>
    
    <div v-if="loading" class="mt-2">
      Searching...
    </div>
    
    <div v-if="verse" class="mt-2 p-2 border rounded">
      <div class="font-bold">{{ verse.reference }} ({{ verse.translation }})</div>
      <div>{{ verse.text }}</div>
      <button @click="insertVerse" class="btn btn-sm btn-success mt-2">
        Insert
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      translation: 'KJV',
      reference: '',
      verse: null,
      loading: false,
    }
  },
  
  methods: {
    async searchVerse() {
      this.loading = true;
      
      try {
        // Call API through Laravel backend
        const response = await axios.get('/api/bible/verse', {
          params: {
            translation: this.translation,
            reference: this.reference
          }
        });
        
        this.verse = response.data;
      } catch (error) {
        // Handle error
      } finally {
        this.loading = false;
      }
    },
    
    insertVerse() {
      this.$emit('insert', this.verse);
    }
  }
}
</script>
```

### 4.2 News Summaries Dashboard

The pastor dashboard will include a news summary widget:

```vue
<!-- resources/js/Components/NewsSummariesWidget.vue -->
<template>
  <div class="news-summaries-widget">
    <h3 class="text-lg font-bold mb-4">Today's News</h3>
    
    <div v-if="loading" class="p-4 text-center">
      <div class="spinner"></div>
      Loading summaries...
    </div>
    
    <div v-else-if="summaries.length === 0" class="p-4 text-center text-gray-500">
      No summaries available for today.
    </div>
    
    <div v-else>
      <div 
        v-for="summary in summaries" 
        :key="summary.id" 
        class="mb-4 p-3 border rounded"
      >
        <div class="font-medium">{{ summary.source }} • {{ formatDate(summary.published_at) }}</div>
        <p class="my-2">{{ summary.summary }}</p>
        
        <div v-if="summary.themes.length > 0" class="mt-2">
          <span 
            v-for="theme in summary.themes" 
            :key="theme"
            class="inline-block px-2 py-1 mr-2 text-xs bg-blue-100 text-blue-800 rounded"
          >
            {{ theme }}
          </span>
        </div>
        
        <div v-if="summary.scripture_connections.length > 0" class="mt-2">
          <div class="text-sm font-medium">Scripture Connections:</div>
          <div class="flex flex-wrap">
            <div 
              v-for="reference in summary.scripture_connections" 
              :key="reference"
              class="mr-2 mt-1 cursor-pointer text-blue-600 hover:underline"
              @click="viewVerse(reference)"
            >
              {{ reference }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      summaries: [],
      loading: true,
    }
  },
  
  mounted() {
    this.fetchSummaries();
  },
  
  methods: {
    async fetchSummaries() {
      try {
        const response = await axios.get('/api/news/summaries/daily');
        this.summaries = response.data;
      } catch (error) {
        // Handle error
      } finally {
        this.loading = false;
      }
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString();
    },
    
    viewVerse(reference) {
      // Show verse details in a modal
    }
  }
}
</script>
```

### 4.3 Sermon Outline Generation

The sermon creation interface will include AI-assisted outline generation:

```vue
<!-- resources/js/Components/SermonOutlineGenerator.vue -->
<template>
  <div class="sermon-outline-generator">
    <h3 class="text-lg font-bold mb-4">Generate Sermon Outline</h3>
    
    <div class="mb-4">
      <label class="block mb-2">Main Scripture</label>
      <input 
        v-model="mainScripture" 
        placeholder="e.g., John 3:16 or Matthew 5:1-12" 
        class="form-input w-full"
      />
    </div>
    
    <div class="mb-4">
      <label class="block mb-2">Topic or Theme (optional)</label>
      <input 
        v-model="topic" 
        placeholder="e.g., Faith, Love, Forgiveness" 
        class="form-input w-full"
      />
    </div>
    
    <button 
      @click="generateOutline" 
      class="btn btn-primary w-full"
      :disabled="loading || !mainScripture"
    >
      {{ loading ? 'Generating...' : 'Generate Outline' }}
    </button>
    
    <div v-if="outline" class="mt-4 p-4 border rounded">
      <h4 class="font-bold mb-2">{{ outline.title }}</h4>
      
      <ol class="list-decimal pl-5">
        <li 
          v-for="(point, index) in outline.points" 
          :key="index"
          class="mb-2"
        >
          <div class="font-medium">{{ point.title }}</div>
          <div v-if="point.scripture" class="text-sm text-gray-600">
            {{ point.scripture }}
          </div>
        </li>
      </ol>
      
      <div class="mt-4 flex justify-end">
        <button @click="useOutline" class="btn btn-success">
          Use This Outline
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      mainScripture: '',
      topic: '',
      outline: null,
      loading: false,
    }
  },
  
  methods: {
    async generateOutline() {
      this.loading = true;
      
      try {
        // Call the RAG system through Laravel backend
        const response = await axios.post('/api/sermons/generate-outline', {
          main_scripture: this.mainScripture,
          topic: this.topic
        });
        
        this.outline = response.data;
      } catch (error) {
        // Handle error
      } finally {
        this.loading = false;
      }
    },
    
    useOutline() {
      this.$emit('use-outline', this.outline);
    }
  }
}
</script>
```

## 5. Authentication & Security

### 5.1 API Authentication

The Laravel SaaS will authenticate with the AI systems using API keys stored securely in environment variables:

```
# .env
BIBLE_INDEXING_API_KEY=your_secure_api_key
BIBLE_INDEXING_BASE_URL=https://api.selahverse.com/bible

NEWS_MONITORING_API_KEY=your_secure_api_key
NEWS_MONITORING_BASE_URL=https://api.selahverse.com/news

RAG_SYSTEM_API_KEY=your_secure_api_key
RAG_SYSTEM_BASE_URL=https://api.selahverse.com/rag
```

Configuration in Laravel:

```php
// config/services.php
return [
    // Other services...
    
    'bible_indexing' => [
        'api_key' => env('BIBLE_INDEXING_API_KEY'),
        'base_url' => env('BIBLE_INDEXING_BASE_URL'),
    ],
    
    'news_monitoring' => [
        'api_key' => env('NEWS_MONITORING_API_KEY'),
        'base_url' => env('NEWS_MONITORING_BASE_URL'),
    ],
    
    'rag_system' => [
        'api_key' => env('RAG_SYSTEM_API_KEY'),
        'base_url' => env('RAG_SYSTEM_BASE_URL'),
    ],
];
```

### 5.2 Rate Limiting

The Laravel application will implement rate limiting for API requests:

```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'api' => [
        // Other middleware...
        'throttle:api',
    ],
];

// routes/api.php
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/bible/verse', [BibleController::class, 'getVerse']);
    Route::post('/bible/search', [BibleController::class, 'searchVerses']);
    // Other Bible routes...
});

Route::middleware('throttle:30,1')->group(function () {
    Route::get('/news/summaries/daily', [NewsController::class, 'getDailySummaries']);
    // Other News routes...
});

Route::middleware('throttle:20,1')->group(function () {
    Route::post('/sermons/generate-outline', [SermonController::class, 'generateOutline']);
    // Other RAG routes...
});
```

## 6. Queue System for Asynchronous Processing

For operations that might take longer to process (like generating sermon outlines), the Laravel application will use queues:

```php
// app/Jobs/GenerateSermonOutline.php
class GenerateSermonOutline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $mainScripture;
    protected $topic;
    protected $sermonId;
    
    public function __construct($userId, $mainScripture, $topic, $sermonId)
    {
        $this->userId = $userId;
        $this->mainScripture = $mainScripture;
        $this->topic = $topic;
        $this->sermonId = $sermonId;
    }
    
    public function handle(RAGService $ragService)
    {
        // Call the RAG service
        $outline = $ragService->generateSermonOutline(
            $this->mainScripture,
            $this->topic
        );
        
        // Update the sermon with the outline
        $sermon = Sermon::find($this->sermonId);
        $sermon->update([
            'outline' => $outline,
            'status' => 'outline_generated'
        ]);
        
        // Notify the user
        $user = User::find($this->userId);
        $user->notify(new SermonOutlineGenerated($sermon));
    }
}
```

Usage in controller:

```php
// app/Http/Controllers/SermonController.php
public function generateOutline(Request $request)
{
    $request->validate([
        'main_scripture' => 'required|string',
        'topic' => 'nullable|string',
    ]);
    
    // Create a sermon draft
    $sermon = Sermon::create([
        'user_id' => Auth::id(),
        'main_scripture' => $request->main_scripture,
        'topic' => $request->topic,
        'status' => 'outline_pending'
    ]);
    
    // Dispatch the job
    GenerateSermonOutline::dispatch(
        Auth::id(),
        $request->main_scripture,
        $request->topic,
        $sermon->id
    );
    
    return response()->json([
        'message' => 'Outline generation has been queued',
        'sermon_id' => $sermon->id
    ]);
}
```

## 7. Testing Strategy

### 7.1 Unit Tests for API Clients

```php
// tests/Unit/Services/BibleIndexingServiceTest.php
class BibleIndexingServiceTest extends TestCase
{
    use MocksHttp;
    
    /** @test */
    public function it_can_get_verse_by_reference()
    {
        // Mock HTTP responses
        $this->mockHttpResponse([
            'reference' => 'John 3:16',
            'text' => 'For God so loved the world...',
            'translation' => 'KJV'
        ]);
        
        $service = new BibleIndexingService($this->httpClient);
        $verse = $service->getVerseByReference('KJV', 'John 3:16');
        
        $this->assertEquals('John 3:16', $verse['reference']);
        $this->assertEquals('KJV', $verse['translation']);
    }
    
    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        // Mock HTTP error response
        $this->mockHttpError(500, 'Server error');
        
        $service = new BibleIndexingService($this->httpClient);
        $verse = $service->getVerseByReference('KJV', 'John 3:16');
        
        // Should return fallback data
        $this->assertIsArray($verse);
        $this->assertArrayHasKey('reference', $verse);
    }
}
```

### 7.2 Feature Tests for Integration

```php
// tests/Feature/BibleVerseApiTest.php
class BibleVerseApiTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function authenticated_users_can_fetch_bible_verses()
    {
        $user = User::factory()->create();
        
        // Mock the BibleIndexingService
        $this->mock(BibleIndexingService::class, function ($mock) {
            $mock->shouldReceive('getVerseByReference')
                ->with('KJV', 'John 3:16')
                ->andReturn([
                    'reference' => 'John 3:16',
                    'text' => 'For God so loved the world...',
                    'translation' => 'KJV'
                ]);
        });
        
        $response = $this->actingAs($user)
            ->getJson('/api/bible/verse?translation=KJV&reference=John 3:16');
        
        $response->assertStatus(200)
            ->assertJson([
                'reference' => 'John 3:16',
                'translation' => 'KJV'
            ]);
    }
}
```

## 8. Monitoring & Performance

The integration will include monitoring to ensure reliability:

1. **API Request Logging**

```php
// Inside service class methods
$startTime = microtime(true);
$result = $this->makeApiCall();
$duration = microtime(true) - $startTime;

Log::info("API call to {$endpoint}", [
    'duration' => $duration,
    'success' => isset($result),
    'params' => $params
]);
```

2. **Sentry Integration for Error Tracking**

```php
// app/Exceptions/Handler.php
public function register()
{
    $this->reportable(function (Throwable $e) {
        if (app()->bound('sentry')) {
            app('sentry')->captureException($e);
        }
    });
}
```

## 9. Future Integration Considerations

1. **WebSocket Integration**: For real-time updates on sermon generation progress

2. **Batch Processing**: For bulk operations like generating sermon series outlines

3. **AI Model Version Management**: Tracking which AI model version was used for content generation

4. **Custom AI Endpoints**: Potential for custom AI endpoints optimized for SermonAssist-specific needs

5. **Direct Database Access**: In future phases, direct database connections might be explored for performance-critical operations

By implementing this integration strategy, the SermonAssist platform will leverage the powerful AI capabilities provided by the separate systems while maintaining a clean, maintainable architecture in the Laravel SaaS application.