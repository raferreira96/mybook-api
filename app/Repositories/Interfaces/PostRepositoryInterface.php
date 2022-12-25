<?php

namespace App\Repositories\Interfaces;

interface PostRepositoryInterface 
{
        public function store(array $data);
        public function getList(int $id, int $page, int $perPage);
        public function get(int $id);
        public function update(array $data, int $id);
        public function destroy(int $id, int $loggedId);
}