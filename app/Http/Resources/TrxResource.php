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
            'price' => 'IDR '.$price,
            'status' => $status
        ];
    }
}
