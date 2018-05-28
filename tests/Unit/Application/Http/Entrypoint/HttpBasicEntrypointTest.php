<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Entrypoint;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;
use StephBug\SecurityModel\Application\Http\Entrypoint\HttpBasicEntrypoint;
use StephBugTest\SecurityModel\Unit\TestCase;
use Symfony\Component\HttpFoundation\Response;

class HttpBasicEntrypointTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_response(): void
    {
        $viewFactory = $this->getMockForAbstractClass(ViewFactory::class);
        $viewFactory->expects($this->once())->method('make')->willReturn('foobar');

        $entrypoint = new HttpBasicEntrypoint($this->getResponseFactory(), $viewFactory, 'my private access');
        $response = $entrypoint->startAuthentication(new Request());

        $this->assertEquals('foobar', $response->getContent());
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());

        $this->assertEquals(
            'Basic realm="my private access"', $response->headers->get('WWW-authenticate')
        );
    }

    private function getResponseFactory(): \Illuminate\Contracts\Routing\ResponseFactory
    {
        return new ResponseFactory(
            $this->getMockForAbstractClass(ViewFactory::class),
            $this->getMockBuilder(Redirector::class)
                ->disableOriginalConstructor()
                ->getMock()
        );
    }
}