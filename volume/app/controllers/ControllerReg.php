<?php

final class ControllerReg extends ControllerBase
{
    public string $title = DicRegistration;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        $resp = new MyResponse(ViewPageReg);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestReg();
            $req->parsePOST($_POST);

            // для сохранения значение, чтоб в случае ошибки выставить на фронте их еще раз
            $resp->data[FieldRequestedEmail] = $req->getEmail();
            $resp->data[FieldRequestedAgreement] = $req->getAgreement();
            $resp->data[FieldRequestedPrivatePolicy] = $req->getPrivatePolicy();

            $err = $this->check_request($req);
            if ($err !== null) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            $serviceUsers = new ServiceUsers();
            $serviceEmail = new ServiceEmail(EMAIL_SMTP_SERVER, EMAIL_PORT, EMAIL_LOGIN, EMAIL_PASS, EMAIL_FROM);

            // проверим пользователя
            $result = $serviceUsers->oneByEmail($req->getEmail());
            if ($result instanceof User) {
                $resp->setHttpCode(400);
                $resp->data[FieldError] = ($result->hashForCheckEmail !== "") ? ErrCheckYourEmail : ErrUserAlreadyHas;
                return $resp;
            } elseif ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "oneByEmail", $result->getMessage()));
                return $resp;
            }

            $user = new User();
            $user->email = $req->getEmail();
            $user->pass = password_hash($req->getPass(), PASSWORD_DEFAULT); // password_verify('rasmuslerdorf', $hash)
            $user->hashForCheckEmail = randomString();

            // запишем в базу и отправим е-мэйл
            $serviceUsers->db->beginTransaction();

            $result = $serviceUsers->createOrUpdate($user);
            if ($result instanceof Error) {
                $serviceUsers->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "createOrUpdate", $result->getMessage()));
                return $resp;
            }

            $err = $serviceEmail->send($user->email, "validate email", $user->hashForCheckEmail);
            if ($err instanceof Error) {
                $serviceUsers->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[FieldError] = ErrInternalServer;
                error_log(sprintf(ErrInWhenTpl, __METHOD__, "send email", $result->getMessage()));
                return $resp;
            }

            $serviceUsers->db->commit();

            redirect("/reg/ok?" . FieldEmail . "={$user->email}");
        }

        //redirect("/reg/check?".FieldHash."=asd");

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
        $resp->data[FieldError] = "===";
        return $resp;
    }

    private function check_request(RequestReg $req): ?Error
    {
        if (!filter_var($req->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return new Error(ErrEmailNotCorrect);
        }
        if (strlen($req->getPass()) < PassMinLen) {
            return new Error(ErrPassIsShort);
        }
        if ($req->getPass() != $req->getPassConfirm()) {
            return new Error(ErrPasswordsNotEqual);
        }
        if (!$req->getAgreement()) {
            return new Error(ErrAcceptAgreement);
        }
        if (!$req->getPrivatePolicy()) {
            return new Error(ErrAcceptPrivatePolicy);
        }

        return null;
    }
}