<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TrxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $price = number_format($this->price, 0, '.', '.');

        // Get conversion rate
        $conversionRate = \App\Models\CurrencySettings::getConversionRate();

        // status : 0 = pending, 1 = success, 2 = reject
        if($this->status == 1){
            $status = 'success';
        }else if($this->status == 2){
            $status = 'reject';
        }else{
            $status = 'pending';
        }

        return [
            'id' => $this->id,
            'dateRequest' => $this->date->format('d M Y'),
            'dateBooking' => $this->booking->date->format('d M Y'),
            'refid' => $this->booking->ref_id,
            'guestName' => $this->booking->name,
            'hotel' => $this->booking->hotel,
            'guide_fee' => 'IDR '.number_format($this->booking->guide_fee, 0, '.', '.'),
            'guide_fee_idr' => 'IDR '.number_format($this->booking->idr_guide_fee, 0, '.', '.'),
            'collect' => 'USD '.number_format($this->booking->collect, 0, '.', '.').' / '.'IDR '.number_format($this->booking->collect*$conversionRate, 0, '.', '.'),
            'collect_usd' => 'USD '.number_format($this->booking->collect, 0, '.', '.'),
            'collect_idr' => 'IDR '.number_format($this->booking->idr_price, 0, '.', '.'),
            'susuk' => 'IDR '.number_format(($this->booking->susuk_guide+$this->booking->susuk_hbd), 0, '.', '.'),
            'susuk_idr' => 'IDR '.number_format($this->booking->idr_susuk_guide + $this->booking->idr_susuk_hbd, 0, '.', '.'),
            'cost' => 'IDR '.number_format($this->booking->tiket_total, 0, '.', '.'),
            'cost_idr' => 'IDR '.number_format($this->booking->idr_tiket_total, 0, '.', '.'),
            'additional' => 'IDR '.number_format($this->booking->additional_price, 0, '.', '.'),
            'additional_idr' => 'IDR '.number_format($this->booking->idr_additional_price, 0, '.', '.'),
            'totalGuideFee' => $this->booking->guide_fee,
            'totalGuideFee_idr' => $this->booking->idr_guide_fee,
            'totalSusuk' => $this->booking->susuk_guide+$this->booking->susuk_hbd,
            'totalSusuk_idr' => $this->booking->idr_susuk_guide + $this->booking->idr_susuk_hbd,
            'totalCost' => $this->booking->tiket_total,
            'totalCost_idr' => $this->booking->idr_tiket_total,
            'totalAdditional' => $this->booking->additional_price,
            'totalAdditional_idr' => $this->booking->idr_additional_price,
            'status' => $status
        ];
    }
}
