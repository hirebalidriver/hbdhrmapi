<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tours extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'itinerary',
        'description',
        'note',
        'status',
        'guide_fee',
        'inclusions',
        'exclusions'
    ];

    public function prices() {
        return $this->hasMany(Prices::class, 'tour_id', 'id');
    }

    public function times() {
        return $this->hasMany(Times::class, 'tour_id', 'id');
    }
}
