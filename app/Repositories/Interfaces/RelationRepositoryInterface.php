<?php

namespace App\Repositories\Interfaces;

interface RelationRepositoryInterface 
{
        public function countFollowers(int $id);
        public function countFollowing(int $id);
        public function hasRelation(int $user, int $id);
}