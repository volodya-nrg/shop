<?php
declare(strict_types=1);

final class ControllerLogin extends ControllerBase
{
    public string $title = EnumDic::Enter->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        global $PDO;
        $resp = new MyResponse(EnumViewFile::PageLogin);
        $serviceUsers = new ServiceUsers($PDO);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestLogin($_POST);
            $resp->data[EnumField::RequestedEmail->value] = $req->email;

            $err = $this->checkRequest($req);
            if ($err instanceof Error) {
                $resp->code = 400;
                $resp->err = $err->getMessage();
                return $resp;
            }

            // достанем пользователя
            $result = $serviceUsers->oneByEmail($req->email);
            if ($result instanceof Error) {
                error_log($result->getMessage());
                $resp->code = 500;
                return $resp;
            } else if ($result === null) {
                $resp->code = 400;
                $resp->err = EnumErr::NotFoundRow->value;
                return $resp;
            } else if ($result instanceof UserRow && $result->email_hash !== null) {
                $resp->code = 400;
                $resp->err = EnumErr::CheckYourEmail->value;
                return $resp;
            }

            $user = $result;

            // проверим по паролю
            if (!password_verify($req->pass, $user->pass)) {
                $resp->code = 400;
                $resp->err = EnumErr::LoginOrPasswordNotCorrect->value;
                return $resp;
            }

            $_SESSION[EnumField::Profile->value] = $user;
            $resp->data = [];

            if ($user->role === EnumField::Admin->value) {
                $_SESSION[EnumField::Admin->value] = true;
            }

            if ($_SERVER[EnumField::ModeIsProd->value]) {
                $this->redirect("/profile");
            }
        }

        return $resp;
    }

    private function checkRequest(RequestLogin $req): Error|null
    {
        if (!filter_var($req->email, FILTER_VALIDATE_EMAIL)) {
            return new Error(EnumErr::EmailNotCorrect->value);
        }
        if (strlen($req->pass) < PassMinLen) {
            return new Error(sprintf(EnumErr::PassIsShortTpl->value, PassMinLen));
        }

        return null;
    }
}