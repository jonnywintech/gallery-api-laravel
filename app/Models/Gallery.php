<?php

namespace App\Models;

use App\Models\User;
use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gallery extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
