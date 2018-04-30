<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HypeCorp\Component\JsonAuthenticationHandler\Tests;

use HypeCorp\Component\JsonAuthenticationHandler\AuthenticationFailureHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Translation\TranslatorInterface;

class AuthenticationFailureHandlerTest extends TestCase
{
    public function testOnAuthenticationFailure()
    {
        $generateTarget = function () {
            return '/';
        };

        $translatorMock = $this->createMock(TranslatorInterface::class);
        $exception = new AuthenticationException('test');

        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $handler = new AuthenticationFailureHandler($generateTarget, $translatorMock);
        $response = $handler->onAuthenticationFailure($request, $exception);
        $this->assertSame(302, $response->getStatusCode());

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $response = $handler->onAuthenticationFailure($request, $exception);
        $this->assertSame(400, $response->getStatusCode());
    }
}
