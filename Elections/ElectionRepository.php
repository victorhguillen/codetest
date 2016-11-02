<?php

namespace App\Political\Elections;

use App\Political\Location\Cities\City;
use App\Political\Location\States\State;
use App\Political\Location\SubCities\SubCity;
use App\Political\Parties\PartyRepository;
use App\Political\Pins\Pin;
use App\Political\PollSites\PollSite;
use App\Political\Precints\Precint;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

/**
 * @property Election election
 * @property \Illuminate\Contracts\Auth\Authenticatable|null user
 */
class ElectionRepository
{
    /**
     * ElectionRepository constructor.
     *
     * @param Election|State $election
     * @param State $state
     * @param City $city
     * @param SubCity $subcity
     * @param PollSite $pollsite
     * @param Precint $precint
     * @param Votes $votes
     * @param Pin $pin
     * @param PartyRepository $party
     */
    public function __construct(
        Election $election,
        State $state,
        City $city,
        SubCity $subcity,
        PollSite $pollsite,
        Precint $precint,
        Votes $votes,
        Pin $pin,
        PartyRepository $party
    )
    {
        $this->election = $election;
        $this->party = $party;
        $this->votes = $votes;
        $this->pin = $pin;
        $this->state = $state;
        $this->city = $city;
        $this->subcity = $subcity;
        $this->pollsite = $pollsite;
        $this->precint = $precint;
    }

    /**
     * Find the Election Selected
     *
     * @param $id
     * @return Response
     * @internal param $data
     */
    public function find($id)
    {
        return $this->election->find($id);
    }


    /**
     * Edit the Election Selected
     *
     * @param $id
     * @return Response
     * @internal param $data
     */
    public function edit($id)
    {
        return $this->election->where('id','=',$id)
            ->with('pin.pollsite.subcity.city.state.region.country')
            ->first();
    }


    /**
     * Get Election By Pin Selected
     *
     * @param $pin
     * @return Response
     * @internal param $data
     */
    public function getPin($pin)
    {
        return $this->pin->where('pin_number', '=', $pin)
            ->with('precint', 'pollsite', 'subcity', 'city', 'region', 'state', 'country')
            ->first();
    }

    /**
     * Find the State Selected
     *
     * @param $precint_id
     * @return Response
     * @internal param $data
     */
    public function precint($precint_id)
    {
        return $this->election->where('precint_id','=',$precint_id)->select('id')->first();
    }

    /**
     * Find the State Selected
     *
     * @return Response
     * @internal param $data
     */
    public function filterCountry()
    {
        $parties = $this->party->all();
        $select = ['states.name AS location',
            DB::raw('COALESCE(SUM(e.valid_votes), 0) as valid'),
            DB::raw('COALESCE(SUM(e.invalid_votes), 0) as invalid'),
            DB::raw('COALESCE(SUM(e.affidavit_votes), 0) as affidavit'),
            DB::raw('COALESCE(COUNT(e.precint_id), 0) as reported'),
            DB::raw('(SELECT COALESCE(SUM(precints.qty_voters)) FROM precints
                WHERE precints.state_id = states.id) AS voters'),
            DB::raw('(SELECT COALESCE(COUNT(*), 0) FROM precints
                WHERE precints.state_id = states.id) AS polling_stations'),
        ];

        foreach($parties as $party){
            $select[] = DB::raw('(SELECT COALESCE(SUM(pv.total), 0) FROM party_votes AS pv
                        WHERE pv.state_id = states.id and
                        pv.party_id = '.$party->id.')
                        AS '.str_slug($party->name, '_'));
        }
        return $this->state
            ->leftJoin('elections AS e', 'states.id', '=', 'e.state_id')
            ->select($select)
            ->groupBy('states.id', 'states.name', 'e.state_id')
            ->get();
    }

    /**
     * Find the State Selected
     *
     * @param $id
     * @return Response
     * @internal param $data
     */
    public function filterState($id)
    {
        $parties = $this->party->all();
        $select = ['cities.name AS location',
            DB::raw('COALESCE(SUM(e.valid_votes), 0) as valid'),
            DB::raw('COALESCE(SUM(e.invalid_votes), 0) as invalid'),
            DB::raw('COALESCE(SUM(e.affidavit_votes), 0) as affidavit'),
            DB::raw('COALESCE(COUNT(e.precint_id), 0) as reported'),
            DB::raw('(SELECT COALESCE(SUM(precints.qty_voters), 0) FROM precints
                WHERE precints.city_id = cities.id) AS voters'),
            DB::raw('(SELECT COALESCE(COUNT(*), 0) FROM precints
                WHERE precints.city_id = cities.id) AS polling_stations'),
        ];

        foreach($parties as $party){
            $select[] = DB::raw('(SELECT COALESCE(sum(pv.total), 0) FROM party_votes AS pv
                        WHERE pv.city_id = cities.id and
                        pv.party_id = '.$party->id.')
                        AS '.str_slug($party->name, '_'));
        }
        return $this->city->where('cities.state_id', '=', $id)
            ->leftJoin('elections AS e', 'cities.id', '=', 'e.city_id')
            ->select($select)
            ->groupBy('cities.id', 'cities.name', 'e.city_id')
            ->get();
    }

