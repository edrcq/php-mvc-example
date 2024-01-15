<?php

session_start();

require_once('model/user.php');

/****************************
 * ----- LOAD LOGIN PAGE -----
 ****************************/

function loginPage()
{
    $user = new stdClass();
    $user->id = $_SESSION['user_id'] ?? false;
    require('view/loginView.php');
}

/***************************
 * ----- LOGIN FUNCTION -----
 ***************************/

function login($post)
{
    if (empty($post['email']) || empty($post['password']))  {
        $error_msg = "Certains champs sont vides";
        require('view/loginView.php');
        return;
    }
    $email = $post['email'];
    $password = hash('sha256', $post['password']);
    $user_data = User::getUserByCredentials($email, $password);

    if ($user_data == null) {
        $error_msg = "Email ou mot de passe incorrect";
        require('view/loginView.php');
        return;
    }

    // Set session
    $_SESSION['user_id'] = $user_data['id'];
    $user_id = $_SESSION['user_id'] ?? false;
    header('location: index.php ');
}

/****************************
 * ----- LOGOUT FUNCTION -----
 ****************************/

function logout()
{
    $_SESSION = array();
    session_destroy();

    header('location: index.php');
}

/****************************
 * ----- LOAD REGISTER PAGE -----
 ****************************/

function registerPage()
{
    $user = new stdClass();
    $user->id = $_SESSION['user_id'] ?? false;
    require('view/registerView.php');
}

function register($post) {
    if (    empty($post['email'])
        ||  empty($post['username'])
        ||  empty($post['password'])
        ||  empty($post['cpassword'])
    )  {
        $error_msg = "Certains champs sont vides";
        require('view/registerView.php');
        return;
    }
    if (filter_var($post['email'], FILTER_VALIDATE_EMAIL) === false) {
        $error_msg = "Adresse email invalide";
        require('view/registerView.php');
        return;
    }
    $email =    $post['email'];
    $username = $post['username'];
    $password = $post['password'];
    $cpassword = $post['cpassword'];
    if ($password !== $cpassword) {
        $error_msg = "Les mots de passe ne correspondent pas.";
        require('view/registerView.php');
        return;
    }
    $already_exists = User::getUserByEmail($email);
    if ($already_exists) {
        $error_msg = "Impossible de s'inscire";
        require('view/registerView.php');
        return;
    }
    $user = new User();
    $user->setEmail($email);
    $user->setUsername(User::generateUsername($username));
    $user->setPassword($password);
    $token = User::createUser($user);
    $success_msg = 'Ton compte est créé. <a href="/index.php?action=validate&token='.$token.'">Valide le en cliquant ici.</a>';
    require('view/registerView.php');
}

function validateAccount($get) {
    if (empty($get['token'])) {
        $error_msg = "Jeton invalide. Impossible de vérifier ton email";
        require('view/registerView.php');
        return;
    }

    $count = User::verifyEmailToken($get['token']);

    if ($count > 0) {
        $success_msg = "Votre compte est validé.";
        require('view/registerView.php');
        return;
    } else {
        $error_msg = "Jeton invalide. Impossible de vérifier ton email";
        require('view/registerView.php');
        return;
    }
}
