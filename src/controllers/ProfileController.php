<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class ProfileController extends Controller
{

    private $loggerUser;

    public function __construct()
    {
        $this->loggerUser = UserHandler::checkLogin();
        if (UserHandler::checkLogin() === false) {
            $this->redirect('/login');
        }
    }

    public function index($atts = [])
    {
        $page = intval(filter_input(INPUT_GET, 'page'));

        // Detectando o usuário acessado
        $id = $this->loggerUser->id;
        if (!empty($atts['id'])) {
            $id = $atts['id'];
        }

        // Pegando informações do usuário
        $user = UserHandler::getUser($id, true);
        if (!$user) {
            $this->redirect('/');
        }

    
        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        // Pegando o feed do usuário
        $feed = PostHandler::getUserFeed(
            $id, 
            $page, 
            $this->loggerUser->id
        );

        // Verificar se eu sigo esse usuário
        $isFollowing = false;
        if ($user->id != $this->loggerUser->id) {
            $isFollowing = UserHandler::isFollowing(
                $this->loggerUser->id, 
                $user->id
            );
        } else {
            $isFollowing = false;
        }

        $this->render('profile', [
            'loggedUser' => $this->loggerUser,
            'user' => $user,
            'feed' => $feed,
            'isFollowing' => $isFollowing
        ]);
    }

    public static function follow($atts) 
    {
        $to = intval($atts['id']);

        if (UserHandler::idExists($to)) {
            if (UserHandler::isFollowing($this->loggerUser->id, $to)) {
                // Deixar de seguir
                UserHandler::unfollow($this->loggerUser->id, $to);
            } else {
                // Seguir
                UserHandler::follow($this->loggerUser->id, $to);
            }
        }

        $this->redirect('/perfil/' . $to);
    }

}
