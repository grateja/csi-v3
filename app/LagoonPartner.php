<?php

namespace App;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class LagoonPartner extends Model
{
    use UsesUuid;
    
    protected $fillable = [
        'id',
        'shop_name',
        'address',
    ];

    public function customers() {
        return $this->belongsToMany('App\Customer', 'lagoon_partner_customers', 'lagoon_partner_id', 'customer_id');
    }

    public function transactions() {
        return $this->belongsToMany('App\Transaction', 'lagoon_partner_transactions', 'lagoon_partner_id', 'transaction_id');
    }
}
