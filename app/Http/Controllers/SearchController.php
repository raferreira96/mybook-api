<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SearchController extends Controller
{
    private $loggedUser;
    private $userService;

    public function __construct(UserService $userService) {
        $this->loggedUser = auth()->user();
        $this->userService = $userService;
    }

    public function search(Request $request) {
        $validator = Validator::make($request->all(), [
            'body' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $validatedData = $validator->safe()->only(['body']);

        $search = $this->userService->searchUser($validatedData);

        return response()->json($search, 200);
    }
}
