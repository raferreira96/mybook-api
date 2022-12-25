<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;

class CommentRepository implements CommentRepositoryInterface {
    public function getList(int $idPost) {
        return Comment::where('id_post', $idPost)->get();
    }

    public function store(array $data) {
        return Comment::create($data);
    }
}