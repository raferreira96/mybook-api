<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $loggedUser;
    private $userService;

    public function __construct(UserService $userService) {
        $this->loggedUser = auth()->user();
        $this->userService = $userService;
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'email|max:100|unique:users,email',
            'name' => 'max:100',
            'birthdate' => 'date_format:Y-m-d',
            'city' => 'max:100',
            'work' => 'max:100',
            'password' => 'max:100',
            'password_confirm' => 'max:100|same:password'
        ]);

        if($validator->fails()) {
            $responseArray['message'] = $validator->errors()->first();
            return response()->json($responseArray, 400);
        }

        $validatedData = $validator->safe()->only([
            'email',
            'name',
            'birthdate',
            'city',
            'work',
            'password',
            'password_confirm'
        ]);

        $update = $this->userService->updateUser($validatedData, $this->loggedUser['id']);

        if(!$update) {
            $responseArray['message'] = 'Usuário não encontrado.';
            return response()->json($responseArray, 404);
        }

        return response()->json(['message' => 'OK'], 201);
    }

    public function avatar(Request $request) {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|file|mimes:jpg,png'
        ]);

        if($validator->fails()) {
            $responseArray['message'] = $validator->errors()->first();
            return response()->json($responseArray, 400);
        }

        $updateAvatar = $this->userService->updateUserAvatar($request, $this->loggedUser['id']);

        if(!$updateAvatar) {
            $responseArray['message'] = 'Usuário não encontrado.';
            return response()->json($responseArray, 404);
        }

        return response()->json(['message' => 'OK'], 201);

    }

    public function cover(Request $request) {
        $validator = Validator::make($request->all(), [
            'cover' => 'required|file|mimes:jpg,png'
        ]);

        if($validator->fails()) {
            $responseArray['message'] = $validator->errors()->first();
            return response()->json($responseArray, 400);
        }

        $updateCover = $this->userService->updateUserCover($request, $this->loggedUser['id']);

        if(!$updateCover) {
            $responseArray['message'] = 'Usuário não encontrado.';
            return response()->json($responseArray, 404);
        }

        return response()->json(['message' => 'OK'], 201);
    }

    public function read($id = false) {
        if($id === false) {
            $id = $this->loggedUser;
        }

        $infoUser = $this->userService->getUserInfo($id, $this->loggedUser);

        if(!$infoUser) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        return response()->json($infoUser, 200);
    }

    public function follow($id) {
        if($id == $this->loggedUser['id']) {
            return response()->json(['message' => 'Você não pode seguir a si mesmo.'], 400);
        }

        $follow = $this->userService->followUser($this->loggedUser['id'], $id);

        if(!$follow) {
            return response()->json(['message' => 'Ocorreu um erro.'], 500);
        }

        return response()->json(['message' => 'OK'], 200);
    }

    public function followers($id = false) {
        if($id == false) {
            $id = $this->loggedUser['id'];
        }

        $data = $this->userService->getFollowers($id);

        if(!$data) {
            return response()->json(['message' => 'Ocorreu um erro.'], 500);
        }

        return response()->json($data, 200);
    }
}
