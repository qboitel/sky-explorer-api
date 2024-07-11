<?php

namespace App\Enum;

enum Type: int
{
    use EnumToArray;

    case LOISIR = 1;
    case FORMATION = 2;
}

