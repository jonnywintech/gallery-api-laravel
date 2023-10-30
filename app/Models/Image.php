<?php

namespace App\Models;

use App\Models\User;
use App\Models\Gallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function gallery()
    {
        return $this->hasOne(Gallery::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
