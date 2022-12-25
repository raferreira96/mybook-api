<?php

namespace App\Repositories;

use App\Models\Like;
use App\Repositories\Interfaces\LikeRepositoryInterface;

class LikeRepository implements LikeRepositoryInterface {
    public function store(array $data) {
        return Like::create($data);
    }

    public function countLike(int $idPost) {
        return Like::where('id_post', $idPost)->count();
    }

    public function isLiked(int $idPost, int $idUser) {
        return Like::where('id_post', $idPost)->where('id_user', $idUser)->count();
    }

    public function get(int $idPost, int $idUser) {
        return Like::where('id_post', $idPost)->where('id_user', $idUser)->first();
    }

    public function destroy(int $idPost) {
        return Like::find($idPost)->delete();
    }
}