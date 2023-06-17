<?php

class ControllerRecover extends ControllerBase
{
    public string $title = DicRecoverAccess;
    public string $description = "";

    public function index(array $args): Response
    {
        $response = new Response(ViewPageRecover);

        if (!empty($_POST[FieldEmail])) {
            $requestedEmail = trim($_POST[FieldEmail]);

            if (!filter_var($requestedEmail, FILTER_VALIDATE_EMAIL)) {
                $response->data[FieldErrors][] = ErrEmailNotCorrect;
            }

            if (isset($response->data[FieldErrors]) && count($response->data[FieldErrors])) {
                $response->setHttpCode(400);
            } else {
                // тут надо проверить, есть ли такой вообще е-мэйл
                $response->data[FieldDataSendMsg] = sprintf(DicRecoverDataSendMsgTpl, $requestedEmail);
            }
        }

        return $response;
    }
}