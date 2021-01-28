<?php

declare(strict_types=1);

namespace Pollen\WpApp\Query;

use Pollen\WpApp\Support\Arr;
use Pollen\WpApp\Support\ParamsBag;
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
 */
class QueryTerm extends ParamsBag implements QueryTermInterface
{
    /**
     * Liste des classes de rappel d'instanciation selon la taxonomie.
     * @var string[][]|array
     */
    protected static $builtInClasses = [];

    /**
     * Liste des arguments de requête de récupération des éléments par défaut.
     * @var array
     */
    protected static $defaultArgs = [];

    /**
     * Classe de rappel d'instanciation
     * @var string|null
     */
    protected static $fallbackClass;

    /**
     * Nom de qualification de la taxonomie associée.
     * @var string
     */
    protected static $taxonomy = '';

    /**
     * Indice de récupération des éléments non associés.
     * @var bool
     */
    protected static $hideEmpty = false;

    /**
     * Instance de terme de taxonomie Wordpress.
     * @var WP_Term
     */
    protected $wpTerm;

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Term|null $wp_term Instance de terme de taxonomie Wordpress.
     *
     * @return void
     */
    public function __construct(?WP_Term $wp_term = null)
    {
        if ($this->wpTerm = $wp_term instanceof WP_Term ? $wp_term : null) {
            $this->set($this->wpTerm->to_array())->parse();
        }
    }

    /**
     * @inheritDoc
     */
    public static function build(object $wp_term): ?QueryTermInterface
    {
        if (!$wp_term instanceof WP_Term) {
            return null;
        }

        $classes = self::$builtInClasses;
        $taxonomy = $wp_term->taxonomy;

        $class = $classes[$taxonomy] ?? (self::$fallbackClass ?: static::class);

        return class_exists($class) ? new $class($wp_term) : new static($wp_term);
    }

