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
        $id = $this->loggerUser->id;

        if (!empty($atts['id'])) {
            $id = $atts['id'];
        }

        $user = UserHandler::getUser($id);

        if (!$user) {
            $this->redirect('/');
        }

        $this->render('profile', [
            'loggedUser' => $this->loggerUser,
            'user' => $user,
        ]);
    }

}
