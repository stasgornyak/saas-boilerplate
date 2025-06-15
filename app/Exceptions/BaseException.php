<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BaseException extends Exception
{
    protected array $data;

    protected array $messages = [];

    protected string $description;

    /**
     * ApiResponseException constructor.
     *
     * @param  string|array|null  $message
     */
    public function __construct($message = null, ?string $description = null, int $code = 0, array $data = [])
    {
        if (is_array($message)) {
            $this->messages = array_values(array_unique($message));
            $message = '';

            $this->description =
                $description ?: implode(' ', array_map(fn ($item) => slugToText($item), $this->messages));
        } else {
            $this->description = $description ?: slugToText($message);
        }

        $this->data = $data;

        $code = $code ?: Response::HTTP_BAD_REQUEST;

        parent::__construct($message, $code);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function render(): JsonResponse
    {
        $responseData = [
            'message' => $this->message ?: $this->messages,
            'description' => $this->description,
            'data' => $this->data ?: null,
        ];

        return response()->json($responseData, $this->code);
    }
}