    /**
     * Find the State Selected
     *
     * @param $id
     * @return Response
     * @internal param $data
     */
    public function filterCity($id)
    {
        $parties = $this->party->all();
        $select = ['subcities.name AS location',
            DB::raw('COALESCE(SUM(e.valid_votes), 0) as valid'),
            DB::raw('COALESCE(SUM(e.invalid_votes), 0) as invalid'),
            DB::raw('COALESCE(SUM(e.affidavit_votes), 0) as affidavit'),
            DB::raw('COALESCE(COUNT(e.precint_id), 0) as reported'),
            DB::raw('(SELECT COALESCE(SUM(precints.qty_voters), 0) FROM precints
                WHERE precints.subcity_id = subcities.id) AS voters'),
            DB::raw('(SELECT COALESCE(COUNT(*), 0) FROM precints
                WHERE precints.subcity_id = subcities.id) AS polling_stations'),
        ];

        foreach($parties as $party){
            $select[] = DB::raw('(SELECT COALESCE(sum(pv.total), 0) FROM party_votes AS pv
                        WHERE pv.subcity_id = subcities.id and
                        pv.party_id = '.$party->id.')
                        AS '.str_slug($party->name, '_'));
        }
        return $this->subcity->where('subcities.city_id', '=', $id)
            ->leftJoin('elections AS e', 'subcities.id', '=', 'e.subcity_id')
            ->select($select)
            ->groupBy('subcities.id', 'subcities.name', 'e.subcity_id')
            ->get();
    }

    public function filterSubCity($id)
    {
        $parties = $this->party->all();
        $select = ['poll_sites.name AS location',
            DB::raw('COALESCE(SUM(e.valid_votes), 0) as valid'),
            DB::raw('COALESCE(SUM(e.invalid_votes), 0) as invalid'),
            DB::raw('COALESCE(SUM(e.affidavit_votes), 0) as affidavit'),
            DB::raw('COALESCE(COUNT(e.precint_id), 0) as reported'),
            DB::raw('(SELECT COALESCE(SUM(precints.qty_voters), 0) FROM precints
                WHERE precints.pollsite_id = poll_sites.id) AS voters'),
            DB::raw('(SELECT COALESCE(COUNT(*), 0) FROM precints
                WHERE precints.pollsite_id = poll_sites.id) AS polling_stations'),
        ];

        foreach($parties as $party){
            $select[] = DB::raw('(SELECT COALESCE(sum(pv.total), 0) FROM party_votes AS pv
                        WHERE pv.pollsite_id = poll_sites.id and
                        pv.party_id = '.$party->id.')
                        AS '.str_slug($party->name, '_'));
        }
        return $this->pollsite->where('poll_sites.subcity_id', '=', $id)
            ->leftJoin('elections AS e', 'poll_sites.id', '=', 'e.pollsite_id')
            ->select($select)
            ->groupBy('poll_sites.id', 'poll_sites.name', 'e.pollsite_id')
            ->get();
    }

    public function filterPollSite($id, $precint=null)
    {
        $parties = $this->party->all();
        $select = ['precints.name AS location',
            DB::raw('COALESCE(SUM(e.valid_votes), 0) AS valid'),
            DB::raw('COALESCE(SUM(e.invalid_votes), 0) AS invalid'),
            DB::raw('COALESCE(SUM(e.affidavit_votes), 0) as affidavit'),
            DB::raw('COALESCE(COUNT(e.precint_id), 0) AS reported'),
            DB::raw('COALESCE(SUM(precints.qty_voters), 0) AS voters'),
            DB::raw('COALESCE(COUNT(precints.id), 0) AS polling_stations'),
        ];

        foreach($parties as $party){
            $select[] = DB::raw('(SELECT COALESCE(sum(pv.total), 0) FROM party_votes AS pv
                        WHERE pv.precint_id = precints.id and
                        pv.party_id = '.$party->id.')
                        AS '.str_slug($party->name, '_'));
        }
        $result = [];

        if ($precint != null) $result = $this->precint->where('precints.id', '=', $precint);
        else $result = $this->precint->where('precints.pollsite_id', '=', $id);

        return $result->leftJoin('elections AS e' ,'precints.id', '=', 'e.precint_id')
            ->select($select)
            ->groupBy('precints.id', 'precints.name', 'e.precint_id')
            ->get();
    }

