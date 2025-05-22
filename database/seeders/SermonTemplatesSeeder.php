<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SermonTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sermon_templates')->insert([
            [
                'name' => 'Standard Sermon Template',
                'user_id' => null, // System template
                'structure' => json_encode([
                    'sections' => [
                        'introduction' => [
                            'title' => 'Introduction',
                            'description' => 'Begin with prayer, establish connection with audience, and introduce sermon topic.'
                        ],
                        'scripture_reading' => [
                            'title' => 'Scripture Reading',
                            'description' => 'Read the main scripture passage that will be the focus of the sermon.'
                        ],
                        'main_points' => [
                            'title' => 'Main Points',
                            'description' => 'Present 3-5 key points derived from the scripture.'
                        ],
                        'application' => [
                            'title' => 'Application',
                            'description' => 'How does this scripture apply to our lives today?'
                        ],
                        'conclusion' => [
                            'title' => 'Conclusion',
                            'description' => 'Summarize the message and end with a call to action.'
                        ],
                    ]
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Expository Sermon Template',
                'user_id' => null, // System template
                'structure' => json_encode([
                    'sections' => [
                        'introduction' => [
                            'title' => 'Introduction',
                            'description' => 'Introduce the Bible passage and its context.'
                        ],
                        'historical_context' => [
                            'title' => 'Historical Context',
                            'description' => 'Explain the historical setting of the passage.'
                        ],
                        'verse_by_verse' => [
                            'title' => 'Verse by Verse Exposition',
                            'description' => 'Explain each verse in detail, drawing out meaning.'
                        ],
                        'theological_implications' => [
                            'title' => 'Theological Implications',
                            'description' => 'Discuss the theological principles in the passage.'
                        ],
                        'practical_application' => [
                            'title' => 'Practical Application',
                            'description' => 'How these truths should be applied in everyday life.'
                        ],
                        'conclusion' => [
                            'title' => 'Conclusion',
                            'description' => 'Summarize the main points and end with prayer.'
                        ],
                    ]
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Topical Sermon Template',
                'user_id' => null, // System template
                'structure' => json_encode([
                    'sections' => [
                        'introduction' => [
                            'title' => 'Introduction',
                            'description' => 'Introduce the topic and its relevance.'
                        ],
                        'biblical_foundation' => [
                            'title' => 'Biblical Foundation',
                            'description' => 'Present key scriptures relevant to the topic.'
                        ],
                        'point_one' => [
                            'title' => 'First Major Point',
                            'description' => 'First aspect of the topic with supporting scriptures.'
                        ],
                        'point_two' => [
                            'title' => 'Second Major Point',
                            'description' => 'Second aspect of the topic with supporting scriptures.'
                        ],
                        'point_three' => [
                            'title' => 'Third Major Point',
                            'description' => 'Third aspect of the topic with supporting scriptures.'
                        ],
                        'practical_steps' => [
                            'title' => 'Practical Steps',
                            'description' => 'Specific actions listeners can take.'
                        ],
                        'conclusion' => [
                            'title' => 'Conclusion',
                            'description' => 'Summarize and call to action.'
                        ],
                    ]
                ]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
