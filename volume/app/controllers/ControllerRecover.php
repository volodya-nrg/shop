<?php declare(strict_types=1);

final class ControllerRecover extends ControllerBase
{
    public string $title = EnumDic::RecoverAccess->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        global $PDO;
        $resp = new MyResponse(EnumViewFile::PageRecover);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestRecover($_POST);
            $resp->data[EnumField::RequestedEmail->value] = $req->email;

            $err = $this->checkRequestIndex($req);
            if ($err instanceof Error) {
                $resp->code = 400;
                $resp->err = $err->getMessage();
                return $resp;
            }

            $serviceUsers = new ServiceUsers($PDO);
            $serviceRecover = new ServiceRecovers($PDO);
            $serviceEmail = new ServiceEmail(
                EMAIL_SMTP_SERVER,
                EMAIL_PORT,
                EMAIL_LOGIN,
                EMAIL_PASS,
                EMAIL_FROM,
                $_SERVER[EnumField::ModeIsProd->value],
            );

            // возьмем пользователя
            $result = $serviceUsers->oneByEmail($req->email);
            if ($result instanceof Error) {
                error_log($result->getMessage());
                $resp->code = 500;
                return $resp;
            } elseif ($result === null) {
                $resp->code = 400;
                $resp->err = EnumErr::NotFoundRow->value;
                return $resp;
            } elseif ($result instanceof UserRow && $result->email_hash !== null) {
                $resp->code = 400;
                $resp->err = EnumErr::CheckYourEmail->value;
                return $resp;
            }
            $user = $result;

            $recover = new RecoverRow();
            $recover->hash = randomString(32, true);
            $recover->user_id = $user->user_id;

            $serviceRecover->db->beginTransaction();

            $result = $serviceRecover->create($recover);
            if ($result instanceof Error) {
                $serviceRecover->db->rollBack();

                error_log($result->getMessage());
                $resp->code = 500;
                return $resp;
            }

            $template = $this->view(EnumViewFile::EmailMsgAndLink, "", [
                EnumField::Msg->value => EnumDic::GoAheadForRecoverPass->value,
                EnumField::Address->value => ADDRESS . "/recover/check?" . EnumField::Hash->value . "={$recover->hash}",
            ]);

            $result = $serviceEmail->send($user->email, EnumDic::RecoverAccess->value, $template);
            if ($result instanceof Error) {
                $serviceRecover->db->rollBack();

                error_log($result->getMessage());
                $resp->code = 500;
                return $resp;
            }

            $serviceRecover->db->commit();

            $resp->data = [];
            $resp->data[EnumField::DataSendMsg->value] = sprintf(EnumDic::RecoverDataSendMsgTpl->value, $req->email);
            $resp->data[EnumField::Hash->value] = $recover->hash; // нужен для отладки в тестах
        }

        return $resp;
    }

    public function check(array $args): MyResponse
    {
        global $PDO;
        $this->title = EnumDic::ChangePassword->value;
        $resp = new MyResponse(EnumViewFile::PageRecoverCheck);
        $hash = $_GET[EnumField::Hash->value] ?? "";
        $user = null;
        $serviceUsers = new ServiceUsers($PDO);
        $serviceRecover = new ServiceRecovers($PDO);

        // если прислали хэш, то найдем строку
        if ($hash) {
            $result = $serviceRecover->one($hash);
            if ($result instanceof Error) {
                error_log($result->getMessage());
                $resp->code = 500;
                return $resp;
            } elseif ($result instanceof RecoverRow) {
                $result = $serviceUsers->one($result->user_id);
                if ($result instanceof Error) {
                    error_log($result->getMessage());
                    $resp->code = 500;
                    return $resp;
                } elseif ($result === null) {
                    $resp->code = 400;
                    $resp->err = EnumErr::NotFoundRow->value;
                    return $resp;
                }

                $user = $result;
                $resp->data[EnumField::Email->value] = $user->email; // чтоб показалась форму смены пароля, необходимо предоставить e-mail
            }
        }

        if (($user !== null) && isset($_POST) && count($_POST)) {
            $req = new RequestRecoverCheck($_POST);

            $err = $this->checkRequestCheck($req);
            if ($err instanceof Error) {
                $resp->code = 400;
                $resp->err = $err->getMessage();
                return $resp;
            }

            $serviceUsers->db->beginTransaction();
            $user->pass = password_hash($req->pass, PASSWORD_DEFAULT);

            // обновим пользователя с новым паролем
            $result = $serviceUsers->update($user);
            if ($result instanceof Error) {
                $serviceUsers->db->rollBack();

                error_log($result->getMessage());
                $resp->code = 500;
                return $resp;
            }

            // удалим запись за ненадобностью
            $result = $serviceRecover->deleteByUserId($user->user_id);
            if ($result instanceof Error) {
                $serviceUsers->db->rollBack();

                error_log($result->getMessage());
                $resp->code = 500;
                return $resp;
            }

            $serviceUsers->db->commit();

            $resp->data = [];
            $resp->data[EnumField::Success->value] = EnumDic::PasswordChangedSuccessfully->value;
        }

        return $resp;
    }

    private function checkRequestIndex(RequestRecover $req): Error|null
    {
        if (!filter_var($req->email, FILTER_VALIDATE_EMAIL)) {
            return new Error(EnumErr::EmailNotCorrect->value);
        }

        return null;
    }

    private function checkRequestCheck(RequestRecoverCheck $req): Error|null
    {
        if (strlen($req->pass) < PassMinLen) {
            return new Error(sprintf(EnumErr::PassIsShortTpl->value, PassMinLen));
        }
        if ($req->pass !== $req->passConfirm) {
            return new Error(EnumErr::PasswordsNotEqual->value);
        }

        return null;
    }
}