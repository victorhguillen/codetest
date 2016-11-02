<?php

namespace App\Political\Elections;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'elections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['pin_number', 'monitor_id', 'sub_monitor_id', 'country_id', 'user_id', 'region_id',
        'state_id', 'city_id', 'subcity_id', 'pollsite_id', 'precint_id', 'document_number',
        'total_votes', 'valid_votes', 'invalid_votes', 'affidavit_votes', 'county_id'];

    /**
     * This attributes are hidden in the collection.
     *
     * @var array
     *
     */
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Election country relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     *
     */
    public function country()
    {
        return $this->belongsTo('App\Political\Location\Countries\Country');
    }

    /**
     * Election region relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     *
     */
    public function region()
    {
        return $this->belongsTo('App\Political\Location\Regions\Region');
    }

    /**
     * Election state relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     *
     */
    public function state()
    {
        return $this->belongsTo('App\Political\Location\States\State');
    }

    /**
     * Election city relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     *
     */
    public function city()
    {
        return $this->belongsTo('App\Political\Location\Cities\City');
    }

    /**
     * Election subcity relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     *
     */
    public function subcity()
    {
        return $this->belongsTo('App\Political\Location\SubCities\SubCity');
    }

    /**
     * Election county relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     *
     */
    public function county()
    {
        return $this->belongsTo('App\Political\Location\Counties\County');
    }

    /**
     * Election pollsite relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     *
     */
    public function pollsite()
    {
        return $this->belongsTo('App\Political\PollSites\PollSite', 'pollsite_id');
    }

    /**
     * Election precint relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     *
     */
    public function precint()
    {
        return $this->belongsTo('App\Political\Precints\Precint');
    }

    /**
     * Election monitor relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     *
     */
    public function monitor()
    {
        return $this->belongsTo('App\Political\Monitors\Monitor');
    }

    /**
     * Election votes relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     *
     */
    public function votes()
    {
        return $this->hasMany('App\Political\Elections\Votes');
    }


}
