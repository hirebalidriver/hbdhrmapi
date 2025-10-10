<?php

namespace App\Http\Controllers;

use App\Models\CurrencySettings;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;

class CurrencySettingsController extends Controller
{
    public function index()
    {
        $settings = CurrencySettings::where('key', 'usd_to_idr_rate')->first();
        
        if (!$settings) {
            // Create default setting if it doesn't exist
            $settings = CurrencySettings::create([
                'key' => 'usd_to_idr_rate',
                'value' => 16500.00,
                'description' => 'USD to IDR Conversion Rate'
            ]);
        }
        
        return ResponseFormatter::success($settings, 'Currency settings retrieved successfully');
    }

    public function update(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric|min:0',
        ]);

        $settings = CurrencySettings::where('key', 'usd_to_idr_rate')->first();
        
        if (!$settings) {
            $settings = CurrencySettings::create([
                'key' => 'usd_to_idr_rate',
                'value' => $request->value,
                'description' => 'USD to IDR Conversion Rate'
            ]);
        } else {
            $settings->update([
                'value' => $request->value
            ]);
        }

        return ResponseFormatter::success($settings, 'Currency settings updated successfully');
    }
}
