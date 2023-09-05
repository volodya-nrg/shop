<?php

require_once dirname(__FILE__) . "/ServiceBase.php";

final class ServiceItems extends ServiceBase
{
    public function __construct()
    {
        parent::__construct();

        $this->table = "items";
        $this->fields = "";
    }
    // getAll
    // getOne
    // createOrUpdate
    // delete
}