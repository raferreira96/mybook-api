<?php

namespace App\Repositories\Interfaces;

interface LikeRepositoryInterface 
{
        public function countLike(int $idPost);
        public function isLiked(int $idPost, int $idUser);
}