<?php

final class ControllerLogin extends ControllerBase
{
    public string $title = DicEnter;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        $resp = new MyResponse(ViewPageLogin);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestLogin($_POST);
            $resp->data[FieldRequestedEmail] = $req->email;

            $err = $this->check_request($req);
            if ($err instanceof Error) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            $serviceUsers = new ServiceUsers();

            // достанем пользователя
            $user = $serviceUsers->oneByEmail($req->email);
            if ($user instanceof Error) {
                $resp->setHttpCode(500);
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "oneByEmail", $user->getMessage()));
                return $resp;
            } else if ($user === null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrNotFoundUser;
                return $resp;
            } else if ($user instanceof UserRow && $user->email_hash !== null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrCheckYourEmail;
                return $resp;
            }

            // проверим по паролю
            if (!password_verify($req->pass, $user->pass)) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrLoginOrPasswordNotCorrect;
                return $resp;
            }

            $_SESSION[FieldProfile] = $user;
            $resp->data = [];

            if ($user->role === FieldAdmin) {
                $_SESSION[FieldAdmin] = true;
            }

            if (!$_SERVER[FieldModeIsTest]) {
                redirect("/profile");
            }
        }

        return $resp;
    }

    private function check_request(RequestLogin $req): Error|null
    {
        if (!filter_var($req->email, FILTER_VALIDATE_EMAIL)) {
            return new Error(ErrEmailNotCorrect);
        }
        if (strlen($req->pass) < PassMinLen) {
            return new Error(ErrPassIsShort);
        }

        return null;
    }
}