    /**
     * Find the Election Selected
     * @return Response
     * @internal param $data
     */
    public function votesByParties()
    {
        $select = [
            'pp.id AS party_id',
            'pp.name AS party_name',
            'pp.candidate AS candidate',
            DB::raw('sum(party_votes.total) AS party_votes'),
            DB::raw('(sum(party_votes.total)/(SELECT sum(e.valid_votes) FROM elections AS e))*100 AS percent')
        ];

        return $this->votes->where('party_votes.total', '>', 0)
            ->leftJoin('political_parties AS pp', 'pp.id', '=', 'party_votes.party_id')
            ->select($select)
            ->groupBy('party_name', 'candidate', 'pp.id')
            ->orderBy('party_votes', 'desc')->take(5)
            ->get();
    }

    /**
     * Find the Election Selected
     * @return Response
     * @internal param $data
     */
    public function totalValidVotes()
    {
        return $this->election->sum('valid_votes');
    }

    /**
     * Find the Election Selected
     * @return Response
     * @internal param $data
     */
    public function totalInvalidVotes()
    {
        return $this->election->sum('invalid_votes');
    }

    /**
     * Find the Election Selected
     * @return Response
     * @internal param $data
     */
    public function partiesReport()
    {
        $select = [
            'pp.id AS party_id',
            'pp.name AS party_name',
            'pp.candidate AS candidate',
            DB::raw('(SELECT COUNT(e.id) FROM elections AS e) AS ballots_count'),
            DB::raw('sum(party_votes.total) AS total_votes'),
        ];

        return $this->votes->where('party_votes.total', '>', 0)
            ->leftJoin('political_parties AS pp', 'pp.id', '=', 'party_votes.party_id')
            ->select($select)
            ->groupBy('party_name', 'candidate', 'pp.id')
            ->orderBy('total_votes', 'desc')
            ->get();
    }

    /**
     * Get all the Elections
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all()
    {
        return $this->election->all();
    }

    /**
     * Find the All Cities By State
     *
     * @param $state_id
     * @return Response
     * @internal param $data
     */
    public function getStateCities($state_id)
    {
        return $this->state->find($state_id)->city;;
    }


    /**
     * Create a new state
     *
     * @param $request
     * @return Election
     * @internal param $data
     */
    public function create($request)
    {
        $results = $this->election->create($this->getDataArray($request));
        $this->savePartyVotes($request, $results->id);
        return $results;
    }

    /**
     * Create a new state
     *
     * @param $results
     * @param $election
     * @return Response
     * @internal param $data
     */
    public function savePartyVotes($results, $election)
    {
        $parties = $this->party->all();
        $getParties = $results->get('party');

        foreach ($getParties as $key => $total) {
            $this->votes->create([
                'total' => $total,
                'party_id' => $parties[$key]->id,
                'election_id' => $election,
                'city_id'  =>  $results->get('city'),
                'state_id'  =>  $results->get('state'),
                'country_id'  => 1,
                'subcity_id'  =>  $results->get('subcity'),
                'county_id'  =>  null,
                'region_id'  =>  $results->get('region'),
                'pollsite_id'  =>  $results->get('pollsite'),
                'precint_id'  =>  $results->get('precint'),
            ]);
        }
    }

    /**
     * Update Election info
     *
     * @param $id
     * @param $request
     * @return Response
     */
    public function update($id, $request)
    {
        $election = $this->election->find($id);
        $election->update($this->getDataArray($request));
    }

    /**
     * get data from request
     *
     * @param $request
     * @return array
     * @internal param $data
     */
    public function getDataArray($request)
    {

        return [
            'total_votes' => $request->get('total_votes'),
            'valid_votes' => $request->get('valid_votes'),
            'invalid_votes' => $request->get('invalid_votes'),
            'affidavit_votes' => $request->get('affidavit_votes'),
            'city_id' => $request->get('city'),
            'state_id' => $request->get('state'),
            'country_id' => 1,
            'subcity_id' => $request->get('subcity'),
            'county_id' => null,
            'region_id' => $request->get('region'),
            'pollsite_id' => $request->get('pollsite'),
            'precint_id' => $request->get('precint'),
            'document_number' => $request->get('document_number'),
            'user_id' => auth()->user()->id,
            'pin_number' => $request->get('pin_number')
        ];

    }

    /**
     * Remove from table
     *
     * @param $id
     * @return Response
     */
    public function remove($id)
    {
        $election = $this->election->find($id);
        $election->delete();
    }

}
