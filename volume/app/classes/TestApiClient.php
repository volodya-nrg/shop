<?php declare(strict_types=1);

final class TestApiClient
{
    private array $tasks = [];

    public function login(?RequestLogin $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
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

    public function reg(?RequestReg $req, string $role, bool $isEmailConfirmed, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $role, $isEmailConfirmed, $cb) {
            global $PDO;
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $resp = (new ControllerReg())->index([]); // $resp может быть с ошибкой

            if (!isset($resp->data[EnumField::Error->value]) &&
                isset($resp->data[EnumField::UserId->value]) &&
                ($role !== "" || $isEmailConfirmed)) {

                $serviceUsers = new ServiceUsers($PDO);
                $userId = $resp->data[EnumField::UserId->value];

                $result = $serviceUsers->one($userId);
                if ($result === null) {
                    abort(EnumErr::NotFoundRow->value);
                } elseif ($result instanceof Error) {
                    abort($result->getMessage());
                }
                $user = $result;

                if ($role !== "") {
                    $user->role = $role;
                }
                if ($isEmailConfirmed) {
                    $user->email_hash = null;
                }

                $result = $serviceUsers->update($user);
                if ($result instanceof Error) {
                    abort($result->getMessage());
                }
            }

            $cb($resp);
        };

        return $this;
    }

    public function regCheck(callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $cb((new ControllerReg())->check([]));
        };

        return $this;
    }

    public function recover(?RequestRecover $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $cb((new ControllerRecover())->index([]));
        };

        return $this;
    }

    public function recoverCheck(?RequestRecoverCheck $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $cb((new ControllerRecover())->check([]));
        };

        return $this;
    }

    public function adm(callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            try {
                $resp = (new ControllerAdm())->index([]);
            } catch (Exception $e) {
                $resp = $this->getRespAdmAccessDined($e);
            }

            $cb($resp);
        };

        return $this;
    }

    public function admCats(?RequestPaginator $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            try {
                $resp = (new ControllerAdm())->cats([]);
            } catch (Exception $e) {
                $resp = $this->getRespAdmAccessDined($e);
            }

            $cb($resp);
        };

        return $this;
    }

    public function admCat(?RequestCat $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            try {
                $resp = (new ControllerAdm())->cat([]);
            } catch (Exception $e) {
                $resp = $this->getRespAdmAccessDined($e);
            }

            $cb($resp);
        };

        return $this;
    }

    public function admItems(?RequestPaginator $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            try {
                $resp = (new ControllerAdm())->items([]);
            } catch (Exception $e) {
                $resp = $this->getRespAdmAccessDined($e);
            }

            $cb($resp);
        };

        return $this;
    }

    public function admItem(?RequestItem $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            try {
                $resp = (new ControllerAdm())->item([]);
            } catch (Exception $e) {
                $resp = $this->getRespAdmAccessDined($e);
            }

            $cb($resp);
        };

        return $this;
    }

    public function admUsers(?RequestPaginator $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            try {
                $resp = (new ControllerAdm())->users([]);
            } catch (Exception $e) {
                $resp = $this->getRespAdmAccessDined($e);
            }

            $cb($resp);
        };

        return $this;
    }

    public function admUser(?RequestUser $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            try {
                $resp = (new ControllerAdm())->user([]);
            } catch (Exception $e) {
                $resp = $this->getRespAdmAccessDined($e);
            }

            $cb($resp);
        };

        return $this;
    }

    public function admOrders(?RequestPaginator $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            try {
                $resp = (new ControllerAdm())->orders([]);
            } catch (Exception $e) {
                $resp = $this->getRespAdmAccessDined($e);
            }

            $cb($resp);
        };

        return $this;
    }

    public function admOrder(?RequestOrder $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            try {
                $resp = (new ControllerAdm())->order([]);
            } catch (Exception $e) {
                $resp = $this->getRespAdmAccessDined($e);
            }

            $cb($resp);
        };

        return $this;
    }

    public function run(): void
    {
        foreach ($this->tasks as $task) {
            $task();
            $_POST = []; // с каждой выполненой задачей явно убираем пост-запросы
        }
    }

    private function getRespAdmAccessDined(Exception $e): MyResponse
    {
        $resp = new MyResponse(EnumViewFile::PageAccessDined);
        $resp->setHttpCode(401);
        $resp->data[EnumField::Error->value] = $e->getMessage();

        return $resp;
    }
}