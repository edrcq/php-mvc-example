<?php

date_default_timezone_set('Europe/Paris');

require_once('controller/conversationController.php');
require_once('controller/friendController.php');
require_once('controller/loginController.php');

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'login':
            if (!empty($_POST)) {
                login($_POST);
            } else {
                loginPage();
            }
            break;

        case 'register':
            if (!empty($_POST)) {
                register($_POST);
            } else {
                registerPage();
            }
            break;

        case 'validate':
            validateAccount($_GET);
            break;

        case 'logout':
            logout();
            break;

        case 'conversation':
            conversationPage();
            break;

        case 'friend':
            friendPage();
            break;
    }
} else {
    $user_id = $_SESSION['user_id'] ?? false;

    if ($user_id) {
        friendPage();
    } else {
        if (isset($_GET['page'])) {
            switch($_GET['page']) {
                case 'login':
                    loginPage();
                    break;
                case 'register':
                    registerPage();
                    break;
                // TODO: Add a contact page
                // case 'contact':
                //     contactPage();
                //     break;
                default:
                    loginPage();
                    break;
            }
        } else {
            loginPage();
        }
    }
}
