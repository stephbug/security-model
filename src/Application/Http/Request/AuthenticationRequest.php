<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Request;

use Illuminate\Http\Request as IlluminateRequest;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

interface AuthenticationRequest extends RequestMatcherInterface
{
    /**
     * @param IlluminateRequest $request
     *
     * @return mixed
     */
    public function extract(IlluminateRequest $request);
}