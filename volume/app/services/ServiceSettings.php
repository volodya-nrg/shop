<?php

final class ServiceSettings extends ServiceDB
{
    protected string $table = "settings";
    protected array $fields = ["setting_id", "value"];
}