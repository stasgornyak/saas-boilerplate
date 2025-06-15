<?php

namespace App\Enums;

enum ProcessStatus: string
{
    case Pending = 'pending';

    case Processing = 'processing';

    case Done = 'done';

    case Failed = 'failed';
}
