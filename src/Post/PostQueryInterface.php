<?php

declare(strict_types=1);

namespace Pollen\WpApp\Post;

use Pollen\Support\DateTime;
use Pollen\Support\ParamsBagInterface;
use Pollen\WpApp\Term\TermQueryInterface;
use Pollen\WpApp\User\UserQueryInterface;
use WP_Post;
use WP_Query;
use WP_Term;

/**
 * @property-read int $ID
 * @property-read int $post_author
 * @property-read string $post_date
 * @property-read string $post_date_gmt
 * @property-read string $post_content
 * @property-read string $post_title
 * @property-read string $post_excerpt
 * @property-read string $post_status
 * @property-read string $comment_status
 * @property-read string $ping_status
 * @property-read string $post_password
 * @property-read string $post_name
 * @property-read string $to_ping
 * @property-read string $pinged
 * @property-read string $post_modified
 * @property-read string $post_modified_gmt
 * @property-read string $post_content_filtered
 * @property-read int $post_parent
 * @property-read string $guid
 * @property-read int $menu_order
 * @property-read string $post_type
 * @property-read string $post_mime_type
 * @property-read int $comment_count
 * @property-read string $filter
 */
interface PostQueryInterface extends ParamsBagInterface
{
    /**
     * Création d'une instance basée sur un objet post Wordpress et selon la cartographie des classes de rappel.
     *
     * @param WP_Post|object $wp_post
     *
     * @return static
     */
    public static function build(object $wp_post): ?PostQueryInterface;

    /**
     * Création d'une instance basée sur un argument de qualification.
     *
     * @param int|string|WP_Post|null $id
     * @param array ...$args Liste des arguments de qualification complémentaires.
     *
     * @return static|null
     */
    public static function create($id = null, ...$args): ?PostQueryInterface;

    /**
     * Récupération d'une instance basée sur le post global courant.
     *
     * @return static|null
     */
    public static function createFromGlobal(): ?PostQueryInterface;

    /**
     * Récupération d'une instance basée sur l'identifiant de qualification d'un post.
     *
     * @param int $post_id Identifiant de qualification.
     *
     * @return static|null
     */
    public static function createFromId(int $post_id): ?PostQueryInterface;

    /**
     * Récupération d'une instance basée sur le nom de qualification d'un post.
     *
     * @param string $post_name Nom de qualification
     *
     * @return static|null
     */
    public static function createFromName(string $post_name): ?PostQueryInterface;

    /**
     * Récupération d'une instance basée sur une liste de données de post.
     *
     * @param array $postdata Liste des données de post. ID requis.
     *
     * @return static|null
     */
    public static function createFromPostdata(array $postdata): ?PostQueryInterface;

    /**
     * Récupération d'une liste d'instances des posts courants|selon une requête WP_Query|selon une liste d'arguments.
     *
     * @param WP_Query|array|null $query
     *
     * @return PostQuery[]|array
     */
    public static function fetch($query = null): array;

    /**
     * Récupération d'une liste d'instances basée sur des arguments de requête de récupération des éléments.
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @param array $args Liste des arguments de la requête récupération des éléments.
     *
     * @return array
     */
    public static function fetchFromArgs(array $args = []): array;

    /**
     * Récupération d'une liste d'instances basée sur la requête de récupération globale.
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @return array
     */
    public static function fetchFromGlobal(): array;

    /**
     * Récupération d'une liste d'instances basée sur des identifiants de qualification de posts.
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @param int[] $ids Liste des identifiants de qualification.
     *
     * @return array
     */
    public static function fetchFromIds(array $ids): array;

    /**
     * Récupération d'une liste d'instances basée sur une instance de classe WP_Query.
     * @see https://developer.wordpress.org/reference/classes/wp_query/
     *
     * @param WP_Query $wp_query
     *
     * @return array
     */
    public static function fetchFromWpQuery(WP_Query $wp_query): array;

