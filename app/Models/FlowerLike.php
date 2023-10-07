<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowerLike extends Model
{
    use HasFactory, HasUuids;

    /**
     * Relates the FlowerLike to the Flower
     */
    public function flower() {
        return $this->belongsTo('App\Models\Flower');
    }

    /**
     * Relates the FlowerLike to the User
     */
    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
