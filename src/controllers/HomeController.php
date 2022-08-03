<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;
use \src\handlers\PostHandler;

class HomeController extends Controller {

    private $loggerUser;

    public function __construct() {
        $this->loggerUser = LoginHandler::checkLogin();
        if (LoginHandler::checkLogin() === false) {
            $this->redirect('/login');
        }
    }

    public function index() {
        $feed = PostHandler::getHomeFeed(
            $this->loggerUser->id
        );

        $this->render('home', [
            'loggedUser' => $this->loggerUser,
            'feed' => $feed
        ]);
    }

}
