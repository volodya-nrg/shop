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

        if (isset($_POST) && count($_POST)) {
            $req = new RequestLogin($_POST);
            $resp->data[EnumField::RequestedEmail->value] = $req->email;

            $err = $this->check_request($req);
            if ($err instanceof Error) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = $err->getMessage();
                return $resp;
            }

            $serviceUsers = new ServiceUsers($PDO);

            // достанем пользователя
            $user = $serviceUsers->oneByEmail($req->email);
            if ($user instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "oneByEmail", $user->getMessage()));
                return $resp;
            } else if ($user === null) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = EnumErr::NotFoundUser->value;
                return $resp;
            } else if ($user instanceof UserRow && $user->email_hash !== null) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = EnumErr::CheckYourEmail->value;
                return $resp;
            }

            // проверим по паролю
            if (!password_verify($req->pass, $user->pass)) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = EnumErr::LoginOrPasswordNotCorrect->value;
                return $resp;
            }

            $_SESSION[EnumField::Profile->value] = $user;
            $resp->data = [];

            if ($user->role === EnumField::Admin->value) {
                $_SESSION[EnumField::Admin->value] = true;
            }

            if (!$_SERVER[EnumField::ModeIsTest->value]) {
                redirect("/profile");
            }
        }

        return $resp;
    }

    private function check_request(RequestLogin $req): Error|null
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