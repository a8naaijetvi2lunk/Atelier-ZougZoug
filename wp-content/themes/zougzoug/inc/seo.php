<?php
/**
 * SEO / GEO — Meta tags, Open Graph, Schema.org JSON-LD, Sitemap, Robots
 *
 * Handles all SEO output for the Atelier ZougZoug theme:
 *  - Meta description, canonical, Open Graph, Twitter Card
 *  - Schema.org: Organization, Person, BreadcrumbList, Course, Event, FAQPage
 *  - Custom XML sitemap
 *  - Custom robots.txt
 *  - Document title override
 *
 * @package Zougzoug
 */

/* =========================================================================
   A) Helper functions
   ========================================================================= */

/**
 * Detect the current page and return its slug, data, and SEO block.
 *
 * @return array|null ['slug' => string, 'data' => array, 'seo' => array] or null
 */
function zz_seo_get_current_page_data() {
	$map = [
		'home'           => 'home',
		'a-propos'       => 'about',
		'contact'        => 'contact',
		'cours'          => 'cours',
		'revendeurs'     => 'revendeurs',
		'collaborations' => 'collaborations',
	];

	$slug = null;
	$json_slug = null;

	if ( is_front_page() ) {
		$slug      = 'home';
		$json_slug = 'home';
	} else {
		foreach ( $map as $page_slug => $data_slug ) {
			if ( $page_slug === 'home' ) {
				continue;
			}
			if ( is_page( $page_slug ) ) {
				$slug      = $page_slug;
				$json_slug = $data_slug;
				break;
			}
		}
	}

	if ( ! $slug ) {
		return null;
	}

	$data = zz_get_data( $json_slug );
	$seo  = isset( $data['seo'] ) ? $data['seo'] : [];

	return [
		'slug' => $slug,
		'data' => $data,
		'seo'  => $seo,
	];
}

/**
 * Get global.json data (static cached).
 *
 * @return array
 */
function zz_seo_get_global() {
	static $global = null;
	if ( $global === null ) {
		$global = zz_get_data( 'global' );
	}
	return $global;
}

/**
 * Resolve an OG image value to a full URL.
 *
 * @param mixed $value Attachment ID (int/numeric string), full URL, or filename.
 * @return string Full URL or empty string.
 */
function zz_seo_og_image_url( $value ) {
	if ( empty( $value ) && $value !== 0 ) {
		return '';
	}

	// Attachment ID
	if ( is_numeric( $value ) ) {
		$src = wp_get_attachment_image_src( intval( $value ), 'og-image' );
		if ( $src ) {
			return $src[0];
		}
		$url = wp_get_attachment_url( intval( $value ) );
		return $url ? $url : '';
	}

	// Already a full URL
	if ( is_string( $value ) && strpos( $value, 'http' ) === 0 ) {
		return $value;
	}

	// Relative filename — prepend theme img path
	if ( is_string( $value ) && $value !== '' ) {
		return get_template_directory_uri() . '/assets/img/' . $value;
	}

	return '';
}

/* =========================================================================
   B) Meta tags + Open Graph + Twitter Card  (wp_head, priority 1)
   ========================================================================= */

add_action( 'wp_head', function () {
	$page   = zz_seo_get_current_page_data();
	$global = zz_seo_get_global();
	$meta   = isset( $global['meta'] ) ? $global['meta'] : [];

	$seo = $page ? $page['seo'] : [];

	// --- Description ---
	$description = '';
	if ( ! empty( $seo['description'] ) ) {
		$description = $seo['description'];
	} elseif ( ! empty( $meta['description'] ) ) {
		$description = $meta['description'];
	}
	if ( $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	}

	// --- Canonical ---
	$canonical = '';
	if ( ! empty( $seo['canonical'] ) ) {
		$canonical = $seo['canonical'];
	} else {
		// Build from current request — path only (no query string)
		$path      = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '/';
		$canonical = home_url( $path );
	}
	echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";

	// --- Title for OG (may differ from <title>) ---
	$title = '';
	if ( ! empty( $seo['title'] ) ) {
		$title = $seo['title'];
	} else {
		$title = wp_get_document_title();
	}

	// --- OG image ---
	$og_image = '';
	if ( ! empty( $seo['og_image'] ) ) {
		$og_image = zz_seo_og_image_url( $seo['og_image'] );
	}
	if ( ! $og_image && ! empty( $meta['og_image'] ) ) {
		$og_image = zz_seo_og_image_url( $meta['og_image'] );
	}

	$site_name = ! empty( $meta['site_name'] ) ? $meta['site_name'] : 'Atelier ZougZoug';
	$locale    = ! empty( $meta['locale'] ) ? $meta['locale'] : 'fr_FR';

	// --- Open Graph ---
	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:locale" content="' . esc_attr( $locale ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $description ) {
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( $canonical ) . '">' . "\n";
	if ( $og_image ) {
		echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
		echo '<meta property="og:image:width" content="1200">' . "\n";
		echo '<meta property="og:image:height" content="630">' . "\n";
	}

	// --- Twitter Card ---
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $description ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	}
	if ( $og_image ) {
		echo '<meta name="twitter:image" content="' . esc_url( $og_image ) . '">' . "\n";
	}
}, 1 );

