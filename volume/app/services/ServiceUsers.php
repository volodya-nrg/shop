<?php

require_once dirname(__FILE__) . "/ServiceBase.php";

final class ServiceUsers extends ServiceBase
{
    public function __construct()
    {
        parent::__construct();

        $this->table = "users";
        $this->fields = "";
    }
    // getAll
    // getOne
    // createOrUpdate
    // delete
}