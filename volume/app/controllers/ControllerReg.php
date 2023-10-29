<?php

final class ControllerReg extends ControllerBase
{
    public string $title = DicRegistration;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        $resp = new MyResponse(ViewPageReg);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestReg($_POST);

            // для сохранения значение, чтоб в случае ошибки выставить на фронте их еще раз
            $resp->data[FieldRequestedEmail] = $req->email;
            $resp->data[FieldRequestedAgreement] = $req->agreement;
            $resp->data[FieldRequestedPrivatePolicy] = $req->privatePolicy;

            $err = $this->check_request($req);
            if ($err instanceof Error) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            $serviceUsers = new ServiceUsers((new UserTbl())->fields);
            $serviceEmail = new ServiceEmail(
                EMAIL_SMTP_SERVER,
                EMAIL_PORT,
                EMAIL_LOGIN,
                EMAIL_PASS,
                EMAIL_FROM,
                $_SERVER[FieldModeIsTest] === true,
            );

            // проверим пользователя
            $result = $serviceUsers->oneByEmail($req->email);
            if ($result instanceof UserTbl) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ($result->emailHash === null) ? ErrUserAlreadyHas : ErrCheckYourEmail;
                return $resp;
            } elseif ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->oneByEmail", $result->getMessage()));
                return $resp;
            }

            $now = date(DatePattern, time());
            $user = new UserTbl();
            $user->email = $req->email;
            $user->pass = password_hash($req->pass, PASSWORD_DEFAULT);
            $user->emailHash = randomString(32, true);
            $user->createdAt = $now;
            $user->updatedAt = $now;

            // запишем в базу и отправим е-мэйл
            $serviceUsers->db->beginTransaction();

            $userId = $serviceUsers->createOrUpdate($user);
            if ($userId instanceof Error) {
                $serviceUsers->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->createOrUpdate", $userId->getMessage()));
                return $resp;
            }
            $user->userId = $userId;

            $template = $this->view(DIR_VIEWS . "/" . ViewEmailMsgAndLink, [
                FieldMsg => DicGoAheadForVerifyEmail,
                FieldAddress => ADDRESS . "/reg/check?" . FieldHash . "={$user->emailHash}",
            ]);

            $err = $serviceEmail->send($user->email, DicVerifyEmail, $template);
            if ($err instanceof Error) {
                $serviceUsers->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceEmail->send", $err->getMessage()));
                return $resp;
            }

            $serviceUsers->db->commit();

            $resp->data = []; // т.к. происходит далее редирект, то data нам не нужен
            $resp->data[FieldHash] = $user->emailHash; // нужен для теста
            $resp->data[FieldUserId] = $user->userId; // нужен для теста

            if (!$_SERVER[FieldModeIsTest]) {
                redirect("/reg/ok?" . FieldEmail . "={$user->email}");
            }
        }

        return $resp;
    }

    public function ok(): MyResponse
    {
        $resp = new MyResponse(ViewPageRegOK);
        $resp->data[FieldEmail] = $_GET[FieldEmail];
        return $resp;
    }

    public function check(): MyResponse
    {
        $resp = new MyResponse(ViewPageRegCheck);
        $hash = $_GET[FieldHash] ?? "";

        if ($hash) {
            $serviceUsers = new ServiceUsers((new UserTbl())->fields);

            $user = $serviceUsers->oneByEmailHash($hash);
            if ($user instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->oneByEmailHash", $user->getMessage()));
                return $resp;
            } elseif ($user === null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ErrNotFoundUser;
                return $resp;
            }

            $user->emailHash = null;

            $err = $serviceUsers->createOrUpdate($user);
            if ($err instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "serviceUsers->createOrUpdate", $user->getMessage()));
                return $resp;
            }

            $resp->data = [];
            $resp->data[FieldMsg] = DicEmailSuccessfullyConfirmed;
        }

        return $resp;
    }

    private function check_request(RequestReg $req): Error|null
    {
        if (!filter_var($req->email, FILTER_VALIDATE_EMAIL)) {
            return new Error(ErrEmailNotCorrect);
        }
        if (strlen($req->pass) < PassMinLen) {
            return new Error(ErrPassIsShort);
        }
        if ($req->pass != $req->passConfirm) {
            return new Error(ErrPasswordsNotEqual);
        }
        if (!$req->agreement) {
            return new Error(ErrAcceptAgreement);
        }
        if (!$req->privatePolicy) {
            return new Error(ErrAcceptPrivatePolicy);
        }

        return null;
    }
}