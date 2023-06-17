<?php

use PHPUnit\Framework\TestCase;

class TestApiClient
{
    private array $tasks = [];
    private ControllerLogin $pageLogin;
    private ControllerReg $pageReg;
    private ControllerRecover $pageRecover;
    private ControllerRecoverChecker $pageRecoverChecker;

    public function login(?callable $cb = null): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $this->pageLogin = new ControllerLogin();
            $response = $this->pageLogin->index([]);

            if ($cb) {
                $cb($response);
            }
        };

        return $this;
    }
    public function reg(?callable $cb = null): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $this->pageReg = new ControllerReg();
            $response = $this->pageReg->index([]);

            if ($cb) {
                $cb($response);
            }
        };

        return $this;
    }
    public function recover(?callable $cb = null): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $this->pageRecover = new ControllerRecover();
            $response = $this->pageRecover->index([]);

            if ($cb) {
                $cb($response);
            }
        };

        return $this;
    }
    public function recoverChecker(?callable $cb = null): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $this->pageRecoverChecker = new ControllerRecoverChecker();
            $response = $this->pageRecoverChecker->index([]);

            if ($cb) {
                $cb($response);
            }
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