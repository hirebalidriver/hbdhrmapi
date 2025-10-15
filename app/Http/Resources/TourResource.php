<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'itinerary' => $this->itinerary,
            'note' => $this->note,
            'status' => $this->status,
            'price_tour' => $this->price_tour,
            'price_guide' => $this->price_guide,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'inclusions' => $this->inclusions,
            'exclusions' => $this->exclusions,
            'guide_fee' => $this->guide_fee,
            'description' => $this->description,
            'discount_name' => $this->discount_name,
            'discount' => $this->discount,
            'idr_guide_fee' => $this->idr_guide_fee,
            'prices' => $this->whenLoaded('prices')->map(function ($price) {
                return [
                    'id' => $price->id,
                    'tour_id' => $price->tour_id,
                    'people' => $price->people,
                    'type' => $price->type,
                    'price' => $price->price,
                    'is_active' => $price->is_active,
                    'created_at' => $price->created_at,
                    'updated_at' => $price->updated_at,
                    'people_end' => $price->people_end,
                    'idr_price' => $price->idr_price
                ];
            }),
            'times' => $this->whenLoaded('times'),
        ];
    }
}
