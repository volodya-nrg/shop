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
            if ($err !== null) {
                $resp->setHttpCode($err->getCode());
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
            } else if ($result instanceof User && $result->hashForCheckEmail != "") {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrCheckYourEmail;
                return $resp;
            }

            $_SESSION["user"] = $result;

            redirect("/profile");
        }

        return $resp;
    }

    private function check_request(RequestLogin $req): ?Error
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