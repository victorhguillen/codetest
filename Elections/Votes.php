<?php

namespace App\Political\Elections;

use Illuminate\Database\Eloquent\Model;

class Votes extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'party_votes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['total', 'party_id', 'election_id', 'country_id', 'region_id',
        'state_id', 'city_id', 'subcity_id', 'pollsite_id', 'precint_id', 'county_id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     *
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Votes Party relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     *
     */
    public function party()
    {
        return $this->belongsTo('App\Political\Parties\PoliticalParty');
    }
}
