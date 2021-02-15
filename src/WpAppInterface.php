<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Pollen\Event\EventDispatcherInterface;
use Pollen\Log\LogManagerInterface;
use Pollen\Partial\PartialDriverInterface;
use Pollen\Partial\PartialInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Validation\ValidatorInterface;
use Pollen\WpApp\Post\PostQueryInterface;
use Pollen\WpApp\Term\TermQueryInterface;
use Pollen\WpApp\User\UserQueryInterface;
use Pollen\WpApp\User\UserRoleManagerInterface;
use WP_Post;
use WP_Query;
use WP_Term;
use WP_Term_Query;
use WP_User;
use WP_User_Query;

/**
 * @mixin \Pollen\Container\Container
 * @mixin \Pollen\Support\Concerns\BootableTrait
 * @mixin \Pollen\Support\Concerns\ConfigBagTrait
 */
interface WpAppInterface
{
    /**
     * Récupération de l'instance courante.
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
     * Initialisation du conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function bootContainer(): void;

    /**
     * Instance du répartiteur d'événements.
     *
     * @return EventDispatcherInterface
     */
    public function event(): EventDispatcherInterface;

    /**
     * Instance du gestionnaire de journalisation|Journalisation d'un événement.
     *
     * @param string|null $message
     * @param string|int|null $level
     * @param array $context
     * @param string|null $channel
     *
     * @return LogManagerInterface|null
     */
    public function log(
        ?string $message = null,
        $level = null,
        array $context = [],
        ?string $channel = null
    ): ?LogManagerInterface;

    /**
     * Récupération du gestionnaire de partial ou instance de partial déclaré selon son alias.
     *
     * @param string|null $alias Alias de qualification|null pour l'instance du gestionnaire.
     * @param mixed $idOrParams Identifiant de qualification|Liste des attributs de configuration.
     * @param array $params Liste des attributs de configuration.
     *
     * @return PartialInterface|PartialDriverInterface|null
     */
    public function partial(?string $alias = null, $idOrParams = null, array $params = []);

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
     * Instance du gestionnaire de rôle utilisateurs.
     *
     * @return UserRoleManagerInterface
     */
    public function role(): UserRoleManagerInterface;

    /**
     * Instance du gestionnaire de routage.
     *
     * @return RouterInterface
     */
    public function router(): RouterInterface;

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
     * @return ValidatorInterface
     */
    public function validator(): ValidatorInterface;
}