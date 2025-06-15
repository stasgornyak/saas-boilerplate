<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PhoneNumber implements Rule
{
    const VALID_PHONE_REGEXP = '/^\d{11,13}$/';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return preg_match(self::VALID_PHONE_REGEXP, $value);
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'phone_must_contain_between_11_and_13_digits';
    }
}
