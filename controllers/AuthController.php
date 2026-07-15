<?php
// controllers/AuthController.php
require_once 'models/UserModel.php';

class AuthController {
    private $model;

    public function __construct($db_connection) {
        $this->model = new UserModel($db_connection);
    }

    public function login() {
        // Redirect jika sudah login
        if (isset($_SESSION['id_user'])) {
            header("Location: kasir");
            exit();
        }

        $error_message = "";

        if (isset($_POST['login'])) {
            $username = $_POST['username'];
            $password = hash('sha256', $_POST['password']);

            $user = $this->model->cekLogin($username, $password);

            if ($user) {
                $_SESSION['id_user']  = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
                header("Location: kasir");
                exit();
            } else {
                $error_message = "Username atau Password salah!";
            }
        }

        // Panggil View Login
        require 'views/login.php';
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: login");
        exit();
    }
}
?>