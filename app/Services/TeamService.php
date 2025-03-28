<?php

namespace App\Services;

use App\Models\Team;

class TeamService
{

    public function getTeams()
    {
        return Team::all();
    }
}
