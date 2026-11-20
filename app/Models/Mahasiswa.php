<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswas';
    protected $fillable = ['nim', 'nama', 'email'];
    protected $hidden = ['password'];

    public function prestasis(): BelongsToMany
    {
        return $this->belongsToMany(Prestasi::class, 'mahasiswa_prestasi');
    }
}