    /**
     * @inheritDoc
     */
    public static function create($id = null, ...$args): ?QueryTermInterface
    {
        if (is_numeric($id)) {
            return static::createFromId((int)$id);
        } elseif (is_string($id)) {
            return static::createFromSlug($id, ...$args);
        } elseif ($id instanceof WP_Term) {
            return static::build($id);
        } elseif ($id instanceof QueryTermInterface) {
            return static::createFromId($id->getId());
        } elseif (is_null($id) && ($instance = static::createFromGlobal())) {
            if (($taxonomy = static::$taxonomy)) {
                return $instance->getTaxonomy() === $taxonomy ? $instance : null;
            } else {
                return $instance;
            }
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function createFromGlobal(): ?QueryTermInterface
    {
        global $wp_query;

        return $wp_query->is_tax() || $wp_query->is_category() || $wp_query->is_tag()
            ? self::createFromId($wp_query->queried_object_id) : null;
    }

    /**
     * @inheritDoc
     */
    public static function createFromId(int $term_id): ?QueryTermInterface
    {
        if ($term_id && ($wp_term = get_term($term_id)) && ($wp_term instanceof WP_Term)) {
            if (!$instance = static::build($wp_term)) {
                return null;
            } else {
                return $instance::is($instance) ? $instance : null;
            }
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public static function createFromSlug(string $term_slug, ?string $taxonomy = null): ?QueryTermInterface
    {
        $wp_term = get_term_by('slug', $term_slug, $taxonomy ?? static::$taxonomy);

        return ($wp_term instanceof WP_Term) ? static::createFromId($wp_term->term_id ?? 0) : null;
    }

    /**
     * @inheritDoc
     */
    public static function fetch($query): array
    {
        if (is_array($query)) {
            return static::fetchFromArgs($query);
        } elseif ($query instanceof WP_Term_Query) {
            return static::fetchFromWpTermQuery($query);
        } else {
            return [];
        }
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromArgs(array $args = []): array
    {
        return static::fetchFromWpTermQuery(new WP_Term_Query(static::parseQueryArgs($args)));
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromIds(array $ids): array
    {
        return static::fetchFromWpTermQuery(new WP_Term_Query(static::parseQueryArgs(['include' => $ids])));
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromWpTermQuery(WP_Term_Query $wp_term_query): array
    {
        $terms = $wp_term_query->get_terms();
        $per_page = $wp_term_query->query_vars['number'] ?: -1;
        $count = count($terms);
        $offset = $wp_term_query->query_vars['offset'] ?: 0;

        if ($per_page > 0) {
            $wp_term_query_count = new WP_Term_Query(array_merge($wp_term_query->query_vars, [
                'count'  => false,
                'number' => 0,
                'offset' => 0,
                'fields' => 'count',
            ]));

            $total = (int)$wp_term_query_count->get_terms();
            $pages = (int)ceil($total / $per_page);
            $page = (int)ceil(($offset + 1) / $per_page);
        } else {
            $pages = 1;
            $page = 1;
            $total = (int)count($terms);
        }

        $results = [];
        foreach ($terms as $wp_term) {
            $instance = static::createFromId($wp_term->term_id);

            if (($taxonomy = static::$taxonomy) && ($taxonomy !== 'any')) {
                if ($instance->taxIn($taxonomy)) {
                    $results[] = $instance;
                }
            } else {
                $results[] = $instance;
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public static function is($instance): bool
    {
        return $instance instanceof static &&
            ((($taxonomy = static::$taxonomy) && ($taxonomy !== 'any')) ? $instance->taxIn($taxonomy) : true);
    }

    /**
     * @inheritDoc
     */
    public static function parseQueryArgs(array $args = []): array
    {
        if ($taxonomy = static::$taxonomy) {
            $args['taxonomy'] = $taxonomy;
        }

        if (!isset($args['hide_empty'])) {
            $args['hide_empty'] = static::$hideEmpty;
        }

        return array_merge(static::$defaultArgs, $args);
    }

    /**
     * @inheritDoc
     */
    public static function setBuiltInClass(string $taxonomy, string $classname): void
    {
        if ($taxonomy === 'any') {
            self::setFallbackClass($classname);
        } else {
            self::$builtInClasses[$taxonomy] = $classname;
        }
    }

    /**
     * @inheritDoc
     */
    public static function setDefaultArgs(array $args): void
    {
        self::$defaultArgs = $args;
    }

    /**
     * @inheritDoc
     */
    public static function setFallbackClass(string $classname): void
    {
        self::$fallbackClass = $classname;
    }

    /**
     * @inheritDoc
     */
    public static function setTaxonomy(string $taxonomy): void
    {
        static::$taxonomy = $taxonomy;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return (string)$this->get('description', '');
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return intval($this->get('term_id', 0));
    }

    /**
     * @inheritDoc
     */
    public function getMeta(string $meta_key, bool $single = false, $default = null)
    {
        return get_term_meta($this->getId(), $meta_key, $single) ?: $default;
    }

    /**
     * @inheritDoc
     */
    public function getMetaMulti(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, false, $default);
    }

    /**
     * @inheritDoc
     */
    public function getMetaSingle(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, true, $default);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return (string)$this->get('name', '');
    }

    /**
     * @inheritDoc
     */
    public function getPermalink(): string
    {
        return get_term_link($this->getWpTerm());
    }

    /**
     * @inheritDoc
     */
    public function getSlug(): string
    {
        return (string)$this->get('slug', '');
    }

    /**
     * @inheritDoc
     */
    public function getTaxonomy(): string
    {
        return (string)$this->get('taxonomy', '');
    }

    /**
     * @inheritDoc
     */
    public function getWpTerm(): WP_Term
    {
        return $this->wpTerm;
    }

    /**
     * @inheritDoc
     */
    public function taxIn($taxonomies): bool
    {
        return in_array((string)$this->getTaxonomy(), Arr::wrap($taxonomies));
    }
}