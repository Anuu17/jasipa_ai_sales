<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    use HasFactory;

    protected $table = 'prompt';
    protected $fillable = [
        'message',
        'user_id',
        'company_id',
        'is_company_admin',
        'is_company_setting'
    ];

    public function company()
    {
        return $this->belongsTo(Companies::class, 'company_id');
    }
}
