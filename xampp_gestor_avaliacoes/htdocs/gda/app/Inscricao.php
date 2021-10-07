<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Inscricao extends Model {
    use Notifiable;

    protected $fillable = [
        'id_aluno', 'id_ucFuncionamento', 'id_avaliacao',
    ];
}