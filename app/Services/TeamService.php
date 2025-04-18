<?php

namespace App\Services;

use App\Models\File;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TeamService
{

    public function createTeamPage()
    {
        $currentUser = Auth::user();
        $apiToken = request()->session()->get('api_token');

        if (!$apiToken) {
            $token = $currentUser->createToken('api_token');
            $apiToken = $token->plainTextToken;
            request()->session()->put('api_token', $apiToken);
        }
        $contacts = $currentUser->contacts()->with('contactUser')->get()->map(function ($contact) {
            return [
                'id' => $contact->id,
                'contact_id' => $contact->contact_id,
                'name' => $contact->contactUser->name,
                'email' => $contact->contactUser->email,
                'path' => null,
            ];
        });

        return Inertia::render('Team/Create', [
            'contacts' => $contacts,
            'apiToken' => $apiToken,
        ]);
    }

    public function storeTeam($formData)
    {
        DB::transaction(function () use ($formData) {
            $team = new Team();
            $team->name = $formData['name'];
            $team->description = $formData['description'];
            $team->creator_id = Auth::user()->id;
            $team->save();

            foreach ($formData['contacts'] as $usertId) {
                $team->users()->attach($usertId);
            }

            if ($formData['photo']) {
                $file = new File();
                $file->name = $formData['photo']->getClientOriginalName();
                $file->path = $formData['photo']->store('team_files');
                $team->files()->save($file);
            }

            return $team
                ? response()->json(['message' => 'Contact request sent'], 201)
                : response()->json(['message' => 'Contact already exists'], 400);
        });
    }
}
