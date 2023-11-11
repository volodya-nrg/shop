<?php declare(strict_types=1);

final class ControllerReg extends ControllerBase
{
    public string $title = EnumDic::Registration->value;
    public string $description = "";

    public function index(array $args): MyResponse
    {
        global $PDO;
        $resp = new MyResponse(EnumViewFile::PageReg);

        if (isset($_POST) && count($_POST)) {
            $req = new RequestReg($_POST);

            // для сохранения значение, чтоб в случае ошибки выставить на фронте их еще раз
            $resp->data[EnumField::RequestedEmail->value] = $req->email;
            $resp->data[EnumField::RequestedAgreement->value] = $req->agreement;
            $resp->data[EnumField::RequestedPrivatePolicy->value] = $req->privatePolicy;

            $err = $this->check_request($req);
            if ($err instanceof Error) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = $err->getMessage();
                return $resp;
            }

            $serviceUsers = new ServiceUsers($PDO);
            $serviceEmail = new ServiceEmail(
                EMAIL_SMTP_SERVER,
                EMAIL_PORT,
                EMAIL_LOGIN,
                EMAIL_PASS,
                EMAIL_FROM,
                $_SERVER[EnumField::ModeIsTest->value] === true,
            );

            // проверим пользователя
            $result = $serviceUsers->oneByEmail($req->email);
            if ($result instanceof UserRow) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = ($result->email_hash === null) ? EnumErr::UserAlreadyHas->value : EnumErr::CheckYourEmail->value;
                return $resp;
            } elseif ($result instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceUsers->oneByEmail", $result->getMessage()));
                return $resp;
            }

            $user = new UserRow();
            $user->email = $req->email;
            $user->pass = password_hash($req->pass, PASSWORD_DEFAULT);
            $user->email_hash = randomString(32, true);

            // запишем в базу и отправим е-мэйл
            $serviceUsers->db->beginTransaction();

            $userId = $serviceUsers->create($user);
            if ($userId instanceof Error) {
                $serviceUsers->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceUsers->createOrUpdate", $userId->getMessage()));
                return $resp;
            }
            $user->user_id = $userId;

            $template = $this->view(EnumViewFile::EmailMsgAndLink, [
                EnumField::Msg->value => EnumDic::GoAheadForVerifyEmail->value,
                EnumField::Address->value => ADDRESS . "/reg/check?" . EnumField::Hash->value . "={$user->email_hash}",
            ]);

            $err = $serviceEmail->send($user->email, EnumDic::VerifyEmail->value, $template);
            if ($err instanceof Error) {
                $serviceUsers->db->rollBack();

                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceEmail->send", $err->getMessage()));
                return $resp;
            }

            $serviceUsers->db->commit();

            $resp->data = []; // т.к. происходит далее редирект, то data нам не нужен
            $resp->data[EnumField::Hash->value] = $user->email_hash; // нужен для теста
            $resp->data[EnumField::UserId->value] = $user->user_id; // нужен для теста

            if (!$_SERVER[EnumField::ModeIsTest->value]) {
                redirect("/reg/ok?" . EnumField::Email->value . "={$user->email}");
            }
        }

        return $resp;
    }

    public function ok(): MyResponse
    {
        $resp = new MyResponse(EnumViewFile::PageRegOK);
        $resp->data[EnumField::Email->value] = $_GET[EnumField::Email->value];
        return $resp;
    }

    public function check(): MyResponse
    {
        global $PDO;
        $resp = new MyResponse(EnumViewFile::PageRegCheck);
        $hash = $_GET[EnumField::Hash->value] ?? "";

        if ($hash) {
            $serviceUsers = new ServiceUsers($PDO);

            $user = $serviceUsers->oneByEmailHash($hash);
            if ($user instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceUsers->oneByEmailHash", $user->getMessage()));
                return $resp;
            } elseif ($user === null) {
                $resp->setHttpCode(400);
                $resp->data[EnumField::Error->value] = EnumErr::NotFoundUser->value;
                return $resp;
            }

            $user->email_hash = null;

            $err = $serviceUsers->update($user);
            if ($err instanceof Error) {
                $resp->setHttpCode(500);
                $resp->data[EnumField::Error->value] = EnumErr::InternalServer->value;
                error_log(sprintf(EnumErr::InWhenTpl->value, __METHOD__, "serviceUsers->createOrUpdate", $user->getMessage()));
                return $resp;
            }

            $resp->data = [];
            $resp->data[EnumField::Msg->value] = EnumDic::EmailSuccessfullyConfirmed->value;
        }

        return $resp;
    }

    private function check_request(RequestReg $req): Error|null
    {
        if (!filter_var($req->email, FILTER_VALIDATE_EMAIL)) {
            return new Error(EnumErr::EmailNotCorrect->value);
        }
        if (strlen($req->pass) < PassMinLen) {
            return new Error(sprintf(EnumErr::PassIsShortTpl->value, PassMinLen));
        }
        if ($req->pass != $req->passConfirm) {
            return new Error(EnumErr::PasswordsNotEqual->value);
        }
        if (!$req->agreement) {
            return new Error(EnumErr::AcceptAgreement->value);
        }
        if (!$req->privatePolicy) {
            return new Error(EnumErr::AcceptPrivatePolicy->value);
        }

        return null;
    }
}