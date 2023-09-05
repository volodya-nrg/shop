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

            $err = $this->check($req);
            if ($err !== null) {
                $resp->setHttpCode($err->getCode());
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            // подключение к БД и тд
        }

        return $resp;
    }

    private function check(RequestReg $req): ?Error
    {
        if (!filter_var($req->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return new Error(ErrEmailNotCorrect, 400);
        }
        if (strlen($req->getPass()) < PassMinLen) {
            return new Error(ErrPassIsShort, 400);
        }
        if ($req->getPass() != $req->getPassConfirm()) {
            return new Error(ErrPasswordsNotEqual, 400);
        }
        if (!$req->getAgreement()) {
            return new Error(ErrAcceptAgreement, 400);
        }
        if (!$req->getPrivatePolicy()) {
            return new Error(ErrAcceptPrivatePolicy, 400);
        }

        return null;
    }
}