<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface {


    public function store(array $data) {
        return User::create($data);
    }

    public function getList() {
    }

    public function getListSearch(array $data) {
        return User::where('name', 'like', '%'.$data['body'].'%')->get();
    }

    public function get(int $id) {
        return User::find($id);
    }

    public function update(array $data, int $id) {
        return User::find($id)->update($data);
    }

    public function destroy(int $id, int $loggedId) {

    }
}