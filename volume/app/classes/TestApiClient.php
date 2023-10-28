<?php

final class TestApiClient
{
    private array $tasks = [];

    public function createOrUpdateProfile(UserTbl $user, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($user, $cb) {
            $serviceUsers = new ServiceUsers();
            $errCode = 200;
            $data = [];

            $result = $serviceUsers->createOrUpdate($user);
            if ($result instanceof Error) {
                $errCode = 500;
                $data[FieldError] = $result->getMessage();
            } else {
                $user->userId = $result;
            }

            $cb(new MyResponse("", $errCode, $data));
        };
        return $this;
    }

    // createAdmin

    public function login(?RequestLogin $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            $_POST = [];
            if ($req !== null) {
                $_POST[FieldEmail] = $req->getEmail();
                $_POST[FieldPassword] = $req->getPass();
            }

            $cb((new ControllerLogin())->index([]));
        };

        return $this;
    }

    public function logout(callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $cb((new ControllerLogout())->index([]));
        };

        return $this;
    }

    public function reg(?RequestReg $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            $_POST = [];
            if ($req !== null) {
                $_POST[FieldEmail] = $req->getEmail();
                $_POST[FieldPassword] = $req->getPass();
                $_POST[FieldPasswordConfirm] = $req->getPassConfirm();
                $_POST[FieldAgreement] = $req->getAgreement();
                $_POST[FieldPrivacyPolicy] = $req->getPrivatePolicy();
            }

            $cb((new ControllerReg())->index([]));
        };

        return $this;
    }

    public function regCheck(callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($cb, $hash) {
            $cb((new ControllerReg())->check([]));
        };

        return $this;
    }

    public function recover(?RequestRecover $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            $_POST = [];
            if ($req !== null) {
                $_POST[FieldEmail] = $req->getEmail();
            }

            $cb((new ControllerRecover())->index([]));
        };

        return $this;
    }

    public function recoverCheck(?RequestRecoverCheck $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            $_POST = [];
            if ($req !== null) {
                $_POST[FieldPassword] = $req->getPass();
                $_POST[FieldPasswordConfirm] = $req->getPassConfirm();
            }

            $cb((new ControllerRecover())->check([]));
        };

        return $this;
    }

    public function adm(callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $cb((new ControllerAdm())->index([]));
        };

        return $this;
    }

    public function run(): void
    {
        foreach ($this->tasks as $task) {
            $task();
        }
    }
}