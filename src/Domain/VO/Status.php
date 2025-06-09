<?php

namespace App\Domain\VO;

enum Status: string
{
    case DONE = 'done';
    case TODO = 'todo';
}
