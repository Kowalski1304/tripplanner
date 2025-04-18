<?php

namespace App\Http\Controllers;

use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct(private readonly TeamService $teamService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function createTeamPage()
    {
        return $this->teamService->createTeamPage();
    }

    public function storeTeam(Request $request)
    {
        return $this->teamService->storeTeam($request->all());
    }
}
