<?php

require_once dirname(__FILE__) . "/ServiceBase.php";

final class ServiceOrders extends ServiceBase
{
    public function __construct()
    {
        parent::__construct();

        $this->table = "orders";
        $this->fields = "";
    }
    // getAll
    // getOne
    // createOrUpdate
    // delete
}