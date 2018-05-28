<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Request;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use StephBug\SecurityModel\Application\Http\Request\EmailAuthenticationRequest;
use StephBug\SecurityModel\Application\Values\Contract\EmailIdentifier;
use StephBugTest\SecurityModel\Unit\TestCase;

class EmailAuthenticationRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_if_route_match_request(): void
    {
        $match = new EmailAuthenticationRequest('foo.bar');

        $request = Request::create('/foo/bar', 'GET');
        $request->setRouteResolver(function () use ($request) {
            $route = new Route('GET', '/foo/bar', ['as' => 'foo.bar']);
            $route->bind($request);
            return $route;
        });

        $this->assertTrue($match->matches($request));
    }

    /**
     * @test
     */
    public function it_extract_email(): void
    {
        $match = new EmailAuthenticationRequest('foo.bar');
        $request = Request::create('/', 'POST', ['identifier' => 'foo@bar.com']);

        $this->assertInstanceOf(EmailIdentifier::class, $match->extract($request));
        $this->assertEquals('foo@bar.com', $match->extract($request)->identify());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\AuthenticationException
     */
    public function it_raise_exception_when_extracted_identifier_is_invalid(): void
    {
        $match = new EmailAuthenticationRequest('foo.bar');
        $request = Request::create('/', 'POST');

        $match->extract($request);
    }
}