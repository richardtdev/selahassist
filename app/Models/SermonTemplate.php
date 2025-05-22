<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SermonTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_id',
        'structure',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'structure' => 'array',
    ];

    /**
     * Check if this is a system template.
     *
     * @return bool
     */
    public function isSystemTemplate()
    {
        return is_null($this->user_id);
    }

    /**
     * Get the user who created this template.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the sermons that use this template.
     */
    public function sermons()
    {
        return $this->hasMany(Sermon::class);
    }
}
