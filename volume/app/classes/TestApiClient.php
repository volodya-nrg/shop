<?php

final class TestApiClient
{
    private array $tasks = [];
    private ControllerReg $pageReg;
    private ControllerRecover $pageRecover;
    private ControllerRecoverChecker $pageRecoverChecker;

    public function login(callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $pageLogin = new ControllerLogin();
            $cb($pageLogin->index([]));
        };

        return $this;
    }

    public function reg(callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $this->pageReg = new ControllerReg();
            $cb($this->pageReg->index([]));
        };

        return $this;
    }

    public function recover(callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $this->pageRecover = new ControllerRecover();
            $cb($this->pageRecover->index([]));
        };

        return $this;
    }

    public function recoverChecker(callable $cb): TestApiClient
    {
        $this->tasks[] = function () use ($cb) {
            $this->pageRecoverChecker = new ControllerRecoverChecker();
            $cb($this->pageRecoverChecker->index([]));
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