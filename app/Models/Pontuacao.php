<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pontuacao extends Model
{
    use HasFactory;

    protected $table = 'pontuacoes';

    protected $guarded = [];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function __toString()
    {
        return $this->velocidade . ' / ' . $this->precisao . '%';
    }
}
