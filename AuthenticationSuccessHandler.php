<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max107\Component\JsonAuthenticationHandler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var callable
     */
    private $generateTarget;

    /**
     * AuthenticationSuccessHandler constructor.
     *
     * @param callable $generateTarget
     */
    public function __construct(callable $generateTarget)
    {
        $this->generateTarget = $generateTarget;
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $redirectUrl = call_user_func($this->generateTarget);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'redirect_url' => $redirectUrl,
            ], 278);
        }
        // If the user tried to access a protected resource and was forces to login
        // redirect him back to that resource
        if ($request->hasSession() && $targetPath = $request->getSession()->get('_security.target_path')) {
            $url = $targetPath;
        } else {
            // Otherwise, redirect him to wherever you want
            $url = $redirectUrl;
        }

        return new RedirectResponse($url);
    }
}
