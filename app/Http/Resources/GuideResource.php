<?php

namespace App\Http\Resources;

use App\Models\Balances;
use Illuminate\Http\Resources\Json\JsonResource;

class GuideResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $balance = Balances::where('guide_id', $this->id)->latest()->first();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'balance' => 'IDR '.number_format($balance->balance, 0, '.', '.'),
            'balance_idr' => 'IDR '.number_format($balance->balance * \App\Models\CurrencySettings::getConversionRate(), 0, '.', '.'),
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'password' => $this->password,
            'phone' => $this->phone,
            'ktp_number' => $this->ktp_number,
            'ktp_url' => $this->ktp_url,
            'code' => $this->code,
            'address' => $this->address,
            'status' => $this->status,
            'profile' => $this->profile,
            'car_photo' => $this->car_photo,
            'car_type' => $this->car_type,
            'plat_number' => $this->plat_number,
            'car_color' => $this->car_color,
            'bank_name' => $this->bank_name,
            'bank_number' => $this->bank_number,
            'bank_account' => $this->bank_account,
            'fcm_token' => $this->fcm_token,
        ];
    }
}
