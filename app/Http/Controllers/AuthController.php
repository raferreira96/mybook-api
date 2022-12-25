<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function unauthorized(): JsonResponse {
        return response()->json(['message' => 'Acesso não autorizado.'], 401);
    }

    public function register(Request $request): JsonResponse {
        $responseArray = ['message' => ''];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100|unique:users,email',
            'name' => 'required|max:100',
            'birthdate' => 'required|date_format:Y-m-d',
            'password' => 'required',
            'password_confirm' => 'required|same:password'
        ]);

        if($validator->fails()) {
            $responseArray['message'] = $validator->errors()->first();
            return response()->json($responseArray, 400);
        }

        $validatedData = $validator->safe()->only([
            'email',
            'name',
            'password',
            'birthdate'
        ]);

        $register = $this->userService->registerUser($validatedData);
        
        if(!$register) {
            $responseArray['message'] = 'Ocorreu um erro.';
            return response()->json($responseArray, 500);
        }

        $responseArray['message'] = 'OK';
        return response()->json($responseArray, 201);
    }

    public function login(Request $request): JsonResponse {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            $responseArray['message'] = $validator->errors()->first();
            return response()->json($responseArray, 400);
        }

        $validatedData = $validator->safe()->only([
            'email',
            'password'
        ]);

        $token = $this->userService->loginUser($validatedData);

        if(!$token) {
            $responseArray['message'] = 'Usuário e/ou senha incorretos.';
            return response()->json($responseArray, 400);
        }

        $responseArray['token'] = $token;
        return response()->json($responseArray, 200);
    }

    public function refresh() {
        $token = auth()->refresh();
        return response()->json(['token' => $token], 200);
    }

    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'OK'], 200);
    }
}
