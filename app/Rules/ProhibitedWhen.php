<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ProhibitedWhen implements Rule
{
    private bool $param;

    private string $messageParam;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(bool $param, ?string $messageParam = null)
    {
        $this->param = $param;
        $this->messageParam = $messageParam;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        return ! $this->param;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return ':attribute_modification_is_prohibited'.($this->messageParam ? '_'.$this->messageParam : '');
    }
}
