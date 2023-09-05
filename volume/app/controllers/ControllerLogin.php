<?php

final class ControllerLogin extends ControllerBase
{
    public string $title = DicEnter;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        $resp = new MyResponse(ViewPageLogin);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestLogin();
            $req->parsePOST($_POST);

            $resp->data[FieldRequestedEmail] = $req->getEmail();

            $err = $this->check($req);
            if ($err !== null) {
                $resp->setHttpCode($err->getCode());
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            // подключение к БД и тд
            // редирекст на страницу профиля
        }

        return $resp;
    }

    private function check(RequestLogin $req): ?Error
    {
        if (!filter_var($req->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return new Error(ErrEmailNotCorrect, 400);
        }
        if (strlen($req->getPass()) < PassMinLen) {
            return new Error(ErrPassIsShort, 400);
        }

        return null;
    }
}