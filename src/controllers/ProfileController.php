<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;
use \src\handlers\PostHandler;

class ProfileController extends Controller {

    private $loggerUser;

    public function __construct() {
        $this->loggerUser = LoginHandler::checkLogin();
        if (LoginHandler::checkLogin() === false) {
            $this->redirect('/login');
        }
    }

    public function index($atts = []) {


        $this->render('profile', [
            'loggedUser' => $this->loggerUser,
        ]);
    }

}
