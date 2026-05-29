<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiarioPost extends Model
{
    protected $fillable = [
        'obra_id',
        'etapa_obra_id',
        'user_id',
        'texto',
        'foto_path',
        'data_postagem',
    ];

    protected $casts = [
        'data_postagem' => 'datetime',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function etapa()
    {
        return $this->belongsTo(EtapaObra::class, 'etapa_obra_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
