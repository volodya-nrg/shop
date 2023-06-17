<?php

class ControllerRecoverChecker extends ControllerBase
{
    public string $title = DicChangePassword;
    public string $description = "";

    public function index(array $args): Response
    {
        $response = new Response(ViewPageRecoverChecker);
        $hash = $_GET[FieldHash] ?? "";
        $email = "";

        if ($hash) {
            // найти е-мэйл по этому хешу
            $email = randomEmail();
        }

        $response->data[FieldEmail] = $email;

        if ($email != "" && !empty($_POST[FieldPassword]) && !empty($_POST[FieldPasswordConfirm])) {
            $tmpPassword = trim($_POST[FieldPassword]);
            $tmpPasswordConfirm = trim($_POST[FieldPasswordConfirm]);

            if (strlen($tmpPassword) < PassMinLen) {
                $response->data[FieldErrors][] = ErrPassIsShort;
            } else {
                if ($tmpPassword != $tmpPasswordConfirm) {
                    $response->data[FieldErrors][] = ErrPasswordsNotEqual;
                }
            }

            if (isset($response->data[FieldErrors]) && count($response->data[FieldErrors])) {
                $response->setHttpCode(400);
            } else {
                // удалить из базы данный хеш и сменить пароли конечно же
                $response->data[FieldSuccess] = DicPasswordChangedSuccessfully;
            }
        }

        return $response;
    }
}