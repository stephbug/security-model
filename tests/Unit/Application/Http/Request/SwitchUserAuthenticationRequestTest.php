<?php

declare(strict_types=1);

namespace StephBugTest\SecurityModel\Unit\Application\Http\Request;

use Illuminate\Http\Request;
use StephBug\SecurityModel\Application\Http\Request\SwitchUserAuthenticationRequest;
use StephBug\SecurityModel\Application\Values\Contract\EmailIdentifier;
use StephBug\SecurityModel\Application\Values\Identifier\NullIdentifier;
use StephBugTest\SecurityModel\Unit\TestCase;

class SwitchUserAuthenticationRequestTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_if_it_exit_user_is_requested(): void
    {
        $match = new SwitchUserAuthenticationRequest();

        $request = Request::create('/foo/bar', 'GET', [SwitchUserAuthenticationRequest::EXIT_USER => null]);

        $this->assertTrue($match->matches($request));
    }

    /**
     * @test
     */
    public function it_check_if_impersonated_user_is_requested(): void
    {
        $match = new SwitchUserAuthenticationRequest();

        $request = Request::create('/foo/bar', 'GET', [SwitchUserAuthenticationRequest::IDENTIFIER_PARAMETER => 'foobar']);

        $this->assertTrue($match->matches($request));
    }

    /**
     * @test
     */
    public function it_extract_null_identifier_if_exit_user_is_requested(): void
    {
        $match = new SwitchUserAuthenticationRequest();

        $request = Request::create('/foo/bar', 'GET', [SwitchUserAuthenticationRequest::EXIT_USER => null]);

        $this->assertTrue($match->matches($request));

        $this->assertInstanceOf(NullIdentifier::class, $match->extract($request));
    }

    /**
     * @test
     */
    public function it_extract_identifier_if_impersonated_user_is_requested(): void
    {
        $match = new SwitchUserAuthenticationRequest();

        $request = Request::create(
            '/foo/bar', 'GET', [SwitchUserAuthenticationRequest::IDENTIFIER_PARAMETER => 'foo@bar.com']);

        $this->assertTrue($match->matches($request));

        $this->assertInstanceOf(EmailIdentifier::class, $match->extract($request));
        $this->assertEquals('foo@bar.com', $match->extract($request)->identify());
    }

    /**
     * @test
     * @expectedException \StephBug\SecurityModel\Application\Exception\AuthenticationException
     * @dataProvider provideInvalidCredentials
     */
    public function it_raise_exception_if_impersonated_identifier_is_invalid($credentials): void
    {
        $match = new SwitchUserAuthenticationRequest();

        $request = Request::create(
            '/foo/bar', 'GET', [SwitchUserAuthenticationRequest::IDENTIFIER_PARAMETER => $credentials]);

        $match->extract($request);
    }

    public function provideInvalidCredentials(): array
    {
        return [[null], [''], ['foobar'],];
    }
}