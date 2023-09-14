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

            $err = $this->check_request($req);
            if ($err instanceof Error) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            $serviceUsers = new ServiceUsers();

            // достанем пользователя
            $user = $serviceUsers->oneByEmail($req->getEmail());
            if ($user instanceof Error) {
                $resp->setHttpCode(500);
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "oneByEmail", $user->getMessage()));
                return $resp;
            } else if ($user === null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrNotFoundUser;
                return $resp;
            } else if ($user instanceof User && $user->emailHash != "") {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrCheckYourEmail;
                return $resp;
            }

            // проверим по паролю
            if (!password_verify($req->getPass(), $user->pass)) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrLoginOrPasswordNotCorrect;
                return $resp;
            }

            $_SESSION[FieldProfile] = $user;
            $resp->data = [];

            if ($user->role === "admin") {
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
        if (!filter_var($req->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return new Error(ErrEmailNotCorrect);
        }
        if (strlen($req->getPass()) < PassMinLen) {
            return new Error(ErrPassIsShort);
        }

        return null;
    }
}