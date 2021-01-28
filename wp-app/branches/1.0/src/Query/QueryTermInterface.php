<?php

declare(strict_types=1);

namespace Pollen\WpApp\Query;

use WP_Term;
use WP_Term_Query;

/**
 * @property-read int $term_id
 * @property-read string $name
 * @property-read string $slug
 * @property-read string $term_group
 * @property-read int $term_taxonomy_id
 * @property-read string $taxonomy
 * @property-read string $description
 * @property-read int $parent
 * @property-read int $count
 * @property-read string $filter
 *
 * @mixin \Pollen\WpApp\Support\ParamsBag
 */
interface QueryTermInterface
{
    /**
     * Création d'une instance basée sur un objet post Wordpress et selon la cartographie des classes de rappel.
     *
     * @param WP_Term|object $wp_term
     *
     * @return static
     */
    public static function build(object $wp_term): ?QueryTermInterface;

    /**
     * Création d'un instance basée sur un argument de qualification.
     *
     * @param int|string|WP_Term $id
     * @param array ...$args Liste des arguments de qualification complémentaires.
     *
     * @return static|null
     */
    public static function create($id = null, ...$args): ?QueryTermInterface;

    /**
     * Récupération d'une instance basée sur le terme global courant.
     *
     * @return static|null
     */
    public static function createFromGlobal(): ?QueryTermInterface;

    /**
     * Récupération d'une instance basée sur l'identifiant de qualification du terme.
     *
     * @param int $term_id Identifiant de qualification.
     *
     * @return static|null
     */
    public static function createFromId(int $term_id): ?QueryTermInterface;

    /**
     * Récupération d'une instance basée sur le nom de qualification du terme.
     *
     * @param string $term_slug
     * @param string|null $taxonomy Nom de qualification de la taxonomie associée.
     *
     * @return static|null
     */
    public static function createFromSlug(string $term_slug, ?string $taxonomy = null): ?QueryTermInterface;

    /**
     * Récupération d'une liste des instances des termes courants|selon une requête WP_Term_Query|selon une liste d'arguments.
     *
     * @param WP_Term_Query|array $query
     *
     * @return QueryTermInterface[]|array
     */
    public static function fetch($query): array;

    /**
     * Récupération d'une liste d'instances basée sur des arguments de requête de récupération des éléments.
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/
     *
     * @param array $args Liste des arguments de la requête récupération des éléments.
     *
     * @return QueryTermInterface[]|array
     */
    public static function fetchFromArgs(array $args = []): array;

    /**
     * Récupération d'une liste d'instances basée sur des identifiants de qualification de termes.
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/
     *
     * @param int[] $ids Liste des identifiants de qualification.
     *
     * @return QueryTermInterface[]|array
     */
    public static function fetchFromIds(array $ids): array;

    /**
     * Récupération d'une liste d'instances basée sur une instance de classe WP_Term_Query.
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/
     *
     * @param WP_Term_Query $wp_term_query
     *
     * @return QueryTermInterface[]|array
     */
    public static function fetchFromWpTermQuery(WP_Term_Query $wp_term_query): array;

    /**
     * Vérification d'intégrité d'une instance.
     *
     * @param static|object $instance
     *
     * @return bool
     */
    public static function is($instance): bool;

    /**
     * Traitement d'arguments de requête de récupération des éléments.
     *
     * @param array $args Liste des arguments de la requête récupération des éléments.
     *
     * @return array
     */
    public static function parseQueryArgs(array $args = []) : array;

    /**
     * Définition d'une classe de rappel d'instanciation selon un type de post.
     *
     * @param string $taxonomy Nom de qualification du type de post associé.
     * @param string $classname Nom de qualification de la classe.
     *
     * @return void
     */
    public static function setBuiltInClass(string $taxonomy, string $classname): void;

    /**
     * Définition de la liste des arguments de requête de récupération des éléments.
     *
     * @param array $args
     *
     * @return void
     */
    public static function setDefaultArgs(array $args): void;

    /**
     * Définition de la classe de rappel par défaut.
     *
     * @param string $classname Nom de qualification de la classe.
     *
     * @return void
     */
    public static function setFallbackClass(string $classname): void;

    /**
     * Définition de la taxonomie associée.
     *
     * @param string $taxonomy
     *
     * @return void
     */
    public static function setTaxonomy(string $taxonomy): void;

    /**
     * Récupération de la description.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Récupération de l'identifiant de qualification Wordpress du terme.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Récupération d'une metadonnée.
     *
     * @param string $meta_key Clé d'indexe de la metadonnée à récupérer
     * @param bool $single Type de metadonnés. single (true)|multiple (false). false par défaut.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getMeta(string $meta_key, bool $single = false, $default = null);

    /**
     * Récupération d'une metadonnée de type multiple.
     *
     * @param string $meta_key Clé d'indexe de la metadonnée à récupérer
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getMetaMulti(string $meta_key, $default = null);

    /**
     * Récupération d'une metadonnée de type simple.
     *
     * @param string $meta_key Clé d'indexe de la metadonnée à récupérer
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getMetaSingle(string $meta_key, $default = null);

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération du permalien d'affichage de la liste de élément associés au terme.
     *
     * @return string
     */
    public function getPermalink(): string;

    /**
     * Récupération du nom de qualification Wordpress du terme.
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * Récupération de la taxonomie relative.
     *
     * @return string
     */
    public function getTaxonomy(): string;

    /**
     * Récupération de l'object Terme Wordpress associé.
     *
     * @return WP_Term
     */
    public function getWpTerm(): WP_Term;

    /**
     * Vérification de correspondance de taxonomies.
     *
     * @param array|string $taxonomies Taxonomie(s) en correspondances.
     *
     * @return bool
     */
    public function taxIn($taxonomies): bool;
}