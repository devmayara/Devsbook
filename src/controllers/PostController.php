<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;
use src\handlers\PostHandler;

class PostController extends Controller {

    private $loggerUser;

    public function __construct() {
        $this->loggerUser = LoginHandler::checkLogin();
        if (LoginHandler::checkLogin() === false) {
            $this->redirect('/login');
        }
    }

    public function new() {
        $body = filter_input(INPUT_POST, 'body');

        if ($body) {
            PostHandler::addPost(
                $this->loggerUser->id,
                'text',
                $body
            );
        }

        $this->redirect('/');
    }

}
