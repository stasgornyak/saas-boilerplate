<?php

namespace App\Enums;

enum LicenseStatus: string
{
    case Created = 'created';

    case Paid = 'paid';

    case Active = 'active';

    case Expired = 'expired';

    case Cancelled = 'cancelled';
}
