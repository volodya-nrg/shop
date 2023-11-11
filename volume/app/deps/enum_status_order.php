<?php declare(strict_types=1);

enum EnumStatusOrder: string
{
    case Created = "created";
    case Collected = "collected";
    case Finished = "finished";
}