<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Calendario extends Model {
    use Notifiable;

    protected $fillable = [
        'nome_aval_input', 'data_aval_input', 'epoca_aval_input', 'uc_aval_input'
    ];
}