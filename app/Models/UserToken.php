<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    use HasFactory;

    protected $table = 'userOwnedToken';
    protected $fillable = [
        'user_id',
        'ai_id',
        'input_token',
        'output_token'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ai()
    {
        return $this->belongsTo(AiSetting::class, 'ai_id');
    }
}
