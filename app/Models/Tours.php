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
        'exclusions',
        'discount_name',
        'discount'
    ];

    // Accessor for IDR guide fee
    public function getIdrGuideFeeAttribute()
    {
        $conversionRate = CurrencySettings::getConversionRate();
        return $this->guide_fee * $conversionRate;
    }

    public function prices() {
        return $this->hasMany(Prices::class, 'tour_id', 'id');
    }

    public function times() {
        return $this->hasMany(Times::class, 'tour_id', 'id');
    }
}
