<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use src\handlers\PostHandler;

class PostController extends Controller
{

    private $loggerUser;

    public function __construct()
    {
        $this->loggerUser = UserHandler::checkLogin();
        if (UserHandler::checkLogin() === false) {
            $this->redirect('/login');
        }
    }

    public function new()
    {
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

    public function delete($atts = [])
    {
        if (!empty($atts['id'])) {
            $id = $atts['id'];

            PostHandler::delete(
                $id, 
                $this->loggerUser->id
            );
        }

        $this->redirect('/');
    }
}
