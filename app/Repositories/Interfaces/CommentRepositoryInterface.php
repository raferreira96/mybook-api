<?php

namespace App\Repositories\Interfaces;

interface CommentRepositoryInterface 
{
        public function getList(int $idPost);
}