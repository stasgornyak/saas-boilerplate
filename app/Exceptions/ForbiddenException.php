<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

/**
 * Exception when access is forbidden.
 *
 * Class ForbiddenException
 */
class ForbiddenException extends BaseException
{
    /**
     * ForbiddenException constructor.
     *
     * @param  array  $data
     */
    public function __construct(?string $message = null, ?string $description = null, $data = [])
    {
        $message = $message ?: MESSAGES['access_denied'];
        $description = $description ?: DESCRIPTIONS['access_denied'];

        parent::__construct($message, $description, Response::HTTP_FORBIDDEN, $data);
    }
}
