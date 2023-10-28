<?php

final class ControllerRecover extends ControllerBase
{
    public string $title = DicRecoverAccess;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        $resp = new MyResponse(ViewPageRecover);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestRecover();
            $req->parsePOST($_POST);

            $resp->data[FieldRequestedEmail] = $req->getEmail();

            $err = $this->check_request_index($req);
            if ($err instanceof Error) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            $serviceUsers = new ServiceUsers();
            $serviceRecover = new ServiceRecover();
            $serviceEmail = new ServiceEmail(EMAIL_SMTP_SERVER, EMAIL_PORT, EMAIL_LOGIN, EMAIL_PASS, EMAIL_FROM, $_SERVER[FieldModeIsTest] === true);

            // возьмем пользователя
            $user = $serviceUsers->oneByEmail($req->getEmail());
            if ($user instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->oneByEmail", $user->getMessage()));
                return $resp;
            } elseif ($user === null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrNotFoundUser;
                return $resp;
            } elseif ($user instanceof UserTbl && $user->emailHash != "") {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrCheckYourEmail;
                return $resp;
            }

            $recover = new RecoverTbl([
                randomString(32, true),
                $user->userId,
            ]);

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
            $resp->data[FieldDataSendMsg] = sprintf(DicRecoverDataSendMsgTpl, $req->getEmail());
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
        $serviceRecover = new ServiceRecover();

        if ($hash) {
            $recover = $serviceRecover->one($hash);
            if ($recover instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceRecover->one", $recover->getMessage()));
                return $resp;
            } elseif ($recover instanceof RecoverTbl) {
                $user = $serviceUsers->one($recover->userId);
                if ($user instanceof Error) {
                    $resp->setHttpCode(500);
                    $resp->data[FieldError] = ErrInternalServer;
                    error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->one", $user->getMessage()));
                    return $resp;
                } elseif ($user === null) {
                    $resp->setHttpCode(400);
                    $resp->data[FieldError] = ErrNotFoundUser;
                    return $resp;
                }

                $resp->data[FieldEmail] = $user->email; // чтоб показалась форма смены пароля, необходимо предоставить e-mail
            }
        }

        if (($user !== null) && isset($_POST) && count($_POST)) {
            $req = new RequestRecoverCheck();
            $req->parsePOST($_POST);

            $err = $this->check_request_check($req);
            if ($err instanceof Error) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            $serviceUsers->db->beginTransaction();
            $user->pass = password_hash($req->getPass(), PASSWORD_DEFAULT);

            $result = $serviceUsers->createOrUpdate($user);
            if ($result instanceof Error) {
                $serviceUsers->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->createOrUpdate", $result->getMessage()));
                return $resp;
            }

            $result = $serviceRecover->deleteByUserId($user->userId);
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
        if (!filter_var($req->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return new Error(ErrEmailNotCorrect);
        }

        return null;
    }

    private function check_request_check(RequestRecoverCheck $req): Error|null
    {
        if (strlen($req->getPass()) < PassMinLen) {
            return new Error(ErrPassIsShort);
        }
        if ($req->getPass() !== $req->getPassConfirm()) {
            return new Error(ErrPasswordsNotEqual);
        }

        return null;
    }
}