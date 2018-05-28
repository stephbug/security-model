<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Request;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use StephBug\SecurityModel\Application\Http\Request\IdentifierPasswordAuthenticationRequest;
use StephBugTest\SecurityModel\Unit\TestCase;

class IdentifierPasswordAuthenticationRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_if_route_match_request(): void
    {
        $match = new IdentifierPasswordAuthenticationRequest('foo.bar');

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
    public function it_extract_credentials(): void
    {
        $match = new IdentifierPasswordAuthenticationRequest('foo.bar');
        $request = Request::create('/', 'POST', ['identifier' => 'foo@bar.com', 'password' => 'password']);

        $this->assertCount(2, $match->extract($request));
        $this->assertEquals('foo@bar.com', $match->extract($request)[0]->identify());
        $this->assertEquals('password', $match->extract($request)[1]->credentials());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\AuthenticationException
     * @dataProvider provideInvalidCredentials
     */
    public function it_raise_exception_when_extracted_identifier_is_invalid($credentials): void
    {
        $match = new IdentifierPasswordAuthenticationRequest('foo.bar');
        $request = Request::create('/', 'POST', $credentials);

        $match->extract($request);
    }

    public function provideInvalidCredentials(): array
    {
        return [
            [[]],
            [['identifier' => null, 'password' => null]],
            [['identifier' => '', 'password' => '']],
            [['identifier' => 'foo@bar.com', 'password' => '']],
            [['identifier' => '', 'password' => 'password']],
        ];
    }
}