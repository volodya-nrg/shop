<?php

final class ControllerRecover extends ControllerBase
{
    public string $title = DicRecoverAccess;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        $resp = new MyResponse(ViewPageRecover);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestRecover($_POST);
            $resp->data[FieldRequestedEmail] = $req->email;

            $err = $this->check_request_index($req);
            if ($err instanceof Error) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            $serviceUsers = new ServiceUsers();
            $serviceRecover = new ServiceRecovers();
            $serviceEmail = new ServiceEmail(
                EMAIL_SMTP_SERVER,
                EMAIL_PORT,
                EMAIL_LOGIN,
                EMAIL_PASS,
                EMAIL_FROM,
                $_SERVER[FieldModeIsTest] === true,
            );

            // возьмем пользователя
            $result = $serviceUsers->oneByEmail($req->email);
            if ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->oneByEmail", $result->getMessage()));
                return $resp;
            } elseif ($result === null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrNotFoundUser;
                return $resp;
            } elseif ($result instanceof UserRow && $result->email_hash !== null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrCheckYourEmail;
                return $resp;
            }
            $user = $result;

            $recover = new RecoverRow();
            $recover->hash = randomString(32, true);
            $recover->user_id = $user->user_id;

            $serviceRecover->db->beginTransaction();

            $err = $serviceRecover->create($recover);
            if ($err instanceof Error) {
                $serviceRecover->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceRecover->create", $err->getMessage()));
                return $resp;
            }

            $template = $this->view(DIR_VIEWS . "/" . ViewEmailMsgAndLink, [
                FieldMsg => DicGoAheadForRecoverPass,
                FieldAddress => ADDRESS . "/recover/check?" . FieldHash . "={$recover->hash}",
            ]);

            $err = $serviceEmail->send($user->email, DicRecoverAccess, $template);
            if ($err instanceof Error) {
                $serviceRecover->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "send email", $err->getMessage()));
                return $resp;
            }

            $serviceRecover->db->commit();

            $resp->data = [];
            $resp->data[FieldDataSendMsg] = sprintf(DicRecoverDataSendMsgTpl, $req->email);
            $resp->data[FieldHash] = $recover->hash; // нужен для отладки в тестах
        }

        return $resp;
    }

    public function check(array $args): MyResponse
    {
        $this->title = DicChangePassword;
        $resp = new MyResponse(ViewPageRecoverCheck);
        $hash = $_GET[FieldHash] ?? "";
        $user = null;
        $serviceUsers = new ServiceUsers();
        $serviceRecover = new ServiceRecovers();

        // если прислали хэш, то найдем строку
        if ($hash) {
            $recover = $serviceRecover->one($hash);
            if ($recover instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceRecover->one", $recover->getMessage()));
                return $resp;
            } elseif ($recover instanceof RecoverRow) {
                $result = $serviceUsers->one($recover->user_id);
                if ($result instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[FieldError] = ErrInternalServer;
                    error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->one", $result->getMessage()));
                    return $resp;
                } elseif ($result === null) {
                    $resp->setHttpCode(400);
                    $resp->data[FieldError] = ErrNotFoundUser;
                    return $resp;
                }

                $user = $result;
                $resp->data[FieldEmail] = $user->email; // чтоб показалась форму смены пароля, необходимо предоставить e-mail
            }
        }

        if (($user !== null) && isset($_POST) && count($_POST)) {
            $req = new RequestRecoverCheck($_POST);

            $err = $this->check_request_check($req);
            if ($err instanceof Error) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            $serviceUsers->db->beginTransaction();
            $user->pass = password_hash($req->pass, PASSWORD_DEFAULT);

            // обновим пользователя с новым паролем
            $result = $serviceUsers->update($user);
            if ($result instanceof Error) {
                $serviceUsers->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->createOrUpdate", $result->getMessage()));
                return $resp;
            }

            // удалим запись за ненадобностью
            $result = $serviceRecover->deleteByUserId($user->user_id);
            if ($result instanceof Error) {
                $serviceUsers->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceRecover->deleteByUserId", $result->getMessage()));
                return $resp;
            }

            $serviceUsers->db->commit();

            $resp->data = [];
            $resp->data[FieldSuccess] = DicPasswordChangedSuccessfully;
        }

        return $resp;
    }

    private function check_request_index(RequestRecover $req): Error|null
    {
        if (!filter_var($req->email, FILTER_VALIDATE_EMAIL)) {
            return new Error(ErrEmailNotCorrect);
        }

        return null;
    }

    private function check_request_check(RequestRecoverCheck $req): Error|null
    {
        if (strlen($req->pass) < PassMinLen) {
            return new Error(ErrPassIsShort);
        }
        if ($req->pass !== $req->passConfirm) {
            return new Error(ErrPasswordsNotEqual);
        }

        return null;
    }
}