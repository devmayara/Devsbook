<?php

namespace src\controllers;

use \core\Controller;

class LoginController extends Controller {

    public function signin() {
        $this->render('login');
    }

    public function signinAction() {
        echo '<h1>login - recebido</h1>';

    }

    public function signup() {
        echo '<h1>Cadastro</h1>';
    }

}
