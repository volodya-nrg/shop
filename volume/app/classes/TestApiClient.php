<?php

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
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $resp = (new ControllerReg())->index([]); // $resp может быть с ошибкой

            if (!isset($resp->data[FieldError]) &&
                isset($resp->data[FieldUserId]) &&
                ($role !== "" || $isEmailConfirmed)) {

                $serviceUsers = new ServiceUsers();
                $userId = $resp->data[FieldUserId];

                $result = $serviceUsers->one($userId);
                if ($result === null) {
                    abort(ErrNotFoundUser);
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
            $cb((new ControllerAdm())->index([]));
        };

        return $this;
    }

    public function admCats(?RequestPaginator $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $cb((new ControllerAdm())->cats([]));
        };

        return $this;
    }

    public function admCat(?RequestCat $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $cb((new ControllerAdm())->cat([]));
        };

        return $this;
    }

    public function admItems(?RequestPaginator $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $cb((new ControllerAdm())->items([]));
        };

        return $this;
    }

    public function admItem(?RequestItem $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $cb((new ControllerAdm())->item([]));
        };

        return $this;
    }

    public function admUsers(?RequestPaginator $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $cb((new ControllerAdm())->users([]));
        };

        return $this;
    }

    public function admUser(?RequestUser $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $cb((new ControllerAdm())->user([]));
        };

        return $this;
    }

    public function admOrders(?RequestPaginator $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $cb((new ControllerAdm())->orders([]));
        };

        return $this;
    }

    public function admOrder(?RequestOrder $req, callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($req, $cb) {
            if ($req !== null) {
                $_POST = $req->toArray();
            }

            $cb((new ControllerAdm())->order([]));
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
}