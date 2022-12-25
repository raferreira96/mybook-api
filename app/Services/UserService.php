<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\PostRepository;
use App\Repositories\RelationRepository;
use DateTime;
use Illuminate\Http\Request;

class UserService {

    private $userRepository;
    private $relationRepository;
    private $postRepository;

    public function __construct(UserRepository $repository, RelationRepository $relationRepository, PostRepository $postRepository) {
        $this->userRepository = $repository;
        $this->relationRepository = $relationRepository;
        $this->postRepository = $postRepository;
    }

    public function registerUser(array $data): bool {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $createdUser = $this->userRepository->store($data);

        if(!$createdUser) {
            return false;
        }

        return true;
    }

    public function loginUser(array $data): string | bool {
        $token = auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password']
        ]);

        if(!$token) {
            return false;
        }
        
        return $token;
    }

    public function updateUser(array $data, int $id) {
        $user = $this->userRepository->get($id);

        if(!$user) {
            return false;
        }

        if(array_key_exists('password', $data)) {
            $hashPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['password'] = $hashPassword;
        }

        $updatedUser = $this->userRepository->update($data, $id);

        return true;
    }

    public function updateUserAvatar(Request $request, int $id) {
        $user = $this->userRepository->get($id);

        if(!$user) {
            return false;
        }

        $file = $request->file('avatar')->store('public');
        $file = explode('public/', $file);
        $photoName = $file[1];

        $data = [
            'avatar' => $photoName
        ];

        $updateAvatar = $this->userRepository->update($data, $id);

        return true;
    }

    public function updateUserCover(Request $request, int $id) {
        $user = $this->userRepository->get($id);

        if(!$user) {
            return false;
        }

        $file = $request->file('cover')->store('public');
        $file = explode('public/', $file);
        $photoName = $file[1];

        $data = ['cover' => $photoName];

        $updateCover = $this->userRepository->update($data, $id);

        return true;
    }

    public function getUserInfo($id, $loggedUser) {
        if(is_numeric($id)) {
            $userInfo = $this->userRepository->get($id);
            if(!$userInfo) {
                return false;
            }
        } else {
            $userInfo = $id;
        }

        $userInfo['avatar'] = asset('storage/'.$userInfo['avatar']);
        $userInfo['cover'] = asset('storage/'.$userInfo['cover']);
        
        $userInfo['me'] = ($userInfo['id'] == $loggedUser['id']) ? true : false;

        $dateFrom = new DateTime($userInfo['birthdate']);
        $dateTo = new DateTime('today');
        $userInfo['age'] = $dateFrom->diff($dateTo)->y;

        $userInfo['followers'] = $this->relationRepository->countFollowers($userInfo['id']);
        $userInfo['following'] = $this->relationRepository->countFollowing($userInfo['id']);

        $userInfo['photoCount'] = $this->postRepository->countUserPhotos($userInfo['id']);

        $hasRelation = $this->relationRepository->hasRelation($loggedUser['id'], $userInfo['id']);
        $userInfo['isFollowing'] = ($hasRelation > 0) ? true : false;

        return $userInfo;
    }

    public function followUser(int $idUser, int $id) {
        $user = $this->userRepository->get($id);
        if(!$user) {
            return false;
        }

        $relation = $this->relationRepository->getLastRelation($idUser, $id);
        if($relation) {
            $this->relationRepository->destroy($relation->id);
            return response()->json(['message' => 'OK'], 200);
        }

        $follow = [
            'user_from' => $idUser,
            'user_to' => $id
        ];

        $this->relationRepository->store($follow);

        return true;
    }

    public function getFollowers(int $id) {
        $user = $this->userRepository->get($id);
        if(!$user) {
            return false;
        }

        $followers = $this->relationRepository->getListFollowers($id);
        $following = $this->relationRepository->getListFollowing($id);

        $data['followers'] = [];
        $data['following'] = [];

        foreach($followers as $item) {
            $user = $this->userRepository->get($item['user_from']);
            $data['followers'][] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'avatar' => asset('storage/'.$user['avatar'])
            ];
        }

        foreach($following as $item) {
            $user = $this->userRepository->get($item['user_to']);
            $data['followers'][] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'avatar' => asset('storage/'.$user['avatar'])
            ];
        }

        return $data;
    }

    public function searchUser(array $data) {
        $userList = $this->userRepository->getListSearch($data);
        $array = ['users' => []];

        foreach($userList as $userItem) {
            $array['users'][] = [
                'id' => $userItem['id'],
                'name' => $userItem['name'],
                'avatar' => asset('storage/'.$userItem['avatar'])
            ];
        }

        return $array;
    }
}