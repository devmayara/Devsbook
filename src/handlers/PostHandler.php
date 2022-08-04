<?php

namespace src\handlers;

use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;

class PostHandler {

    public static function addPost($idUser, $type, $body) {
        $body = trim($body);

        if (!empty($idUser) && !empty($body)) {

            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();
        }
    }

    public static function _postListToObject($postList, $loggedUserId) {
        $posts = [];
        foreach ($postList as $postItem) {
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->body = $postItem['body'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->mine = false;

            if ($postItem['id_user'] === $loggedUserId) {
                $newPost->mine = true;
            }

            // preencher as informações adicionais no post
            $newUser = User::select()
                ->where('id', $postItem['id_user'])
            ->one();

            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];

            // preencher infomações de LIKES
            $newPost->likeCount = 0;
            $newPost->liked = false;

            // preencher infomações de COMENTÁRIOS
            $newPost->comments = [];

            $posts[] = $newPost;
        }

        return $posts;
    }

    public static function getUserFeed($idUser, $page, $loggedUserId) {
        $perPage = 2;

        // pegar os posts ordenando pela data
        $postsList = Post::select()
            ->where('id_user', $idUser)
            ->orderBy('created_at', 'desc')
            ->page($page, $perPage)
        ->get();
        
        $total = Post::select()
            ->where('id_user', $idUser)
        ->count();
        $pageCount = ceil($total / $perPage);

        // transformar o resultado em objeto dos models
        $posts = self::_postListToObject($postsList, $loggedUserId);

        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'currentPage' => $page
        ];
    }

    public static function getHomeFeed($idUser, $page) {
        $perPage = 2;

        // pegar lista de usuários que EU sigo
        $userList = UserRelation::select()
            ->where('user_from', $idUser)
        ->get();

        $users = [];
        foreach ($userList as $userItem) {
            $users[] = $userItem('user_to');
        }
        $users[] = $idUser;

        // pegar os posts ordenando pela data
        $postsList = Post::select()
            ->where('id_user', 'in', $users)
            ->orderBy('created_at', 'desc')
            ->page($page, $perPage)
        ->get();
        
        $total = Post::select()
            ->where('id_user', 'in', $users)
        ->count();
        $pageCount = ceil($total / $perPage);

        // transformar o resultado em objeto dos models
        $posts = self::_postListToObject($postsList, $idUser);

        // retornar o resultado
        return [
            'posts' => $posts,
            'pageCount' => $pageCount,
            'currentPage' => $page
        ];
    }

    public static function getPhotosFrom($idUser) {
        $photosData = Post::select()
            ->where('id_user', $idUser)
            ->where('type', 'photo')
        ->get();

        $photos = [];

        foreach ($photosData as $photo) {
            $newPost = new Post();
            $newPost->id = $photo['id'];
            $newPost->type = $photo['type'];
            $newPost->body = $photo['body'];
            $newPost->created_at = $photo['created_at'];

            $photos[] = $newPost;
        }

        return $photos;
    }

}
