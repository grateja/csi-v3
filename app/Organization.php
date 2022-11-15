<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    public static function list() {
        return [
            'Greenlanders',
            'TODAG',
            'CAGRECA',
            'Parish Pastoral Council',
            'CTCC Personel',
            'ELS Personel',
        ];
    }
}
