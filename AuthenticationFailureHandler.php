<?php

declare(strict_types=1);

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HypeCorp\Component\JsonAuthenticationHandler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class AuthenticationFailureHandler implements AuthenticationFailureHandlerInterface
{
    /**
     * @var callable
     */
    private $generateTarget;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * AuthenticationFailureHandler constructor.
     *
     * @param callable            $generateTarget
     * @param TranslatorInterface $translator
     */
    public function __construct(
        callable $generateTarget,
        TranslatorInterface $translator
    ) {
        $this->generateTarget = $generateTarget;
        $this->translator = $translator;
    }

    /**
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'errors' => [
                    '_username' => [
                        $this->translator->trans($exception->getMessage()),
                    ],
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        return new RedirectResponse(call_user_func($this->generateTarget));
    }
}
