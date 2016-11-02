<?php

namespace App\Political\Elections;

use App\Http\Controllers\Controller;
use App\Political\Location\Countries\Country;
use App\Political\Parties\PartyRepository;
use App\Political\Precints\Precint;
use Illuminate\Http\Response;

class ElectionController extends Controller
{

    private $country;
    private $precint;
    private $party;
    private $election;

    /**
     * Create a new controller instance.
     *
     * @param ElectionRepository $election
     * @param Country $country
     * @param Precint $precint
     * @param PartyRepository $party
     */
    public function __construct(
        ElectionRepository $election,
        Country $country,
        Precint $precint,
        PartyRepository $party
    )
    {
        $this->election = $election;
        $this->party = $party;
        $this->country = $country;
        $this->precint = $precint;
        $this->middleware('auth')->except('filter');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $parties = $this->party->all();
        return view('election_results.create', compact(['parties']));
    }

    /**
     * Show the general results view
     *
     * @return Response
     */
    public function index()
    {
        list($votes, $election, $elections, $precint, $voters) = $this->results();
        $parties = $this->party->all();
        return view('election_results.general_results', compact([
            'votes',
            'election',
            'elections',
            'precint',
            'voters',
            'parties'
        ]));
    }

    /**
     * Filter results by the options
     * @return Response Json
     * @internal param \Illuminate\Http\Request $request
     */
    public function filter()
    {
        $result = [];

        if (request()->has('precint')) {
            $result = $this->election->filterPollSite(null, request('precint'));
        } else if (request()->has('pollsite')) {
            $result = $this->election->filterPollSite(request('pollsite'));
        } else if (request()->has('subcity')) {
            $result = $this->election->filterSubCity(request('subcity'));
        } else if (request()->has('city')) {
            $result = $this->election->filterCity(request('city'));
        } else if (request()->has('state')) {
            $result = $this->election->filterState(request('state'));
        } else {
            $result = $this->election->filterCountry(1);
        }

        $data = [
            'draw' => 0,
            'recordsTotal' => count($result),
            'recordsFiltered' => count($result),
            'data' => $result
        ];

        return response()->json($data);
    }

    /**
     * Elections Results Data
     *
     * @return array
     */
    public function results()
    {
        $votes = $this->election->votesByParties();
        $election = $this->election->all();
        $precint = $this->precint->all();

        $precint['total'] = $precint->count();
        $precint['computed'] = $election->groupBy('precint_id')->count();
        $precint['missing'] = $precint['total'] - $precint['computed'];

        $voters['suscribed'] = $precint->sum('qty_voters');
        $voters['voted'] = $election->sum('total_votes');
        $voters['to_vote'] = $voters['suscribed'] - $voters['voted'];

        $elections['emited'] = $election->sum('total_votes');
        $elections['voted'] = $election->sum('valid_votes');
        $elections['invalid'] = $election->sum('invalid_votes');
        $elections['affidavit'] = $election->sum('affidavit_votes');
        $elections['voted_percent'] =
            ($elections['emited'] === 0) ? 0 :
                number_format($elections['voted'] / $elections['emited'] * 100, 2);
        $elections['invalid_percent'] =
            ($elections['emited'] === 0) ? 0 :
                number_format($elections['invalid'] / $elections['emited'] * 100, 2);
        $elections['affidavit_percent'] =
            ($elections['emited'] === 0) ? 0 :
                number_format($elections['affidavit'] / $elections['emited'] * 100, 2);

        return [$votes, $election, $elections, $precint, $voters];

    }

    /**
     * Results for the TOP 5 Parties
     *
     * @return Response
     */
    public function partiesReport()
    {
        $mainReport = $this->election->partiesReport();
        $votes = $this->election->votesByParties();
        $totalValidVotes = $this->election->totalValidVotes();
        $totalInvalidVotes = $this->election->totalInvalidVotes();

        return view('election_results.parties_report', compact([
            'mainReport',
            'votes',
            'totalValidVotes',
            'totalInvalidVotes'
        ]));
    }

    /**
     * Store a newly created resource in the db.
     *
     * @param ElectionRequest|\Illuminate\Http\Request $request
     * @return Response
     */
    public function store(ElectionRequest $request)
    {
        $this->election->create($request);

        return redirect()->route('elections.create')->withSuccess([trans('electionResults.createdMsg')]);
    }

    /**
     * Show the form for update a election.
     *
     * @param $id
     * @return Response
     */
    public function edit($id)
    {
        $countries = $this->country->all();
        $election = $this->election->edit($id);
        return view('election.edit', compact(['election', 'countries']));
    }

    /**
     * Show a single election.
     *
     * @param $id
     * @return Response
     */
    public function single($id)
    {
        $election = $this->election->find(decrypt($id));
        $parties = $election->votes;

        return view('election_results.single', compact(['election', 'parties']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ElectionRequest|\Illuminate\Http\Request $request
     * @param  int $id
     * @return Response
     */
    public function update(ElectionRequest $request, $id)
    {
        $this->election->update($id, $request);
        return redirect()->back()->withSuccess([trans('electionResults.updatedMsg')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     *
     */
    public function destroy($id)
    {
        $this->election->remove($id);
        return redirect()->route('elections.index')->withSuccess([trans('electionResults.deletedMsg')]);
    }

}
