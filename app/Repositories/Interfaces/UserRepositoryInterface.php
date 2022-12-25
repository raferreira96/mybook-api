<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface 
{
        public function store(array $data);
        public function getList();
        public function get(int $id);
        public function update(array $data, int $id);
        public function destroy(int $id, int $loggedId);
}