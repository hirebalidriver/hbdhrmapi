<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlists extends Model
{
    use HasFactory;

    protected $fillable = [
        "package_id",
        "tour_id",
        "time",
        "guest_id",
        "date",
        "adult",
        "child",
        "payment",
    ];

    public function option() {
        return $this->hasOne(Tours::class, 'id', 'tour_id');
    }

    public function tour() {
        return $this->hasOne(Packages::class, 'id', 'package_id');
    }
}