/* =========================================================================
   C) Schema.org JSON-LD — Organization (all pages, wp_head priority 2)
   ========================================================================= */

add_action( 'wp_head', function () {
	$global  = zz_seo_get_global();
	$footer  = isset( $global['footer'] ) ? $global['footer'] : [];
	$email   = ! empty( $footer['email'] ) ? $footer['email'] : 'atelierzougzoug@gmail.com';
	$sameAs  = [];
	if ( ! empty( $footer['instagram'] ) ) {
		$sameAs[] = $footer['instagram'];
	}

	$schema = [
		'@context'       => 'https://schema.org',
		'@type'          => 'LocalBusiness',
		'@id'            => home_url( '/#organization' ),
		'name'           => 'Atelier ZougZoug',
		'alternateName'  => 'Charlotte Auroux Céramiste',
		'url'            => home_url( '/' ),
		'address'        => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => '6 rue de la Terrasse',
			'postalCode'      => '43100',
			'addressLocality' => 'Brioude',
			'addressRegion'   => 'Auvergne-Rhône-Alpes',
			'addressCountry'  => 'FR',
		],
		'geo'            => [
			'@type'     => 'GeoCoordinates',
			'latitude'  => 45.2949,
			'longitude' => 3.3853,
		],
		'telephone'      => '+33660199818',
		'email'          => $email,
		'sameAs'         => $sameAs,
		'founder'        => [
			'@type' => 'Person',
			'@id'   => home_url( '/#person' ),
			'name'  => 'Charlotte Auroux',
		],
	];

	echo '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
	echo "\n" . '</script>' . "\n";

	/* =====================================================================
	   D) Schema.org JSON-LD — Person (page 'a-propos' only)
	   ===================================================================== */

	if ( is_page( 'a-propos' ) ) {
		$person = [
			'@context'     => 'https://schema.org',
			'@type'        => 'Person',
			'@id'          => home_url( '/#person' ),
			'name'         => 'Charlotte Auroux',
			'jobTitle'     => 'Céramiste',
			'worksFor'     => [
				'@type' => 'LocalBusiness',
				'@id'   => home_url( '/#organization' ),
			],
			'knowsAbout'   => [
				'céramique',
				'poterie',
				'tournage',
				'émaux',
				'luminaires',
				'art de la table',
			],
			'workLocation' => [
				'@type'   => 'Place',
				'name'    => 'Atelier ZougZoug',
				'address' => [
					'@type'           => 'PostalAddress',
					'streetAddress'   => '6 rue de la Terrasse',
					'postalCode'      => '43100',
					'addressLocality' => 'Brioude',
					'addressRegion'   => 'Auvergne-Rhône-Alpes',
					'addressCountry'  => 'FR',
				],
			],
		];

		echo '<script type="application/ld+json">' . "\n";
		echo wp_json_encode( $person, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
		echo "\n" . '</script>' . "\n";
	}

	/* =====================================================================
	   E) Schema.org JSON-LD — BreadcrumbList (all pages except front_page)
	   ===================================================================== */

	if ( ! is_front_page() ) {
		$page_data = zz_seo_get_current_page_data();

		if ( $page_data ) {
			$names = [
				'a-propos'       => 'À propos',
				'collaborations' => 'Collaborations',
				'contact'        => 'Contact',
				'cours'          => 'Cours de céramique',
				'revendeurs'     => 'Revendeurs & Évènements',
			];

			$slug         = $page_data['slug'];
			$display_name = isset( $names[ $slug ] ) ? $names[ $slug ] : ucfirst( $slug );

			$breadcrumb = [
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => [
					[
						'@type'    => 'ListItem',
						'position' => 1,
						'name'     => 'Accueil',
						'item'     => home_url( '/' ),
					],
					[
						'@type'    => 'ListItem',
						'position' => 2,
						'name'     => $display_name,
						'item'     => home_url( '/' . $slug . '/' ),
					],
				],
			];

			echo '<script type="application/ld+json">' . "\n";
			echo wp_json_encode( $breadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
			echo "\n" . '</script>' . "\n";
		}
	}

	/* =====================================================================
	   F) Schema.org JSON-LD — Course (page 'cours' only)
	   ===================================================================== */

	if ( is_page( 'cours' ) ) {
		$cours_data = zz_get_data( 'cours' );
		$offres     = isset( $cours_data['offres'] ) ? $cours_data['offres'] : [];

		if ( ! empty( $offres ) ) {
			$instances = [];
			foreach ( $offres as $offre ) {
				$instance = [
					'@type'      => 'CourseInstance',
					'name'       => isset( $offre['nom'] ) ? $offre['nom'] : '',
					'courseMode'  => 'onsite',
					'location'   => [
						'@type'   => 'Place',
						'name'    => 'Atelier ZougZoug',
						'address' => [
							'@type'           => 'PostalAddress',
							'streetAddress'   => '6 rue de la Terrasse',
							'postalCode'      => '43100',
							'addressLocality' => 'Brioude',
							'addressCountry'  => 'FR',
						],
					],
				];

				if ( isset( $offre['prix'] ) ) {
					$instance['offers'] = [
						'@type'         => 'Offer',
						'price'         => $offre['prix'],
						'priceCurrency' => 'EUR',
					];
				}

				$instances[] = $instance;
			}

			$course_schema = [
				'@context'       => 'https://schema.org',
				'@type'          => 'Course',
				'name'           => 'Cours de céramique — Atelier ZougZoug',
				'description'    => isset( $cours_data['intro']['text'] ) ? $cours_data['intro']['text'] : '',
				'provider'       => [
					'@type' => 'LocalBusiness',
					'@id'   => home_url( '/#organization' ),
				],
				'hasCourseInstance' => $instances,
			];

			echo '<script type="application/ld+json">' . "\n";
			echo wp_json_encode( $course_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
			echo "\n" . '</script>' . "\n";
		}
	}

	/* =====================================================================
	   G) Schema.org JSON-LD — Event (page 'revendeurs' only)
	   ===================================================================== */

	if ( is_page( 'revendeurs' ) ) {
		$rev_data    = zz_get_data( 'revendeurs' );
		$evenements  = isset( $rev_data['evenements'] ) ? $rev_data['evenements'] : [];

		foreach ( $evenements as $evt ) {
			$start_date = '';
			if ( ! empty( $evt['month'] ) && ! empty( $evt['year'] ) ) {
				$month_num = zz_seo_parse_event_date( $evt['month'], $evt['year'] );
				if ( $month_num ) {
					$start_date = $evt['year'] . '-' . str_pad( $month_num, 2, '0', STR_PAD_LEFT );
				}
			}

			$event_schema = [
				'@context'  => 'https://schema.org',
				'@type'     => 'Event',
				'name'      => isset( $evt['nom'] ) ? $evt['nom'] : '',
			];

			if ( $start_date ) {
				$event_schema['startDate'] = $start_date;
			}

			if ( ! empty( $evt['lieu'] ) ) {
				$event_schema['location'] = [
					'@type' => 'Place',
					'name'  => $evt['lieu'],
				];
			}

			echo '<script type="application/ld+json">' . "\n";
			echo wp_json_encode( $event_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
			echo "\n" . '</script>' . "\n";
		}
	}

	/* =====================================================================
	   H) Schema.org JSON-LD — FAQPage (page 'cours' only)
	   ===================================================================== */

	if ( is_page( 'cours' ) ) {
		$cours_data = zz_get_data( 'cours' );
		$faq_items  = isset( $cours_data['faq'] ) ? $cours_data['faq'] : [];

		if ( ! empty( $faq_items ) ) {
			$entities = [];
			foreach ( $faq_items as $item ) {
				$entities[] = [
					'@type'          => 'Question',
					'name'           => isset( $item['question'] ) ? $item['question'] : '',
					'acceptedAnswer' => [
						'@type' => 'Answer',
						'text'  => isset( $item['answer'] ) ? $item['answer'] : '',
					],
				];
			}

			$faq_schema = [
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $entities,
			];

			echo '<script type="application/ld+json">' . "\n";
			echo wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
			echo "\n" . '</script>' . "\n";
		}
	}
}, 2 );

/* =========================================================================
   G helper) Parse French month abbreviation to month number
   ========================================================================= */

/**
 * Map a French month string (abbreviation or full) + year to a numeric month.
 *
 * @param string $month_str French month (e.g. "janv", "fév", "Mars", "Sept.")
 * @param string $year      Year (unused in parsing, kept for signature consistency)
 * @return int|false Month number (1-12) or false on failure.
 */
function zz_seo_parse_event_date( $month_str, $year ) {
	$month_str = mb_strtolower( trim( $month_str ) );
	// Strip trailing period
	$month_str = rtrim( $month_str, '.' );

	$map = [
		'janv'      => 1,
		'janvier'   => 1,
		'jan'       => 1,
		'fev'       => 2,
		'fév'       => 2,
		'fevrier'   => 2,
		'février'   => 2,
		'feb'       => 2,
		'mars'      => 3,
		'mar'       => 3,
		'avr'       => 4,
		'avril'     => 4,
		'mai'       => 5,
		'juin'      => 6,
		'juil'      => 7,
		'juillet'   => 7,
		'aout'      => 8,
		'août'      => 8,
		'sept'      => 9,
		'septembre' => 9,
		'oct'       => 10,
		'octobre'   => 10,
		'nov'       => 11,
		'novembre'  => 11,
		'dec'       => 12,
		'déc'       => 12,
		'decembre'  => 12,
		'décembre'  => 12,
	];

	return isset( $map[ $month_str ] ) ? $map[ $month_str ] : false;
}

/* =========================================================================
   I) Sitemap XML (init — early interception before WP rewrite redirect)
   ========================================================================= */

// Remove WP core's duplicate canonical tag (our seo.php outputs its own)
remove_action( 'wp_head', 'rel_canonical' );

// Disable WP native sitemap (prevents wp-sitemap.xml from being generated)
add_filter( 'wp_sitemaps_enabled', '__return_false' );

// Intercept /sitemap.xml at 'init' before WP processes the sitemap query var
// and issues a 301 redirect to /wp-sitemap.xml
add_action( 'init', function () {
	$request_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';

	if ( '/sitemap.xml' !== rtrim( $request_path, '/' ) ) {
		return;
	}

	header( 'Content-Type: application/xml; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex' );

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

	// Static pages
	$static_pages = [
		[ 'path' => '/',               'priority' => '1.0',  'changefreq' => 'weekly' ],
		[ 'path' => '/collaborations/', 'priority' => '0.9', 'changefreq' => 'weekly' ],
		[ 'path' => '/a-propos/',       'priority' => '0.7', 'changefreq' => 'monthly' ],
		[ 'path' => '/contact/',        'priority' => '0.6', 'changefreq' => 'monthly' ],
		[ 'path' => '/cours/',          'priority' => '0.8', 'changefreq' => 'monthly' ],
		[ 'path' => '/revendeurs/',     'priority' => '0.7', 'changefreq' => 'monthly' ],
	];

	foreach ( $static_pages as $sp ) {
		echo "\t" . '<url>' . "\n";
		echo "\t\t" . '<loc>' . esc_url( home_url( $sp['path'] ) ) . '</loc>' . "\n";
		echo "\t\t" . '<changefreq>' . $sp['changefreq'] . '</changefreq>' . "\n";
		echo "\t\t" . '<priority>' . $sp['priority'] . '</priority>' . "\n";
		echo "\t" . '</url>' . "\n";
	}

	// Projet CPT posts
	$projets = get_posts( [
		'post_type'      => 'projet',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	] );

	foreach ( $projets as $projet ) {
		echo "\t" . '<url>' . "\n";
		echo "\t\t" . '<loc>' . esc_url( get_permalink( $projet ) ) . '</loc>' . "\n";
		echo "\t\t" . '<changefreq>monthly</changefreq>' . "\n";
		echo "\t\t" . '<priority>0.6</priority>' . "\n";
		echo "\t" . '</url>' . "\n";
	}

	echo '</urlset>' . "\n";
	exit;
} );

/* =========================================================================
   J) Robots.txt (robots_txt filter)
   ========================================================================= */

add_filter( 'robots_txt', function ( $output, $public ) {
	$sitemap_url = home_url( '/sitemap.xml' );

	$robots  = "# Atelier ZougZoug — robots.txt\n";
	$robots .= "# Generated by theme SEO module\n\n";

	// Default: allow all
	$robots .= "User-agent: *\n";
	$robots .= "Allow: /\n";
	$robots .= "Disallow: /wp-admin/\n";
	$robots .= "Allow: /wp-admin/admin-ajax.php\n\n";

	// Explicitly allow AI crawlers
	$robots .= "User-agent: GPTBot\n";
	$robots .= "Allow: /\n\n";

	$robots .= "User-agent: ChatGPT-User\n";
	$robots .= "Allow: /\n\n";

	$robots .= "User-agent: OAI-SearchBot\n";
	$robots .= "Allow: /\n\n";

	$robots .= "User-agent: Bingbot\n";
	$robots .= "Allow: /\n\n";

	// Sitemap
	$robots .= "Sitemap: " . $sitemap_url . "\n";

	return $robots;
}, 10, 2 );

/* =========================================================================
   K) Title override (document_title_parts + document_title_separator)
   ========================================================================= */

add_filter( 'document_title_parts', function ( $title_parts ) {
	$page = zz_seo_get_current_page_data();

	if ( $page && ! empty( $page['seo']['title'] ) ) {
		return [ 'title' => $page['seo']['title'] ];
	}

	return $title_parts;
} );

add_filter( 'document_title_separator', function ( $sep ) {
	$page = zz_seo_get_current_page_data();

	if ( $page && ! empty( $page['seo']['title'] ) ) {
		return '';
	}

	return $sep;
} );

/* =========================================================================
   L) Auto-regeneration OG mosaique Collaborations
   ========================================================================= */

/**
 * Regenere l'image OG mosaique quand un projet est publie, modifie ou supprime.
 * Debounce via transient (max 1 fois par 2 minutes) pour ne pas ralentir l'admin.
 */
add_action( 'save_post_projet', function ( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( wp_is_post_revision( $post_id ) ) return;

	// Regenerer seulement quand le statut est publish ou qu'on depublie
	if ( $post->post_status !== 'publish' && get_post_meta( $post_id, '_zz_was_published', true ) !== '1' ) {
		return;
	}

	// Tracker le statut publish pour detecter les depublications
	if ( $post->post_status === 'publish' ) {
		update_post_meta( $post_id, '_zz_was_published', '1' );
	} else {
		delete_post_meta( $post_id, '_zz_was_published' );
	}

	zz_schedule_og_mosaic_regeneration();
}, 10, 2 );

add_action( 'trashed_post', function ( $post_id ) {
	if ( get_post_type( $post_id ) !== 'projet' ) return;
	zz_schedule_og_mosaic_regeneration();
} );

/**
 * Planifie la regeneration via wp_schedule_single_event (async, non-bloquant).
 */
function zz_schedule_og_mosaic_regeneration() {
	// Debounce : max 1 regeneration par 2 minutes
	if ( get_transient( 'zz_og_mosaic_pending' ) ) return;
	set_transient( 'zz_og_mosaic_pending', '1', 2 * MINUTE_IN_SECONDS );

	// Planifier execution asynchrone
	if ( ! wp_next_scheduled( 'zz_regenerate_og_mosaic' ) ) {
		wp_schedule_single_event( time() + 5, 'zz_regenerate_og_mosaic' );
	}
}

add_action( 'zz_regenerate_og_mosaic', function () {
	require_once get_template_directory() . '/inc/admin/generate-og-mosaic.php';
	zz_generate_og_mosaic( false );
} );
