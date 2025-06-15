<?php

namespace App\Exceptions;

use Illuminate\Http\Response;

/**
 * Exceptions from server errors.
 *
 * Class ServerException
 */
class ServerErrorException extends BaseException
{
    /**
     * ServerException constructor.
     *
     * @param  string|array|null  $message
     */
    public function __construct($message = null, ?string $description = null, array $data = [])
    {
        $message = $message ?: MESSAGES['an_error_occurred'];
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;

        parent::__construct($message, $description, $code, $data);
    }
}
