<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyToken extends Model
{
    use HasFactory;

    protected $table = 'ownedToken';
    protected $fillable = [
        'company_id',
        'ai_id',
        'input_token',
        'output_token'
    ];

    public function company()
    {
        return $this->belongsTo(Companies::class, 'company_id');
    }

    public function ai()
    {
        return $this->belongsTo(AiSetting::class, 'ai_id');
    }
}