    /**
     * Vérification d'intégrité d'une instance.
     *
     * @param PostQueryInterface|object $instance
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
    public static function parseQueryArgs(array $args = []): array;

    /**
     * Définition d'une classe de rappel d'instanciation selon un type de post.
     *
     * @param string $post_type Nom de qualification du type de post associé.
     * @param string $classname Nom de qualification de la classe.
     *
     * @return void
     */
    public static function setBuiltInClass(string $post_type, string $classname): void;

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
     * Définition du type de post ou une liste de type de posts associés.
     *
     * @param string|array $post_type
     *
     * @return void
     */
    public static function setPostType($post_type): void;

    /**
     * Url de la page d'archive associé.
     *
     * @return string|null
     */
    public function getArchiveUrl(): ?string;

    /**
     * Récupération de l'identifiant de qualification de l'auteur original.
     *
     * @return int
     */
    public function getAuthorId(): int;

    /**
     * Récupération du texte précédent la balise <!--more-->.
     *
     * @return string|null
     */
    public function getBeforeMore(): ?string;

    /**
     * Récupération de la liste des instance des enfants
     *
     * @param int|null $per_page Nombre d'élément par page. défaut -1. Si null utilise lé réglage posts_per_page.
     * @param int $page Page courante.
     * @param array $args Liste des arguments de requête complémentaires.
     *
     * @return static[]
     */
    public function getChildren(?int $per_page = -1, int $page = 1, array $args = []): array;

    /**
     * Récupération de la liste des classes HTML associées.
     *
     * @param string[] $classes Liste de classes complémentaires.
     * @param bool $html Activation du format de sortie de l'attribut de balise class. ex. class="post"
     *
     * @return string
     */
    public function getClass(array $classes = [], bool $html = false): string;

    /**
     * Récupération de la liste des classes HTML associées.
     *
     * @param string[] $classes Liste de classes complémentaires.
     *
     * @return string|array
     */
    public function getClasses(array $classes = []): array;

    /**
     * Récupération du contenu de description.
     *
     * @param bool $raw Formatage de la valeur.
     *
     * @return string
     */
    public function getContent(bool $raw = false): string;

    /**
     * Récupération de la date de création au format datetime.
     *
     * @param bool $gmt Activation de la valeur basée sur le temps moyen de Greenwich.
     *
     * @return string
     */
    public function getDate(bool $gmt = false): string;

    /**
     * Récupération de l'objet DateTime basée sur la date création.
     *
     * @param bool $gmt Activation de la valeur basée sur le temps moyen de Greenwich.
     *
     * @return DateTime
     */
    public function getDateTime(bool $gmt = false): DateTime;

    /**
     * Récupération du lien d'édition du post dans l'interface administrateur.
     *
     * @return string
     */
    public function getEditUrl(): string;

    /**
     * Récupération de la valeur brute ou formatée de l'extrait.
     *
     * @param bool $raw Formatage de la valeur.
     *
     * @return string
     */
    public function getExcerpt(bool $raw = false): string;

    /**
     * Récupération de l'identifiant unique de qualification global.
     * {@internal Ne devrait pas être utilisé en tant que lien.}
     * @see https://developer.wordpress.org/reference/functions/the_guid/
     *
     * @return string
     */
    public function getGuid(): string;

    /**
     * Récupération de l'identifiant de qualification Wordpress du post.
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
     * Récupération de la liste des indices de métadonnées.
     *
     * @return array
     */
    public function getMetaKeys(): array;

    /**
     * Récupération d'une metadonnée de type multiple.
     *
     * @param string $meta_key Clé d'indexe de la metadonnée à récupérer
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return string|array|mixed
     */
    public function getMetaMulti(string $meta_key, $default = null);

    /**
     * Récupération d'une metadonnée de type simple.
     *
     * @param string $meta_key Clé d'indexe de la metadonnée à récupérer
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return string|array|mixed
     */
    public function getMetaSingle(string $meta_key, $default = null);

    /**
     * Récupération de la date de la dernière modification au format datetime.
     *
     * @param bool $gmt Activation de la valeur basée sur le temps moyen de Greenwich.
     *
     * @return string
     */
    public function getModified(bool $gmt = false): string;

    /**
     * Récupération de l'objet DateTime basée sur la date de modification.
     *
     * @param bool $gmt Activation de la valeur basée sur le temps moyen de Greenwich.
     *
     * @return DateTime
     */
    public function getModifiedDateTime(bool $gmt = false): DateTime;

