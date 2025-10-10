<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencySettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description'
    ];

    protected $casts = [
        'value' => 'decimal:2'
    ];

    public static function getConversionRate()
    {
        $setting = self::where('key', 'usd_to_idr_rate')->first();
        return $setting ? $setting->value : 16500.00;
    }
}
