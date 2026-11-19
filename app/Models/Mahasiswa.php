<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use IIluminate\Database\Eloquent\Relations\HasMany;


class Mahasiswa extends User{

public function prestasis() : HasMany
    {
        return $this->hasMany(Prestasi::class);
    }

    protected $table = 'mahasiswas';
    protected $fillable = ['nim', 'nama', 'email']; 
    protected $hidden = ['password'];
}