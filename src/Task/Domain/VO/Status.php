<?php
declare(strict_types=1);

namespace App\Task\Domain\VO;

enum Status: string
{
    case DONE = 'done';
    case TODO = 'todo';
}