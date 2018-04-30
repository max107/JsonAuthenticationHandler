<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HypeCorp\Component\JsonAuthenticationHandler\Tests;

use HypeCorp\Component\JsonAuthenticationHandler\AuthenticationSuccessHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AuthenticationSuccessHandlerTest extends TestCase
{
    public function testOnAuthenticationFailure()
    {
        $tokenMock = $this->createMock(TokenInterface::class);
        $generateTarget = function () {
            return '/';
        };

        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);
        $request->getSession()->set('_security.target_path', '/');

        $handler = new AuthenticationSuccessHandler($generateTarget);
        $response = $handler->onAuthenticationSuccess($request, $tokenMock);
        $this->assertSame(302, $response->getStatusCode());

        $request->getSession()->remove('_security.target_path');
        $handler = new AuthenticationSuccessHandler($generateTarget);
        $response = $handler->onAuthenticationSuccess($request, $tokenMock);
        $this->assertSame(302, $response->getStatusCode());

        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $response = $handler->onAuthenticationSuccess($request, $tokenMock);
        $this->assertSame(278, $response->getStatusCode());
    }
}
