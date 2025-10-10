<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TrxDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // Get conversion rate
        $conversionRate = \App\Models\CurrencySettings::getConversionRate();

        // status : 0 = pending, 1 = success, 2 = reject
        // if($this->status == 1){
        //     $status = 'success';
        // }else if($this->status == 2){
        //     $status = 'reject';
        // }else{
        //     $status = 'pending';
        // }

        return [
            'id' => $this->id,
            'refid' => $this->booking->ref_id,
            'date' => $this->booking->date->format('d M Y'),
            'name' => $this->booking->name,
            'supplier' => $this->booking->supplier,
            'hotel' => $this->booking->hotel,
            'adult' => $this->booking->adult,
            'child' => $this->booking->child,
            'guide_fee' => 'IDR '.number_format($this->booking->guide_fee, 0, '.', '.'),
            'guide_fee_idr' => 'IDR '.number_format($this->booking->idr_guide_fee, 0, '.', '.'),
            'susuk' => 'IDR '.number_format(($this->booking->susuk_guide+$this->booking->susuk_hbd), 0, '.', '.'),
            'susuk_idr' => 'IDR '.number_format($this->booking->idr_susuk_guide + $this->booking->idr_susuk_hbd, 0, '.', '.'),
            'cost' => 'IDR '.number_format($this->booking->tiket_total, 0, '.', '.'),
            'cost_idr' => 'IDR '.number_format($this->booking->idr_tiket_total, 0, '.', '.'),
            'additional' => 'IDR '.number_format($this->booking->additional_price, 0, '.', '.'),
            'additional_idr' => 'IDR '.number_format($this->booking->idr_additional_price, 0, '.', '.'),
            'note' => $this->booking->note_price,
            // 'all' => $this->booking,
        ];
    }
}
