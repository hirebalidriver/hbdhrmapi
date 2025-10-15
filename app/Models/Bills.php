<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'destination_id',
        'destination_name',
        'photo',
        'people',
        'price',
        'note',
        'is_susuk',
    ];

    // Accessor for IDR price
    public function getIdrPriceAttribute()
    {
        $conversionRate = CurrencySettings::getConversionRate();
        return $this->price * $conversionRate;
    }
}
