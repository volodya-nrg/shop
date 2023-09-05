<?php

class ControllerRecoverChecker extends ControllerBase
{
    public string $title = DicChangePassword;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        $resp = new MyResponse(ViewPageRecoverChecker);
        $hash = $_GET[FieldHash] ?? "";
        $email = "";

        if ($hash) {
            // найти е-мэйл по этому хешу
            $email = randomEmail();
            $resp->data[FieldEmail] = $email; // чтоб показалась форма смены пароля, необходимо предоставить e-mail
        }

        if ($email && isset($_POST) && count($_POST)) {
            $req = new RequestRecoverChecker();
            $req->parsePOST($_POST);

            $err = $this->check($req);
            if ($err !== null) {
                $resp->setHttpCode($err->getCode());
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            // подключение к БД и тд
            // удалить из базы данный хеш и сменить пароли конечно же
            $resp->data[FieldSuccess] = DicPasswordChangedSuccessfully;
        }

        return $resp;
    }

    private function check(RequestRecoverChecker $req): ?Error
    {
        if (strlen($req->getPass()) < PassMinLen) {
            return new Error(ErrPassIsShort, 400);
        }
        if ($req->getPass() != $req->getPassConfirm()) {
            return new Error(ErrPasswordsNotEqual, 400);
        }

        return null;
    }
}