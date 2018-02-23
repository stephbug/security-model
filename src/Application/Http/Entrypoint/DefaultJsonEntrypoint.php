<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Entrypoint;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class DefaultJsonEntrypoint implements Entrypoint
{
    /**
     * @var ResponseFactory
     */
    private $response;

    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response
    {
        $statusCode = Response::HTTP_UNAUTHORIZED;

        return $this->response->json(
            [
                'message' => $exception ? $exception->getMessage() : 'You must login first',
                'status_code' => $statusCode
            ],
            $statusCode);
    }
}