<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Entrypoint;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use StephBug\SecurityModel\Application\Exception\AuthenticationException;
use StephBug\SecurityModel\Application\Http\Entrypoint\DefaultJsonEntrypoint;
use StephBugTest\SecurityModel\Unit\TestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultJsonEntrypointTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_a_json_response(): void
    {
        $statusCode = Response::HTTP_UNAUTHORIZED;

        $jsonResp = new DefaultJsonEntrypoint($this->responseFactory());
        $response = $jsonResp->startAuthentication(Request::create('','GET'));

        $this->assertEquals($statusCode, $response->getStatusCode());
        $exp = [
            'message' => 'You must login first',
            'status_code' => $statusCode
        ];

        $this->assertEquals(json_encode($exp), $response->getContent());
    }

    /**
     * @test
     */
    public function it_return_exception_message(): void
    {
        $statusCode = Response::HTTP_UNAUTHORIZED;
        $ex = new AuthenticationException('some message');

        $jsonResp = new DefaultJsonEntrypoint($this->responseFactory());
        $response = $jsonResp->startAuthentication(Request::create('','GET'), $ex);

        $exp = [
            'message' => $ex->getMessage(),
            'status_code' => $statusCode
        ];

        $this->assertEquals(json_encode($exp), $response->getContent());
    }

    private function responseFactory(): ResponseFactory{

        return new \Illuminate\Routing\ResponseFactory(
            $this->getMockForAbstractClass(ViewFactory::class),
            $this->getMockBuilder(Redirector::class)->disableOriginalConstructor()->getMock()
        );
    }
}