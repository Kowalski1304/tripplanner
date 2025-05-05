<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return $this->userService->getAllUsersPage();
    }

    public function show(User $user): Response
    {
        return $this->userService->show($user);
    }
}
