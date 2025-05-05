<?php

namespace App\Http\Controllers;

use App\Events\ContactEvent;
use App\Models\User;
use App\Services\ContactService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class WebSocketController extends Controller
{
    public function __construct(private readonly ContactService $contactService)
    {
    }

    public function handleMessage(Request $request)
    {
        $data = $request->json()->all();
        $type = $data['type'] ?? null;
        $userId = $data['userId'] ?? null;
        $channel = $data['channel'] ?? null;

        if (!$type || !$userId || !$channel) {
            return response()->json(['error' => 'Invalid message format'], 400);
        }

        $currentUser = Auth::user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        switch ($type) {
            case 'add_contact':
                $this->contactService->addContact($currentUser, $targetUser);
                Broadcast::event(new ContactEvent('add_contact', $userId, true));
                break;

            case 'remove_contact':
                $this->contactService->removeContact($currentUser, $targetUser);
                Broadcast::event(new ContactEvent('remove_contact', $userId, false));
                break;

            case 'subscribe':
                // Handle subscription if needed
                break;

            default:
                return response()->json(['error' => 'Unknown message type'], 400);
        }

        return response()->json(['status' => 'success']);
    }
} 