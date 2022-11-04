<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageRelations extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'tour_id',
    ];

    public function tour() {
        return $this->hasOne(Tours::class, 'id', 'tour_id');
    }
}
