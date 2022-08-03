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

    public static function getHomeFeed($idUser) {
        // pegar lista de usuários que EU sigo
        $userList = UserRelation::select()
            ->where('user_from', $idUser)
            ->get();
        $users = [];
        foreach ($userList as $userItem) {
            $users[] = $userItem('user_to');
        }
        $users[] = $idUser;

        // pegar os posts do user que sigo pela data

        // transformar o resultado em objeto dos models

        // preencher as informações adicionais no post

        // retornar o resultado
    }

}
