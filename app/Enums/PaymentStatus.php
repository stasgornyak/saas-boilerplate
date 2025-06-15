<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Created = 'created';

    case Processing = 'processing';

    case Declined = 'declined';

    case Hold = 'hold';

    case Approved = 'approved';

    case Expired = 'expired';

    case Reversed = 'reversed';

    public static function failedValues(): array
    {
        return [self::Declined->value, self::Expired->value, self::Reversed->value];
    }

    public static function successfulValues(): array
    {
        return [self::Created->value, self::Processing->value, self::Approved->value];
    }
}
