<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\AutoSynch;
use App\Traits\UsesSynch;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutSourceJobOrderLinen extends Model
{
    use SoftDeletes, UsesUuid, UsesSynch;

    protected $fillable = [
        'out_source_job_order_id',
        'out_source_linen_id',
        'category',
        'name',
        'degree_of_soil',
        'unit_price',
        'quantity'
    ];

    public function getNameAttribute() {
        if($this->with_stain) {
            return "{$this->attributes['name']}(with stain)";
        }
        return $this->attributes['name'];
    }

    public function getDegreeOfSoilAttribute() {
        return [
            'regular_price' => 'Normal',
            'with_stain_light' => 'Lightly soiled',
            'with_stain_medium' => 'Medium soiled',
            'with_stain_heavy' => 'Heavily soiled'
        ][$this->attributes['degree_of_soil']];
    }
}
