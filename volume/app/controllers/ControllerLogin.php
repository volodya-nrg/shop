<?php

class ControllerLogin extends ControllerBase
{
    public string $title = DicEnter;
    public string $description = "";

    public function index(array $args): Response
    {
        $response = new Response(ViewPageLogin);

        if (!empty($_POST[FieldEmail]) && !empty($_POST[FieldPassword])) {
            $requestedEmail = trim($_POST[FieldEmail]);
            $requestedPass = trim($_POST[FieldPassword]);

            if (!filter_var($requestedEmail, FILTER_VALIDATE_EMAIL)) {
                $response->data[FieldErrors][] = ErrEmailNotCorrect;
            } else {
                $response->data[FieldRequestedEmail] = $requestedEmail;
            }
            if (strlen($requestedPass) < PassMinLen) {
                $response->data[FieldErrors][] = ErrPassIsShort;
            }

            if (isset($response->data[FieldErrors]) && count($response->data[FieldErrors])) {
                $response->setHttpCode(400);
            }
        }

        return $response;
    }
}