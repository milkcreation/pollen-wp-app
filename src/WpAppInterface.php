<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Pollen\WpApp\Http\RequestInterface;
use Pollen\WpApp\Routing\RouterInterface;
use Pollen\WpApp\Post\PostQueryInterface;
use Pollen\WpApp\Term\TermQueryInterface;
use Pollen\WpApp\User\UserQueryInterface;
use Pollen\WpApp\User\UserRoleManagerInterface;
use Pollen\WpApp\Validation\ValidatorInterface;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use WP_Post;
use WP_Query;
use WP_Term;
use WP_Term_Query;
use WP_User;
use WP_User_Query;

/**
 * @mixin \Pollen\WpApp\Container\Container
 * @mixin \Pollen\WpApp\Support\Concerns\BootableTrait
 * @mixin \Pollen\WpApp\Support\Concerns\ConfigBagTrait
 */
interface WpAppInterface
{
    /**
     * Récupération de l'instance.
     *
     * @return static
     */
    public static function instance(): WpAppInterface;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): WpAppInterface;

    /**
     * Instance du post courant ou associé à une définition.
     *
     * @param string|int|WP_Post|null $post
     *
     * @return PostQueryInterface|null
     */
    public function post($post = null): ?PostQueryInterface;

    /**
     * Liste des instances de posts courants ou associés à une requête WP_Query ou associés à une liste d'arguments.
     *
     * @param WP_Query|array|null $query
     *
     * @return PostQueryInterface[]|array
     */
    public function posts($query = null): array;

    /**
     * Instance de la requête HTTP globale au format PSR-7.
     *
     * @return PsrRequest|null
     */
    public function psrRequest(): ?PsrRequest;

    /**
     * Instance de la requête HTTP globale.
     *
     * @return RequestInterface|null
     */
    public function request(): ?RequestInterface;

    /**
     * Instance du gestionnaire de rôle utilisateurs.
     *
     * @return UserRoleManagerInterface|null
     */
    public function role(): ?UserRoleManagerInterface;

    /**
     * Instance du gestionnaire de routage.
     *
     * @return RouterInterface|null
     */
    public function router(): ?RouterInterface;

    /**
     * Instance du terme de taxonomie courant ou associé à une définition.
     *
     * @param string|int|WP_Term|null $term
     *
     * @return TermQueryInterface|null
     */
    public function term($term = null): ?TermQueryInterface;

    /**
     * Liste des instances de termes de taxonomie associés à une requête WP_Term_Query ou une liste d'arguments.
     *
     * @param WP_Term_Query|array $query
     *
     * @return TermQueryInterface[]|array
     */
    public function terms($query): array;

    /**
     * Instance de l'utilisateur courant ou associé à une definition.
     *
     * @param string|int|WP_User|null $id
     *
     * @return UserQueryInterface|null
     */
    public function user($id = null): ?UserQueryInterface;

    /**
     * Liste des instances d'utilisateurs associés à une requête WP_User_Query ou une liste d'arguments.
     *
     * @param WP_User_Query|array $query
     *
     * @return UserQueryInterface[]|array
     */
    public function users($query): array;

    /**
     * Instance du gestionnaire de validation.
     *
     * @return ValidatorInterface|null
     */
    public function validator(): ?ValidatorInterface;
}