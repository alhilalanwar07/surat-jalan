<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'kendaraans';
    protected $fillable = ['nomor_plat', 'tahun_pembuatan', 'nama_pemilik', 
                        'alamat_pemilik', 'no_telepon_pemilik', 'status_kir'];
}
