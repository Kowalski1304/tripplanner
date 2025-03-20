<?php

use App\Models\Team;
use App\Services\TeamService;

test('example', function () {
    expect(true)->toBeTrue();
});

test('get all teams collection', function () {
    Team::factory()->count(3)->create();
    $service = new TeamService();
    $result = $service->getTeams();
    expect($result)->count();
});
