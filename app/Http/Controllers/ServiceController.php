<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::select(
            'id',
            'first_name',
            'last_name',
            'name',
            'email',
            'phone',
            'role',
            'kyc_level',
            'wallet_balance',
            'created_at',
            'updated_at'
        )->orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }
}