    /**
     * Alias de récupération de l'identifiant de qualification Wordpress (post_name).
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de l'instance tiFy du produit parent.
     *
     * @return static|null
     */
    public function getParent(): ?PostQueryInterface;

    /**
     * Récupération de l'identifiant de qualification du post parent relatif.
     *
     * @return int
     */
    public function getParentId(): int;

    /**
     * Récupération du chemin relatif vers l'affichage du post dans l'interface utilisateur.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Récupération de l'instance de l'auteur associé.
     *
     * @return UserQueryInterface|null
     */
    public function getQueriedAuthor(): ?UserQueryInterface;

    /**
     * Récupération de l'instance de l'image représentative associée.
     *
     * @return PostQueryInterface|null
     */
    public function getQueriedThumbnail(): ?PostQueryInterface;

    /**
     * Récupération de la liste des termes d'une taxonomie associée.
     *
     * @param string|array $taxonomy Liste ou Nom de qualification de la taxonomie.
     * @param array $args Liste des arguments de récupération.
     *
     * @return TermQueryInterface[]|array
     */
    public function getQueriedTerms($taxonomy, array $args = []): array;

    /**
     * Récupération du permalien d'affichage du post dans l'interface utilisateur.
     *
     * @return string
     */
    public function getPermalink(): string;

    /**
     * Récupération de l'identifiant de qualification Wordpress (post_name).
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * Récupération d'un contenu d'accroche basé sur l'extrait.
     *
     * @param int $length Nombre maximum de caractères de la chaîne.
     * @param string $teaser Délimiteur de fin de chaîne réduite (defaut : [...]).
     * @param boolean $use_tag Détection d'une balise d'arrêt du type <!--more-->.
     * @param boolean $uncut Préservation de la découpe de mots en fin de chaîne.
     *
     * @return string
     */
    public function getTeaser(
        int $length = 255,
        string $teaser = ' [&hellip;]',
        bool $use_tag = true,
        bool $uncut = true
    ): string;

    /**
     * Récupération de la liste des termes de taxonomie.
     *
     * @param string|array $taxonomy Liste ou Nom de qualification de la taxonomie.
     * @param array $args Liste des arguments de récupération
     *
     * @return WP_Term[]|array
     */
    public function getTerms($taxonomy, array $args = []): array;

    /**
     * Récupération l'identifiant de qualification de l'image représentative.
     *
     * @return int
     */
    public function getThumbnailId(): int;

    /**
     * Récupération de l'image représentative.
     *
     * @param string|array $size Taille d'image déclaré|Tableau indexé [hauteur, largeur].
     * @param array $attrs Liste des attributs HTML de la balise img
     *
     * @return string
     */
    public function getThumbnail($size = 'post-thumbnail', array $attrs = []): string;

    /**
     * Récupération de l'url de l'image représentative.
     *
     * @param string|array $size Taille d'image déclaré|Tableau indexé [hauteur, largeur].
     *
     * @return string
     */
    public function getThumbnailSrc($size = 'post-thumbnail'): string;

    /**
     * Récupération de la valeur brute ou formatée de l'intitulé de qualification.
     *
     * @param bool $raw Formatage de la valeur.
     *
     * @return string
     */
    public function getTitle(bool $raw = false): string;

    /**
     * Récupération du type de post.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Récupération de l'instance de post Wordpress associée.
     *
     * @return WP_Post|null
     */
    public function getWpPost(): ?WP_Post;

    /**
     * Vérifie le balisage de suite de lecture est actif.
     *
     * @return bool
     */
    public function hasMore(): bool;

    /**
     * Vérification d'existance de terme(s) de taxonomie pour le post associé.
     *
     * @param string|int|array Nom de qualification|Identifiant de qualification|Slug du terme ou liste de terme.
     * @param string $taxonomy Nom de qualification de la taxonomie.
     *
     * @return boolean
     */
    public function hasTerm($term, string $taxonomy): bool;

    /**
     * Vérifie si le post est habilité à être parent.
     *
     * @return bool
     */
    public function isHierarchical(): bool;

    /**
     * Vérification de correspondance de type de post.
     *
     * @param array|string $post_types Type(s) de post en correspondance.
     *
     * @return bool
     */
    public function typeIn($post_types): bool;
}