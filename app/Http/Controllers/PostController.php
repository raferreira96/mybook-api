<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    private $loggedUser;
    private $postService;

    public function __construct(PostService $postService) {
        $this->loggedUser = auth()->user();
        $this->postService = $postService;
    }

    public function like($id) {
        if(!$id) {
            return response()->json(['message' => 'Post inválido'], 404);
        }

        $likes = $this->postService->likePost($this->loggedUser['id'], $id);

        if(!$likes) {
            return response()->json(['message' => 'Post não encontrado'], 404);
        }

        return response()->json($likes, 200);
    }

    public function comment(Request $request, $id) {
        if(!$id) {
            return response()->json(['message' => 'Post inválido'], 404);
        }

        $validator = Validator::make($request->all(), [
            'body' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $validatedData = $validator->safe()->only(['body']);

        $createComment = $this->postService->createComment($this->loggedUser['id'], $id, $validatedData);
    }
}
