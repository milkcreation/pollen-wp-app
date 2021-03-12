<?php

declare(strict_types=1);

namespace Pollen\WpApp;

use Pollen\Asset\AssetManagerInterface;
use Pollen\Container\ContainerInterface;
use Pollen\Cookie\CookieInterface;
use Pollen\Cookie\CookieJarInterface;
use Pollen\Debug\DebugManagerInterface;
use Pollen\Event\EventDispatcherInterface;
use Pollen\Field\FieldDriverInterface;
use Pollen\Field\FieldManagerInterface;
use Pollen\Filesystem\FilesystemInterface;
use Pollen\Filesystem\StorageManagerInterface;
use Pollen\Form\FormManagerInterface;
use Pollen\Form\FormInterface;
use Pollen\Log\LogManagerInterface;
use Pollen\Partial\PartialDriverInterface;
use Pollen\Partial\PartialManagerInterface;
use Pollen\Routing\RouterInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Validation\ValidatorInterface;
use Pollen\WpHook\WpHookerInterface;
use Pollen\WpHook\WpHookableInterface;
use Pollen\WpPost\WpPostQueryInterface;
use Pollen\WpTaxonomy\WpTermQueryInterface;
use Pollen\WpUser\WpUserQueryInterface;
use Pollen\WpUser\WpUserRoleManagerInterface;
use WP_Post;
use WP_Query;
use WP_Term;
use WP_Term_Query;
use WP_User;
use WP_User_Query;

interface WpAppInterface extends BootableTraitInterface, ConfigBagAwareTraitInterface, ContainerInterface
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
     * Instance du gestionnaire d'assets.
     *
     * @return AssetManagerInterface
     */
    public function asset(): AssetManagerInterface;

    /**
     * Instance du gestionnaire d'instance de cookies|Instance d'un cookie.
     *
     * @param string|null $alias
     * @param array $args
     *
     * @return CookieJarInterface|CookieInterface
     */
    public function cookie(?string $alias = null, array $args = []);

    /**
     * Instance du gestionnaire de débogage.
     *
     * @return DebugManagerInterface
     */
    public function debug(): DebugManagerInterface;

    /**
     * Décryptage d'une chaîne de caractères.
     *
     * @param string $hash
     *
     * @return string
     */
    public function decrypt(string $hash): string;

    /**
     * Encryptage d'une chaîne de caractères.
     *
     * @param string $plain
     *
     * @return string
     */
    public function encrypt(string $plain): string;

    /**
     * Instance du répartiteur d'événements.
     *
     * @return EventDispatcherInterface
     */
    public function event(): EventDispatcherInterface;

    /**
     * Récupération du gestionnaire de champ ou instance d'un champ déclaré selon son alias.
     *
     * @param string|null $alias Alias de qualification|null pour l'instance du gestionnaire.
     * @param mixed $idOrParams Identifiant de qualification|Liste des attributs de configuration.
     * @param array $params Liste des attributs de configuration.
     *
     * @return FieldManagerInterface|FieldDriverInterface|null
     */
    public function field(?string $alias = null, $idOrParams = null, array $params = []);

    /**
     * Récupération du gestionnaire de formulaire ou instance d'un formulaire déclaré.
     *
     * @param string|null $alias Alias de qualification du formulaire.
     *
     * @return FormManagerInterface|FormInterface|null
     */
    public function form(?string $alias = null);

    /**
     * Récupération du gestionnaire de contenu d'accroche|Instance d'un contenu d'accroche déclaré.
     *
     * @param string|null $hook
     *
     * @return WpHookerInterface|WpHookableInterface|null
     */
    public function hook(?string $hook = null);

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
     * Récupération du gestionnaire de portions d'affichage ou instance d'une portion d'affichage déclarée selon son
     * alias.
     *
     * @param string|null $alias Alias de qualification|null pour l'instance du gestionnaire.
     * @param mixed $idOrParams Identifiant de qualification|Liste des attributs de configuration.
     * @param array $params Liste des attributs de configuration.
     *
     * @return PartialManagerInterface|PartialDriverInterface|null
     */
    public function partial(?string $alias = null, $idOrParams = null, array $params = []);

    /**
     * Instance du post courant ou associé à une définition.
     *
     * @param string|int|WP_Post|null $post
     *
     * @return WpPostQueryInterface|null
     */
    public function post($post = null): ?WpPostQueryInterface;

    /**
     * Liste des instances de posts courants ou associés à une requête WP_Query ou associés à une liste d'arguments.
     *
     * @param WP_Query|array|null $query
     *
     * @return WpPostQueryInterface[]|array
     */
    public function posts($query = null): array;

    /**
     * Instance du gestionnaire de rôle utilisateurs.
     *
     * @return WpUserRoleManagerInterface
     */
    public function role(): WpUserRoleManagerInterface;

    /**
     * Instance du gestionnaire de routage.
     *
     * @return RouterInterface
     */
    public function router(): RouterInterface;

    /**
     * Récupération du gestionnaire des système de fichiers ou instance d'un système de fichier déclaré.
     *
     * @param string|null $name
     *
     * @return StorageManagerInterface|FilesystemInterface|null
     */
    public function storage(?string $name = null);

    /**
     * Instance du terme de taxonomie courant ou associé à une définition.
     *
     * @param string|int|WP_Term|null $term
     *
     * @return WpTermQueryInterface|null
     */
    public function term($term = null): ?WpTermQueryInterface;

    /**
     * Liste des instances de termes de taxonomie associés à une requête WP_Term_Query ou une liste d'arguments.
     *
     * @param WP_Term_Query|array $query
     *
     * @return WpTermQueryInterface[]|array
     */
    public function terms($query): array;

    /**
     * Instance de l'utilisateur courant ou associé à une definition.
     *
     * @param string|int|WP_User|null $id
     *
     * @return WpUserQueryInterface|null
     */
    public function user($id = null): ?WpUserQueryInterface;

    /**
     * Liste des instances d'utilisateurs associés à une requête WP_User_Query ou une liste d'arguments.
     *
     * @param WP_User_Query|array $query
     *
     * @return WpUserQueryInterface[]|array
     */
    public function users($query): array;

    /**
     * Instance du gestionnaire de validation.
     *
     * @return ValidatorInterface
     */
    public function validator(): ValidatorInterface;
}