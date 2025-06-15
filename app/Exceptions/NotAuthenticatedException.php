<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

/**
 * When User is not Authenticated.
 *
 * Class NotAuthenticatedException
 */
class NotAuthenticatedException extends BaseException
{
    /**
     * NotAuthenticatedException constructor.
     *
     * @param  array|string|null  $message
     */
    public function __construct($message = null, ?string $description = null, array $data = [])
    {
        $message = $message ?: MESSAGES['not_authenticated'];

        parent::__construct($message, $description, Response::HTTP_UNAUTHORIZED, $data);
    }
}
