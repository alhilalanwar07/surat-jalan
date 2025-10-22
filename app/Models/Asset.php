<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project',
        'kode_lot',
        'kode_cat',
        'initial_location',
        'area',
        'item_category',
        'kode',
        'item_name',
        'merk',
        'qty',
        'uom',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];
}
