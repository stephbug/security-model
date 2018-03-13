<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Application\Http\Entrypoint;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class HttpBasicEntrypoint implements Entrypoint
{
    /**
     * @var ResponseFactory
     */
    private $response;

    /**
     * @var Factory
     */
    private $view;

    /**
     * @var string
     */
    private $realmName;

    public function __construct(ResponseFactory $response, Factory $view, string $realmName = 'Private access')
    {
        $this->response = $response;
        $this->view = $view;
        $this->realmName = $realmName;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response
    {
        $statusCode = Response::HTTP_UNAUTHORIZED;

        $content = $this->view->make('errors.' . $statusCode);

        $headers = ['WWW-authenticate' => sprintf('Basic realm="%s"', $this->realmName)];

        return $this->response->make($content, $statusCode, $headers);
    }
}