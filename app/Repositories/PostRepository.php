<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface {
    public function store(array $data){
        return Post::create($data);
    }
    public function countUserPost(int $id){
        return Post::where('id_user', $id)->count();
    }
    public function getList(int $id, int $offset, int $limit){
        return Post::where('id_user', $id)->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();
    }
    public function getListFeedPosts(array $users, int $offset, int $limit) {
        return Post::whereIn('id_user', $users)->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();
    }
    public function countFeedPost(array $users) {
        return Post::whereIn('id_user', $users)->count();
    }
    public function countUserPhotos(int $id) {
        return Post::where('id_user', $id)->where('type', 'photo')->count();
    }
    public function getListPhotos(int $id, int $offset, int $limit) {
        return Post::where('id_user', $id)->where('type', 'photo')->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();
    }
    public function get(int $id){
        return Post::find($id);
    }
    public function update(array $data, int $id){}
    public function destroy(int $id, int $loggedId){}
}