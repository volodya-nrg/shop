<?php

require_once dirname(__FILE__) . "/ServiceBase.php";

final class ServiceProps extends ServiceBase
{
    public function __construct()
    {
        parent::__construct();

        $this->table = "props";
        $this->fields = "";
    }
    // getAll
    // getOne
    // createOrUpdate
    // delete
}