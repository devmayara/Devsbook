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

        $id = $this->loggerUser->id;

        if (!empty($atts['id'])) {
            $id = $atts['id'];
        }

        $user = UserHandler::getUser($id, true);

        if (!$user) {
            $this->redirect('/');
        }

    
        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        $feed = PostHandler::getUserFeed(
            $id, 
            $page, 
            $this->loggerUser->id
        );

        $this->render('profile', [
            'loggedUser' => $this->loggerUser,
            'user' => $user,
            'feed' => $feed
        ]);
    }

}
