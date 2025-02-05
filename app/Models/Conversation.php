<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'user_id',
        'title',
        'prompt_message',
        'project_details',
        'skills_experience',
        'upload_file',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
