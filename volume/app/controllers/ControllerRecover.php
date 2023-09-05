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

            $err = $this->check($req);
            if ($err !== null) {
                $resp->setHttpCode($err->getCode());
                $resp->data[FieldError] = $err->getMessage();
                return $resp;
            }

            // подключение к БД и тд
            $resp->data[FieldDataSendMsg] = sprintf(DicRecoverDataSendMsgTpl, $req->getEmail());;
        }

        return $resp;
    }

    private function check(RequestRecover $req): ?Error
    {
        if (!filter_var($req->getEmail(), FILTER_VALIDATE_EMAIL)) {
            return new Error(ErrEmailNotCorrect, 400);
        }

        return null;
    }
}