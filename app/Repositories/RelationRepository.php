<?php

namespace App\Repositories;

use App\Models\Relation;
use App\Repositories\Interfaces\RelationRepositoryInterface;

class RelationRepository implements RelationRepositoryInterface {
    public function store(array $data) {
        return Relation::create($data);
    }

    public function getListFollowersPosts(int $idUser) {
        return Relation::where('user_from', $idUser)->get();
    }

    public function getListFollowers(int $id) {
        return Relation::where('user_to', $id)->get();
    }

    public function getListFollowing(int $id) {
        return Relation::where('user_from', $id)->get();
    }

    public function countFollowers(int $id) {
        return Relation::where('user_to', $id)->count();
    }

    public function countFollowing(int $id) {
        return Relation::where('user_from', $id)->count();
    }

    public function hasRelation(int $idUser, int $id) {
        return Relation::where('user_from', $idUser)->where('user_to', $id)->count();
    }

    public function getLastRelation(int $idUser, int $id) {
        return Relation::where('user_from', $idUser)->where('user_to', $id)->first();
    }

    public function destroy(int $id) {
        return Relation::find($id)->delete();
    }
}