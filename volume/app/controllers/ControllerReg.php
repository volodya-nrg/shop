<?php

class ControllerReg extends ControllerBase
{
    public string $title = DicRegistration;
    public string $description = "";

    public function index(array $args): Response
    {
        $response = new Response(ViewPageReg);

        if (!empty($_POST[FieldEmail]) && !empty($_POST[FieldPassword]) && !empty($_POST[FieldPasswordConfirm])) {
            $tmpEmail = trim($_POST[FieldEmail]);
            $tmpPassword = trim($_POST[FieldPassword]);
            $tmpPasswordConfirm = trim($_POST[FieldPasswordConfirm]);
            $response->data[FieldRequestedAgreement] = isset($_POST[FieldAgreement]) && trim($_POST[FieldAgreement]) != "";
            $response->data[FieldRequestedPrivatePolicy] = isset($_POST[FieldPrivacyPolicy]) && trim($_POST[FieldPrivacyPolicy]) != "";

            if (!filter_var($tmpEmail, FILTER_VALIDATE_EMAIL)) {
                $response->data[FieldErrors][] = ErrEmailNotCorrect;
            } else {
                $response->data[FieldRequestedEmail] = $tmpEmail;
            }
            if (strlen($tmpPassword) < PassMinLen) {
                $response->data[FieldErrors][] = ErrPassIsShort;
            } else {
                if ($tmpPassword != $tmpPasswordConfirm) {
                    $response->data[FieldErrors][] = ErrPasswordsNotEqual;
                }
            }
            if (!$response->data[FieldRequestedAgreement]) {
                $response->data[FieldErrors][] = ErrAcceptAgreement;
            }
            if (!$response->data[FieldRequestedPrivatePolicy]) {
                $response->data[FieldErrors][] = ErrAcceptPrivatePolicy;
            }

            if (isset($response->data[FieldErrors]) && count($response->data[FieldErrors])) {
                $response->setHttpCode(400);
            }
        }

        return $response;
    }
}