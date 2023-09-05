<?php

require_once dirname(__FILE__) . "/ServiceBase.php";

final class ServiceInfos extends ServiceBase
{
    public function __construct()
    {
        parent::__construct();

        $this->table = "infos";
        $this->fields = "";
    }
    // getAll
    // getOne
    // createOrUpdate
    // delete
}