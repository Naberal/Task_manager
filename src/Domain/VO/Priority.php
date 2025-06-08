<?php

namespace App\Domain\VO;

enum Priority: int
{
    case CRITICAL = 1;
    case HIGH = 2;
    case MEDIUM = 3;
    case LOW = 4;
    case MINOR = 5;
}
