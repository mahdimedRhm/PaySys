<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'code',
    ];

    public function sender()
    {
        return $this->hasOne(User::class);
    }

    public function receiver()
    {
        return $this->hasOne(User::class);
    }
    
}
