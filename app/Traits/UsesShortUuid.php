<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait UsesShortUuid {
    protected static function bootUsesUuid() {
        static::creating(function($model) {
            if(!$model->getKey()) {
                $model->{$model->getKeyName()} = Str::random(5);
            }
        });
    }

    public function getIncrementing() {
        return false;
    }

    public function getKeyType() {
        return 'string';
    }
}
