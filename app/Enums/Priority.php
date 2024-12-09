<?php

namespace App\Enums;

use App\Enums\Concerns\InteractsWithEnums;

enum Priority: int
{
    use InteractsWithEnums;

    case URGENT = 1;
    case HIGH = 2;
    case NORMAL = 3;
    case LOW = 4;
}
