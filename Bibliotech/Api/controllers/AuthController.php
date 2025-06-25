<?php
// controllers/AuthController.php

include_once __DIR__ . 'C:\laragon\www\Bibliotech\Api\controllers\User.php';

class AuthController {
    private $user;

    public function __construct($db) {
        $this->user = new User($db);
    }

    public function login($correo, $password) {
        $this->user->correo = $correo;
        $this->user->password = $password;

        if ($this->user->login()) {
            return ["estado" => "ok", "mensaje" => "Autenticación satisfactoria"];
        } else {
            return ["estado" => "error", "mensaje" => "Error en la autenticación"];
        }
    }
}
?>
