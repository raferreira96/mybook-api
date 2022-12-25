<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedController extends Controller
{
    private $loggedUser;
    private $postService;

    public function __construct(PostService $postService) {
        $this->loggedUser = auth()->user();
        $this->postService = $postService;
    }

    public function feed(Request $request, $id = false) {
        if($id == false) {
            $id = $this->loggedUser['id'];
        }

        $validator = Validator::make($request->all(), [
            'page' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $validatedData = $validator->safe()->only(['page']);

        $posts = $this->postService->getUserPostList($validatedData, $id);

        if(!$posts) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        return response()->json($posts, 200);
    }

    public function photos(Request $request, $id = false) {
        if($id === false) {
            $id = $this->loggedUser['id'];
        }

        $validator = Validator::make($request->all(), [
            'page' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $validatedData = $validator->safe()->only(['page']);

        $photos = $this->postService->getUserPhotosList($validatedData, $id);

        if(!$photos) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        return response()->json($photos, 200);
    }

    public function read(Request $request) {
        $validator = Validator::make($request->all(), [
            'page' => 'required|numeric'
        ]);

        if($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $validatedData = $validator->safe()->only(['page']);

        $posts = $this->postService->getFeed($validatedData, $this->loggedUser['id']);

        if(!$posts) {
            return response()->json(['message' => 'Ocorreu um erro.'], 500);
        }

        return response()->json($posts, 200);
    }

    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'body' => 'required_if:type,==,text',
            'photo' => 'required_if:type,==,photo|file|mimes:jpg,png'
        ]);

        if($validator->fails()) {
            $responseArray['message'] = $validator->errors()->first();
            return response()->json($responseArray, 500);
        }

        $validatedData = $validator->safe()->only(['type','body','photo']);

        $createdPost = $this->postService->createPost($validatedData, $this->loggedUser['id']);

        return response()->json(['message' => 'OK'], 201);
    }
}
