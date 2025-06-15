<?php

namespace App\Enums;

enum LicensePeriod: int
{
    case Monthly = 30;

    case Yearly = 365;
}
