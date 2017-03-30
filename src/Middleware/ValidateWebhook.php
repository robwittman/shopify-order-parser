<?php

namespace App\Middleware;

class ValidateWebhook
{
    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function __invoke($request, $response, $next)
    {
        return true;
    }
}
