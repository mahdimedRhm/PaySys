<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rsakey extends Model
{
    use HasFactory;

    protected $fillable = [
        'key'
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
