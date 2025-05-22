<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sermon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'sermon_template_id',
        'title',
        'scripture_reference',
        'content',
        'is_draft',
        'scheduled_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
        'is_draft' => 'boolean',
        'scheduled_date' => 'datetime',
    ];

    /**
     * Get the user who created the sermon.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team that the sermon belongs to.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the template that the sermon uses.
     */
    public function template()
    {
        return $this->belongsTo(SermonTemplate::class, 'sermon_template_id');
    }
}
