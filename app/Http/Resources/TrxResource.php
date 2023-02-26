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
            'collect' => 'USD '.number_format($this->booking->collect, 0, '.', '.').' / '.'IDR '.number_format($this->booking->collect*15000, 0, '.', '.'),
            'susuk' => 'IDR '.number_format(($this->booking->susuk_guide+$this->booking->susuk_hbd), 0, '.', '.'),
            'cost' => 'IDR '.number_format($this->booking->tiket_total, 0, '.', '.'),
            'additional' => 'IDR '.number_format($this->booking->additional_price, 0, '.', '.'),
            'totalGuideFee' => $this->booking->guide_fee,
            'totalSusuk' => $this->booking->susuk_guide+$this->booking->susuk_hbd,
            'totalCost' => $this->booking->tiket_total,
            'totalAdditional' => $this->booking->additional_price,
            'status' => $status
        ];
    }
}
