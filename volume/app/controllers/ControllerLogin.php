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

            // проверим пользователя
            $result = $serviceUsers->oneByEmail($req->getEmail());
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "oneByEmail", $result->getMessage()));
                return $resp;
            } else if ($result === null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrNotFoundUser;
                return $resp;
            } else if ($result instanceof User && $result->emailHash != "") {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrCheckYourEmail;
                return $resp;
            }

            $_SESSION[FieldProfile] = $result;
            $resp->data = [];

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