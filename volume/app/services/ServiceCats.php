<?php

require_once dirname(__FILE__) . "/ServiceBase.php";

final class ServiceCats extends ServiceBase
{
    public function __construct()
    {
        parent::__construct();

        $this->table = "cats";
        $this->fields = "";
    }
    // getAll
    // getOne
    // createOrUpdate
    // delete
}