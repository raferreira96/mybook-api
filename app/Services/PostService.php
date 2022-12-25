<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use App\Repositories\LikeRepository;
use App\Repositories\PostRepository;
use App\Repositories\RelationRepository;
use App\Repositories\UserRepository;

class PostService {

    private $postRepository;
    private $userRepository;
    private $likeRepository;
    private $commentRepository;
    private $relationRepository;

    public function __construct(PostRepository $postRepository, UserRepository $userRepository, LikeRepository $likeRepository, CommentRepository $commentRepository, RelationRepository $relationRepository) {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->likeRepository = $likeRepository;
        $this->commentRepository = $commentRepository;
        $this->relationRepository = $relationRepository;
    }

    private function _postListToObject($postList, $loggedId) {
        foreach($postList as $postKey => $postItem) {
            if($postItem['id_user'] == $loggedId) {
                $postList[$postKey]['mine'] = true;
            } else {
                $postList[$postKey]['mine'] = false;
            }

            $userInfo = $this->userRepository->get($postItem['id_user']);
            $userInfo['avatar'] = asset('storage/'.$userInfo['avatar']);
            $userInfo['cover'] = asset('storage/'.$userInfo['cover']);
            $postList[$postKey]['user'] = $userInfo;

            $likes = $this->likeRepository->countLike($postItem['id']);
            $postList[$postKey]['like_count'] = $likes;

            $isLiked = $this->likeRepository->isLiked($postItem['id'], $loggedId);
            $postList[$postKey]['liked'] = ($isLiked > 0) ? true : false;

            $comments = $this->commentRepository->getList($postItem['id']);
            foreach($comments as $commentKey => $comment) {
                $user = $this->userRepository->get($comment['id_user']);
                $user['avatar'] = asset('storage/'.$user['avatar']);
                $user['cover'] = asset('storage/'.$user['cover']);
                $comments[$commentKey]['user'] = $user;
            }

            $postList[$postKey]['comments'] = $comments;

        }
        return $postList;
    }

    public function getUserPostList(array $data, int $id) {
        $user = $this->userRepository->get($id);

        if(!$user) {
            return false;
        }

        $page = intval($data['page']);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $postList = $this->postRepository->getList($id, $offset, $limit);
        $totalPosts = $this->postRepository->countUserPost($id);
        $pageCount = ceil($totalPosts / $limit);

        $posts = $this->_postListToObject($postList, $id);

        $array = [
            'current_page' => $page,
            'page_count' => $pageCount,
            'posts' => $posts
        ];

        return $array;
    }

    public function getUserPhotosList(array $data, int $id) {
        $user = $this->userRepository->get($id);

        if(!$user) {
            return false;
        }

        $page = intval($data['page']);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $photosList = $this->postRepository->getListPhotos($id, $offset, $limit);
        $totalPhotos = $this->postRepository->countUserPhotos($id);
        $pageCount = ceil($totalPhotos / $limit);

        $photos = $this->_postListToObject($photosList, $id);

        $array = [
            'current_page' => $page,
            'page_count' => $pageCount,
            'posts' => $photos
        ];

        return $array;
    }

    public function getFeed(array $data, int $idUser) {
        $page = intval($data['page']);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $users = [];
        $userList = $this->relationRepository->getListFollowersPosts($idUser);

        foreach($userList as $userItem) {
            $users[] = $userItem['user_to'];
        }
        $users[] = $idUser;

        $postList = $this->postRepository->getListFeedPosts($users, $offset, $limit);
        $totalPosts = $this->postRepository->countFeedPost($users);
        $pageCount = ceil($totalPosts / $limit);

        $posts = $this->_postListToObject($postList, $idUser);

        $array = [
            'current_page' => $page,
            'page_count' => $pageCount,
            'posts' => $posts
        ];

        return $array;
    }

    public function createPost(array $data, int $idUser) {
        if(!$data['type']) {
            return false;
        }
        
        switch($data['type']) {
            case 'text':
                if(!array_key_exists('body', $data)) {
                    return false;
                }

                if(!$data['body']) {
                    return false;
                }

                break;
            case 'photo':
                if(!array_key_exists('photo', $data)) {
                    return false;
                }

                $file = $data['photo'];
                $file = $file->store('public');
                $file = explode('public/', $file);
                $data['body'] = $file[1];
                break;
        }

        $data['id_user'] = $idUser;
        $data['created_at'] = date('Y-m-d H:i:s');

        $createPost = $this->postRepository->store($data);

        return true;
    }

    public function likePost(int $idUser, int $id) {
        $post = $this->postRepository->get($id);
        if(!$post) {
            return false;
        }

        $isLiked = $this->likeRepository->isLiked($id, $idUser);
        if(!$isLiked) {
            $data = [
                'id_post' => $id,
                'id_user' => $idUser,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $likePost = $this->likeRepository->store($data);
            $array['is_liked'] = true;
        } else {
            $postLiked = $this->likeRepository->get($id, $idUser);
            $this->likeRepository->destroy($postLiked['id']);
            $array['is_liked'] = false;
        }
        $array['like_count'] = $this->likeRepository->countLike($id);

        return $array;
    }

    public function createComment(int $idUser, int $id, array $data) {
        $post = $this->postRepository->get($id);

        if(!$post) {
            return false;
        }

        $data = [
            'id_post' => $id,
            'id_user' => $idUser,
            'created_at' => date('Y-m-d H:i:s'),
            'body' => $data['body']
        ];

        $createComment = $this->commentRepository->store($data);

        return true;
    }
}