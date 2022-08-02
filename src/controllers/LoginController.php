<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\LoginHandler;

class LoginController extends Controller {

    public function signin() {
        $flash = '';
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('login', [
            'flash' => $flash
        ]);
    }

    public function signinAction() {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');

        if ($email && $password) {
            $token = LoginHandler::verifyLogin($email, $password);
            if ($token) {
                $_SESSION['token'] = $token;
                $this->redirect('/');
            } else {
                $_SESSION['flash'] = 'E-mail ou senha inválidos';
                $this->redirect('/login');
            }
        } else {
            $_SESSION['flash'] = 'Preencha todos os campos corretamente!';
            $this->redirect('/login');
        }
    }

    public function signup() {
        echo '<h1>Cadastro</h1>';
    }

}
