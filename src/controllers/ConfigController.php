<?php

namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;

class ConfigController extends Controller
{

    private $loggerUser;

    public function __construct()
    {
        $this->loggerUser = UserHandler::checkLogin();
        if (UserHandler::checkLogin() === false) {
            $this->redirect('/login');
        }
    }

    public function index()
    {
        $users = UserHandler::getUser( $this->loggerUser->id);

        $flash = '';
        if (!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }

        $this->render('config', [
            'loggedUser' => $this->loggerUser,
            'users' => $users,
            'flash' => $flash
        ]);
    }

    public function save()
    {
        $name = filter_input(INPUT_POST, 'name');
        $birthdate = filter_input(INPUT_POST, 'birthdate');
        $email = filter_input(INPUT_POST, 'email');
        $city = filter_input(INPUT_POST, 'city');
        $work = filter_input(INPUT_POST, 'work');
        $password = filter_input(INPUT_POST, 'password');
        $passwordConfirm = filter_input(INPUT_POST, 'passwordConfirm');

        if ($name && $email) {
            $updateFields = [];

            $user = UserHandler::getUser( $this->loggerUser->id);

            // E-MAIL
            if ( $user->email != $email ) {
                if (UserHandler::emailExists($email)) {
                    $updateFields['email'] = $email;
                } else {
                    $_SESSION['flash'] = 'E-mail já cadastrado!';
                    $this->redirect('/config');
                }
            }

            // BIRTHDATE
            $birthdate = explode('/', $birthdate);
            if ( count($birthdate) != 3 ) {
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $this->redirect('/config');
            }
            $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];
            if ( strtotime($birthdate) === false ) {
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $this->redirect('/config');
            }
            $updateFields['birthdate'] = $birthdate;

            // PASSWORD
            if (!empty($password)) {
                if ($password === $passwordConfirm) {
                    $updateFields['password'] = $password;
                } else {
                    $_SESSION['flash'] = 'As senhas não conferem!';
                    $this->redirect('/config');
                }
            }

            // CAMPOS NORMAIS
            $updateFields['name'] = $name;
            $updateFields['city'] = $city;
            $updateFields['work'] = $work;

            UserHandler::updateUser( $this->loggerUser->id, $updateFields);

            // AVATAR
            if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['tmp_name'])) {
                $newAvatar = $_FILES['avatar'];

                if (in_array($newAvatar['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
                    $avatarName = $this->cutImage($newAvatar, 200, 200, 'media/avatars');
                    $updateFields['avatar'] = $avatarName;
                }
            }
            

            // COVER
            if (isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])) {
                $newCover = $_FILES['cover'];

                if (in_array($newCover['type'], ['image/jpeg', 'image/jpg', 'image/png'])) {
                    $coverName = $this->cutImage($newCover, 850, 310, 'media/covers');
                    $updateFields['cover'] = $coverName;
                }
            }

            UserHandler::updateUser( $this->loggerUser->id, $updateFields);            
        }

        $this->redirect('/config');
    }

    private function cutImage($file, $w, $h, $folder)
    {
        list($widthOrig, $heightOrig) = getimagesize($file['tmp_name']);
        $ratio = $widthOrig / $heightOrig;

        $newWidth = $w;
        $newHeight = $newWidth / $ratio;

        if ($newHeight < $h) {
            $newHeight = $h;
            $newWidth = $newHeight * $ratio;
        }

        $x = $w - $newWidth;
        $y = $h - $newHeight;
        $x = $x < 0 ? $x / 2 : $x;
        $y = $y < 0 ? $y / 2 : $y;

        $finalImage = imagecreatetruecolor($w, $h);
        switch ($file['type']) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($file['tmp_name']);
            break;
            case 'image/png':
                $image = imagecreatefrompng($file['tmp_name']);
            break;
        }

        imagecopyresampled(
            $finalImage, $image,
            $x, $y, 0, 0,
            $newWidth, $newHeight, $widthOrig, $heightOrig
        );

        $fileName = md5(time().rand(0,9999)).'.jpg';

        imagejpeg($finalImage, $folder.'/'.$fileName);

        return $fileName;
    }

}
