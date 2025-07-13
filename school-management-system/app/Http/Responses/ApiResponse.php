<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    protected array $response = [];

    protected $message;

    protected array $results = [];

    public static function setMessage(string $message): self
    {
        $instance = new self;
        $instance->message = $message;
        $instance->response['message'] = $instance->message;

        return $instance;
    }

    public static function setData($data): self
    {
        $instance = new self;
        $instance->response['data'] = $data;

        return $instance;
    }

    public function mergeResults(array $results = []): self
    {
        $this->results = array_merge($this->results, $results);
        $this->response = array_merge($this->response, $this->results);

        return $this;
    }

    public function mergeEnums(array $enums): static
    {
        $this->response['enums'] = $enums;

        return $this;
    }

    public function response($statusCode): JsonResponse
    {
        return response()->json($this->response, $statusCode);
    }
}
