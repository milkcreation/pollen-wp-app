<?php

declare(strict_types=1);

namespace Pollen\WpApp\Middleware;

use Pollen\Routing\Exception\ForbiddenException;
use Pollen\Support\Exception\ProxyThrowable;
use Pollen\WpUser\WpUserProxy;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Psr\Http\Server\RequestHandlerInterface;
use Pollen\Routing\BaseMiddleware;

class WpAdminMiddleware extends BaseMiddleware
{
    use WpUserProxy;

    /**
     * Liste des rôles autorisés.
     * @var string[]
     */
    protected $allowedRoles = ['editor', 'administrator', 'author'];

    /**
     * @param array $allowedRoles
     */
    public function __construct(array $allowedRoles = [])
    {
        if (!empty($allowedRoles)) {
            $this->setAllowedRoles($allowedRoles);
        }
    }

    /**
     * @inheritDoc
     *
     * @throws ForbiddenException
     */
    public function process(PsrRequest $request, RequestHandlerInterface $handler): PsrResponse
    {
        try {
            $user = $this->wpUser(true);
        } catch(ProxyThrowable $e) {
            $user = null;
        }

        if (is_user_logged_in() && $user->roleIn('administrator')) {
           return $handler->handle($request);
        }

        throw new ForbiddenException('Sorry, this user is not allowed.', 'User not allowed');
    }

    /**
     * Définition de la liste des rôles autorisés.
     *
     * @param array $allowedRoles
     *
     * @return $this
     */
    public function setAllowedRoles(array $allowedRoles): self
    {
        $this->allowedRoles = $allowedRoles;

        return $this;
    }
}