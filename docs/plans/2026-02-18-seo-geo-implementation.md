# SEO/GEO Module Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Build a custom SEO/GEO module for the Atelier ZougZoug WordPress theme — meta tags, Open Graph, Twitter Card, Schema.org JSON-LD, sitemap XML, robots.txt, FAQ accordion, and admin UI.

**Architecture:** A single `inc/seo.php` file reads SEO data from `data/*.json` files (new `seo` block per page) with fallbacks to `global.json`. It injects all meta/OG/Schema into `wp_head`. The admin editor gets a new "SEO & Partage" section per page. Page Cours gets an FAQ accordion + FAQPage schema.

**Tech Stack:** WordPress hooks (`wp_head`, `template_redirect`, `robots_txt`), JSON data files, Vanilla JS admin UI, Schema.org JSON-LD

**Design doc:** `docs/plans/2026-02-18-seo-geo-module-design.md`

---

## Task 1: Add SEO data to all JSON files

**Files:**
- Modify: `data/global.json`
- Modify: `data/home.json`
- Modify: `data/about.json`
- Modify: `data/contact.json`
- Modify: `data/cours.json`
- Modify: `data/revendeurs.json`

**Step 1: Update global.json — extend meta section**

Add `site_name` and `locale` fields to the existing `meta` block:

```json
{
    "site_title": "Atelier ZougZoug",
    "site_subtitle": "Charlotte Auroux — Céramiste, Brioude",
    "footer": { ... },
    "meta": {
        "description": "Atelier ZougZoug — Charlotte Auroux, céramiste à Brioude. Luminaires sur mesure, vaisselle artisanale, cours de céramique.",
        "og_image": "og-default.webp",
        "site_name": "Atelier ZougZoug",
        "locale": "fr_FR"
    }
}
```

**Step 2: Add `seo` block to home.json**

Add at the top level of the JSON object:

```json
{
    "seo": {
        "title": "Charlotte Auroux, céramiste à Brioude — Atelier ZougZoug",
        "description": "Charlotte Auroux, céramiste à Brioude (Auvergne). Luminaires, vaisselle et art de la table en céramique sur mesure. Atelier ZougZoug.",
        "og_image": "",
        "canonical": ""
    },
    "hero": { ... }
}
```

**Step 3: Add `seo` block to about.json**

```json
{
    "seo": {
        "title": "Charlotte Auroux, céramiste en Auvergne — Atelier ZougZoug",
        "description": "Découvrez le parcours de Charlotte Auroux, potière installée en Auvergne. Céramique artisanale, émaux naturels, pièces sur mesure à Brioude.",
        "og_image": "",
        "canonical": ""
    },
    "hero": { ... }
}
```

**Step 4: Add `seo` block to contact.json**

```json
{
    "seo": {
        "title": "Contact — Atelier ZougZoug, céramiste à Brioude (43)",
        "description": "Contactez Charlotte Auroux, céramiste à Brioude (43). Projets sur mesure, luminaires, vaisselle. Atelier ZougZoug, 6 rue de la Terrasse.",
        "og_image": "",
        "canonical": ""
    },
    "form": { ... }
}
```

**Step 5: Add `seo` + `faq` blocks to cours.json**

```json
{
    "seo": {
        "title": "Cours de céramique à Brioude — Atelier ZougZoug",
        "description": "Cours de céramique à Brioude : tournage, modelage, duo. À partir de 55€. Atelier ZougZoug, Charlotte Auroux.",
        "og_image": "",
        "canonical": ""
    },
    "faq": [
        {
            "question": "Quel matériel est fourni pour les cours de céramique ?",
            "answer": "Tout le matériel est fourni : argile, outils de tournage, émaux. Vous repartez avec votre pièce après cuisson (comptez 3 à 4 semaines)."
        },
        {
            "question": "Faut-il avoir de l'expérience pour participer ?",
            "answer": "Aucune expérience n'est requise. Les cours sont adaptés aux débutants comme aux initiés. Charlotte vous guide pas à pas."
        },
        {
            "question": "Où se déroulent les cours ?",
            "answer": "Les cours ont lieu à l'Atelier ZougZoug, 6 rue de la Terrasse, 43100 Brioude, au cœur de l'Auvergne."
        },
        {
            "question": "Comment réserver un cours de céramique ?",
            "answer": "Contactez Charlotte par email à atelierzougzoug@gmail.com ou par téléphone au 06 60 19 98 18. Vous pouvez aussi réserver via WeCanDoo."
        }
    ],
    "hero": { ... }
}
```

**Step 6: Add `seo` block to revendeurs.json**

```json
{
    "seo": {
        "title": "Points de vente et évènements — Atelier ZougZoug, Auvergne",
        "description": "Retrouvez les céramiques Atelier ZougZoug en boutique à Clermont-Ferrand et Brioude. Agenda des marchés et expositions 2026.",
        "og_image": "",
        "canonical": ""
    },
    "hero": { ... }
}
```

**Step 7: Commit**

```bash
git add data/global.json data/home.json data/about.json data/contact.json data/cours.json data/revendeurs.json
git commit -m "feat(seo): add SEO data blocks to all JSON files"
```

---

## Task 2: Create inc/seo.php — Meta tags + OG + Twitter Card

**Files:**
- Create: `inc/seo.php`
- Modify: `functions.php` (add require)

**Step 1: Create inc/seo.php with meta output**

```php
<?php
/**
 * Module SEO/GEO — Meta, Open Graph, Twitter Card, Schema.org, Sitemap, Robots
 */

/**
 * Determine la page courante et retourne ses donnees SEO
 */
function zz_seo_get_current_page_data() {
	$slug = '';

	if (is_front_page()) {
		$slug = 'home';
	} elseif (is_page('a-propos')) {
		$slug = 'about';
	} elseif (is_page('contact')) {
		$slug = 'contact';
	} elseif (is_page('cours')) {
		$slug = 'cours';
	} elseif (is_page('revendeurs')) {
		$slug = 'revendeurs';
	} elseif (is_page('collaborations')) {
		$slug = 'collaborations';
	}

	if (!$slug) return null;

	$data = zz_get_data($slug);
	return [
		'slug' => $slug,
		'data' => $data,
		'seo'  => $data['seo'] ?? [],
	];
}

/**
 * Donnees SEO globales (fallback)
 */
function zz_seo_get_global() {
	static $global = null;
	if ($global === null) {
		$global = zz_get_data('global');
	}
	return $global;
}

/**
 * Resoudre une image OG : ID → URL au format og-image (1200x630)
 */
function zz_seo_og_image_url($value) {
	if (empty($value)) return '';

	// Attachment ID
	if (is_numeric($value)) {
		$src = wp_get_attachment_image_src(intval($value), 'og-image');
		if ($src) return $src[0];
		// Fallback: URL complete
		$url = wp_get_attachment_url(intval($value));
		if ($url) return $url;
	}

	// String: chemin relatif ou URL
	if (is_string($value)) {
		if (strpos($value, 'http') === 0) return $value;
		return get_template_directory_uri() . '/assets/img/' . $value;
	}

	return '';
}

/**
 * Injecter meta description, canonical, OG, Twitter Card dans wp_head
 */
add_action('wp_head', function () {
	$page = zz_seo_get_current_page_data();
	$global = zz_seo_get_global();
	$meta = $global['meta'] ?? [];

	// Valeurs SEO
	$title       = '';
	$description = '';
	$og_image    = '';
	$canonical   = '';

	if ($page) {
		$seo = $page['seo'];
		$title       = $seo['title'] ?? '';
		$description = $seo['description'] ?? '';
		$og_image    = $seo['og_image'] ?? '';
		$canonical   = $seo['canonical'] ?? '';
	}

	// Fallbacks
	if (!$description) {
		$description = $meta['description'] ?? '';
	}
	if (!$og_image) {
		$og_image = $meta['og_image'] ?? '';
	}
	if (!$canonical) {
		// URL courante sans query string
		$canonical = home_url(add_query_arg([], wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
	}
	if (!$title) {
		$title = wp_get_document_title();
	}

	$site_name = $meta['site_name'] ?? 'Atelier ZougZoug';
	$locale    = $meta['locale'] ?? 'fr_FR';
	$og_img_url = zz_seo_og_image_url($og_image);

	// — Meta description
	if ($description) {
		echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
	}

	// — Canonical
	if ($canonical) {
		echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
	}

	// — Open Graph
	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:locale" content="' . esc_attr($locale) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
	if ($description) {
		echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
	}
	if ($canonical) {
		echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";
	}
	if ($og_img_url) {
		echo '<meta property="og:image" content="' . esc_url($og_img_url) . '">' . "\n";
		echo '<meta property="og:image:width" content="1200">' . "\n";
		echo '<meta property="og:image:height" content="630">' . "\n";
	}

	// — Twitter Card
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
	if ($description) {
		echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
	}
	if ($og_img_url) {
		echo '<meta name="twitter:image" content="' . esc_url($og_img_url) . '">' . "\n";
	}
}, 1);
```

**Step 2: Add og-image size in setup.php**

In `inc/setup.php`, inside the `after_setup_theme` callback, add after line 33:

```php
	add_image_size('og-image', 1200, 630, true);
```

**Step 3: Add require in functions.php**

After the `require_once ... '/inc/image-optimizer.php';` line (line 15), add:

```php
require_once get_template_directory() . '/inc/seo.php';
```

**Step 4: Verify — check meta tags in HTML output**

```bash
wp eval "
\$page = zz_seo_get_current_page_data();
echo 'Page data loaded: ' . (\$page ? 'yes' : 'no') . PHP_EOL;
\$global = zz_seo_get_global();
echo 'Global meta: ' . print_r(\$global['meta'] ?? [], true);
"
```

Then curl the homepage and check for meta tags:

```bash
curl -sk https://zougzoug.lan/ | grep -E '(og:|twitter:|description|canonical)' | head -20
```

Expected: meta description, canonical, og:*, twitter:* tags present.

**Step 5: Commit**

```bash
git add inc/seo.php inc/setup.php functions.php
git commit -m "feat(seo): add meta tags, Open Graph and Twitter Card output"
```

---

## Task 3: Add Schema.org JSON-LD — Organization + Person + Breadcrumb

**Files:**
- Modify: `inc/seo.php` (append)

**Step 1: Add Organization schema (all pages)**

Append to `inc/seo.php`:

```php
/**
 * Schema.org JSON-LD — Organization (LocalBusiness)
 * Injecte sur toutes les pages
 */
add_action('wp_head', function () {
	$global = zz_seo_get_global();
	$footer = $global['footer'] ?? [];

	$schema = [
		'@context'      => 'https://schema.org',
		'@type'         => 'LocalBusiness',
		'@id'           => home_url('/#organization'),
		'name'          => $global['site_title'] ?? 'Atelier ZougZoug',
		'alternateName' => 'Charlotte Auroux Céramiste',
		'description'   => $global['meta']['description'] ?? '',
		'url'           => home_url('/'),
		'telephone'     => '+33660199818',
		'email'         => $footer['email'] ?? 'atelierzougzoug@gmail.com',
		'address'       => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => '6 rue de la Terrasse',
			'addressLocality' => 'Brioude',
			'postalCode'      => '43100',
			'addressRegion'   => 'Auvergne-Rhône-Alpes',
			'addressCountry'  => 'FR',
		],
		'geo'           => [
			'@type'     => 'GeoCoordinates',
			'latitude'  => 45.2949,
			'longitude' => 3.3853,
		],
		'sameAs'        => [],
		'founder'       => [
			'@type'    => 'Person',
			'@id'      => home_url('/#person'),
			'name'     => 'Charlotte Auroux',
			'jobTitle' => 'Céramiste',
		],
	];

	if (!empty($footer['instagram'])) {
		$schema['sameAs'][] = $footer['instagram'];
	}

	echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}, 2);

/**
 * Schema.org JSON-LD — Person (page A propos uniquement)
 */
add_action('wp_head', function () {
	if (!is_page('a-propos')) return;

	$schema = [
		'@context'     => 'https://schema.org',
		'@type'        => 'Person',
		'@id'          => home_url('/#person'),
		'name'         => 'Charlotte Auroux',
		'jobTitle'     => 'Céramiste',
		'worksFor'     => ['@id' => home_url('/#organization')],
		'knowsAbout'   => ['céramique', 'poterie', 'tournage', 'émaux', 'luminaires', 'art de la table'],
		'workLocation' => [
			'@type'   => 'Place',
			'name'    => 'Atelier ZougZoug',
			'address' => [
				'@type'           => 'PostalAddress',
				'streetAddress'   => '6 rue de la Terrasse',
				'addressLocality' => 'Brioude',
				'postalCode'      => '43100',
				'addressRegion'   => 'Auvergne-Rhône-Alpes',
				'addressCountry'  => 'FR',
			],
		],
	];

	echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}, 2);

/**
 * Schema.org JSON-LD — BreadcrumbList (toutes pages sauf accueil)
 */
add_action('wp_head', function () {
	if (is_front_page()) return;

	$items = [
		['name' => 'Accueil', 'url' => home_url('/')],
	];

	$page_title = wp_get_document_title();
	$page_url = home_url(add_query_arg([], wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));

	// Noms propres par page
	$breadcrumb_names = [
		'a-propos'       => 'À propos',
		'collaborations' => 'Collaborations',
		'contact'        => 'Contact',
		'cours'          => 'Cours de céramique',
		'revendeurs'     => 'Revendeurs & Évènements',
	];

	$page_obj = get_queried_object();
	$slug = $page_obj->post_name ?? '';
	$name = $breadcrumb_names[$slug] ?? $page_title;

	$items[] = ['name' => $name, 'url' => $page_url];

	$list_items = [];
	foreach ($items as $i => $item) {
		$list_items[] = [
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item['name'],
			'item'     => $item['url'],
		];
	}

	$schema = [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $list_items,
	];

	echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}, 2);
```

**Step 2: Verify schemas**

```bash
curl -sk https://zougzoug.lan/ | grep -o 'application/ld+json' | wc -l
curl -sk https://zougzoug.lan/a-propos/ | grep -o 'application/ld+json' | wc -l
```

Expected: homepage = 1 (Organization), a-propos = 3 (Organization + Person + Breadcrumb)

**Step 3: Commit**

```bash
git add inc/seo.php
git commit -m "feat(seo): add Schema.org JSON-LD — Organization, Person, BreadcrumbList"
```

---

## Task 4: Add Schema.org — Course + Event + FAQPage

**Files:**
- Modify: `inc/seo.php` (append)

**Step 1: Add Course schema (page Cours)**

Append to `inc/seo.php`:

```php
/**
 * Schema.org JSON-LD — Course (page Cours)
 * Genere depuis cours.json → offres[]
 */
add_action('wp_head', function () {
	if (!is_page('cours')) return;

	$data = zz_get_data('cours');
	$offres = $data['offres'] ?? [];
	if (empty($offres)) return;

	$instances = [];
	foreach ($offres as $offre) {
		$instance = [
			'@type'      => 'CourseInstance',
			'name'       => $offre['nom'] ?? '',
			'courseMode'  => 'onsite',
			'location'   => [
				'@type'   => 'Place',
				'name'    => 'Atelier ZougZoug',
				'address' => [
					'@type'           => 'PostalAddress',
					'streetAddress'   => '6 rue de la Terrasse',
					'addressLocality' => 'Brioude',
					'postalCode'      => '43100',
					'addressCountry'  => 'FR',
				],
			],
		];

		if (!empty($offre['prix'])) {
			$instance['offers'] = [
				'@type'         => 'Offer',
				'price'         => (string) $offre['prix'],
				'priceCurrency' => 'EUR',
			];
		}

		$instances[] = $instance;
	}

	$schema = [
		'@context'          => 'https://schema.org',
		'@type'             => 'Course',
		'name'              => $data['seo']['title'] ?? 'Cours de céramique à Brioude',
		'description'       => $data['seo']['description'] ?? $data['intro']['text'] ?? '',
		'provider'          => ['@id' => home_url('/#organization')],
		'hasCourseInstance' => $instances,
	];

	echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}, 2);

/**
 * Schema.org JSON-LD — Event (page Revendeurs)
 * Genere depuis revendeurs.json → evenements[]
 */
add_action('wp_head', function () {
	if (!is_page('revendeurs')) return;

	$data = zz_get_data('revendeurs');
	$events = $data['evenements'] ?? [];
	if (empty($events)) return;

	foreach ($events as $event) {
		// Construire une date approximative depuis month + year
		$start_date = zz_seo_parse_event_date($event['month'] ?? '', $event['year'] ?? '');

		$schema = [
			'@context'  => 'https://schema.org',
			'@type'     => 'Event',
			'name'      => $event['nom'] ?? '',
			'organizer' => ['@id' => home_url('/#organization')],
		];

		if ($start_date) {
			$schema['startDate'] = $start_date;
		}

		if (!empty($event['lieu'])) {
			$schema['location'] = [
				'@type'   => 'Place',
				'name'    => $event['lieu'],
			];
		}

		echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
	}
}, 2);

/**
 * Helper : convertir "Mai" + "2026" en "2026-05"
 */
function zz_seo_parse_event_date($month_str, $year) {
	if (empty($year)) return '';

	$months = [
		'janv' => '01', 'jan' => '01', 'janvier' => '01',
		'fev' => '02', 'fevr' => '02', 'février' => '02', 'fevrier' => '02',
		'mars' => '03', 'mar' => '03',
		'avr' => '04', 'avril' => '04',
		'mai' => '05',
		'juin' => '06', 'jun' => '06',
		'juil' => '07', 'juillet' => '07', 'jul' => '07',
		'aout' => '08', 'août' => '08', 'aou' => '08',
		'sept' => '09', 'septembre' => '09', 'sep' => '09',
		'oct' => '10', 'octobre' => '10',
		'nov' => '11', 'novembre' => '11',
		'dec' => '12', 'déc' => '12', 'decembre' => '12', 'décembre' => '12',
	];

	$key = mb_strtolower(rtrim($month_str, '.'));
	$m = $months[$key] ?? '';
	if (!$m) return $year;

	// Try to extract day from "details" field (e.g. "13 — 14 juin")
	return $year . '-' . $m;
}

/**
 * Schema.org JSON-LD — FAQPage (page Cours uniquement)
 * Genere depuis cours.json → faq[]
 */
add_action('wp_head', function () {
	if (!is_page('cours')) return;

	$data = zz_get_data('cours');
	$faqs = $data['faq'] ?? [];
	if (empty($faqs)) return;

	$entities = [];
	foreach ($faqs as $faq) {
		if (empty($faq['question']) || empty($faq['answer'])) continue;
		$entities[] = [
			'@type'          => 'Question',
			'name'           => $faq['question'],
			'acceptedAnswer' => [
				'@type' => 'Answer',
				'text'  => $faq['answer'],
			],
		];
	}

	if (empty($entities)) return;

	$schema = [
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	];

	echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}, 2);
```

**Step 2: Verify**

```bash
curl -sk https://zougzoug.lan/cours/ | grep -o 'application/ld+json' | wc -l
curl -sk https://zougzoug.lan/revendeurs/ | grep -o 'application/ld+json' | wc -l
```

Expected: cours = 4 (Organization + Breadcrumb + Course + FAQPage), revendeurs = 3+ (Organization + Breadcrumb + N Events)

**Step 3: Commit**

```bash
git add inc/seo.php
git commit -m "feat(seo): add Schema.org — Course, Event, FAQPage"
```

---

## Task 5: Add Sitemap XML + Robots.txt

**Files:**
- Modify: `inc/seo.php` (append)

**Step 1: Add sitemap generation**

Append to `inc/seo.php`:

```php
/**
 * Sitemap XML dynamique — /sitemap.xml
 */
add_action('template_redirect', function () {
	if ($_SERVER['REQUEST_URI'] !== '/sitemap.xml') return;

	header('Content-Type: application/xml; charset=UTF-8');
	header('X-Robots-Tag: noindex');

	$urls = [];

	// Pages statiques
	$pages = [
		['loc' => home_url('/'),                'priority' => '1.0', 'changefreq' => 'weekly'],
		['loc' => home_url('/collaborations/'), 'priority' => '0.8', 'changefreq' => 'monthly'],
		['loc' => home_url('/a-propos/'),       'priority' => '0.7', 'changefreq' => 'monthly'],
		['loc' => home_url('/contact/'),         'priority' => '0.7', 'changefreq' => 'monthly'],
		['loc' => home_url('/cours/'),           'priority' => '0.8', 'changefreq' => 'monthly'],
		['loc' => home_url('/revendeurs/'),      'priority' => '0.6', 'changefreq' => 'monthly'],
	];

	// Projets (CPT)
	$projets = get_posts([
		'post_type'      => 'projet',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
	]);

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

	foreach ($pages as $page) {
		echo '  <url>' . "\n";
		echo '    <loc>' . esc_url($page['loc']) . '</loc>' . "\n";
		echo '    <changefreq>' . $page['changefreq'] . '</changefreq>' . "\n";
		echo '    <priority>' . $page['priority'] . '</priority>' . "\n";
		echo '  </url>' . "\n";
	}

	foreach ($projets as $projet) {
		echo '  <url>' . "\n";
		echo '    <loc>' . esc_url(get_permalink($projet)) . '</loc>' . "\n";
		echo '    <lastmod>' . get_the_modified_date('Y-m-d', $projet) . '</lastmod>' . "\n";
		echo '    <changefreq>monthly</changefreq>' . "\n";
		echo '    <priority>0.6</priority>' . "\n";
		echo '  </url>' . "\n";
	}

	echo '</urlset>' . "\n";
	exit;
});

/**
 * Robots.txt — autoriser les crawlers IA
 */
add_filter('robots_txt', function ($output, $public) {
	// Remplacer entierement
	$robots  = "User-agent: *\n";
	$robots .= "Allow: /\n";
	$robots .= "Disallow: /wp-admin/\n";
	$robots .= "Allow: /wp-admin/admin-ajax.php\n\n";

	$robots .= "# AI Crawlers\n";
	$robots .= "User-agent: GPTBot\n";
	$robots .= "Allow: /\n\n";
	$robots .= "User-agent: ChatGPT-User\n";
	$robots .= "Allow: /\n\n";
	$robots .= "User-agent: OAI-SearchBot\n";
	$robots .= "Allow: /\n\n";
	$robots .= "User-agent: Bingbot\n";
	$robots .= "Allow: /\n\n";

	$robots .= "Sitemap: " . home_url('/sitemap.xml') . "\n";

	return $robots;
}, 10, 2);

/**
 * Desactiver le sitemap WordPress natif (WP 5.5+) pour eviter les doublons
 */
add_filter('wp_sitemaps_enabled', '__return_false');
```

**Step 2: Verify**

```bash
curl -sk https://zougzoug.lan/sitemap.xml | head -30
curl -sk https://zougzoug.lan/robots.txt
```

Expected: sitemap.xml shows urlset with pages + projets. robots.txt shows our custom rules.

**Step 3: Commit**

```bash
git add inc/seo.php
git commit -m "feat(seo): add sitemap.xml generation and robots.txt with AI crawler access"
```

---

## Task 6: Add FAQ accordion to page-cours.php

**Files:**
- Modify: `page-cours.php`
- Modify: `assets/css/cours.css`
- Modify: `assets/js/cours.js`

**Step 1: Add FAQ HTML section in page-cours.php**

Insert before the CTA final section (before `<!-- ======================== CTA FINAL ======================== -->`):

```php
  <!-- ======================== FAQ ======================== -->
  <?php if (!empty($data['faq'])) : ?>
  <section class="cours-faq">
    <div class="cours-faq-inner">
      <h2 class="cours-faq-title">Questions fréquentes</h2>
      <div class="cours-faq-list">
        <?php foreach ($data['faq'] as $faq) : ?>
        <details class="faq-item">
          <summary class="faq-question"><?php echo esc_html($faq['question']); ?></summary>
          <div class="faq-answer">
            <p><?php echo esc_html($faq['answer']); ?></p>
          </div>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
```

**Step 2: Add FAQ styles in cours.css**

Append to `assets/css/cours.css`:

```css
/* ======================== FAQ ======================== */

.cours-faq {
  padding: 80px 40px;
  max-width: 800px;
  margin: 0 auto;
}

.cours-faq-title {
  font-family: var(--font-family, 'General Sans', sans-serif);
  font-size: 14px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: rgba(26, 26, 26, 0.4);
  margin-bottom: 40px;
}

.cours-faq-list {
  display: flex;
  flex-direction: column;
}

.faq-item {
  border-bottom: 1px solid rgba(26, 26, 26, 0.1);
}

.faq-question {
  padding: 20px 0;
  font-family: var(--font-family, 'General Sans', sans-serif);
  font-size: 18px;
  font-weight: 500;
  color: #1A1A1A;
  cursor: pointer;
  list-style: none;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.faq-question::-webkit-details-marker {
  display: none;
}

.faq-question::after {
  content: '+';
  font-size: 20px;
  font-weight: 400;
  color: rgba(26, 26, 26, 0.4);
  transition: transform 0.3s ease;
  flex-shrink: 0;
  margin-left: 20px;
}

.faq-item[open] .faq-question::after {
  content: '−';
}

.faq-answer {
  padding: 0 0 20px 0;
}

.faq-answer p {
  font-size: 16px;
  line-height: 1.7;
  color: rgba(26, 26, 26, 0.7);
  margin: 0;
}

@media (max-width: 768px) {
  .cours-faq {
    padding: 60px 20px;
  }

  .faq-question {
    font-size: 16px;
  }
}
```

**Step 3: No JS needed**

The native HTML `<details>/<summary>` elements handle the accordion behavior without JavaScript. No modification to `cours.js` needed.

**Step 4: Verify**

```bash
curl -sk https://zougzoug.lan/cours/ | grep -c 'faq-item'
```

Expected: 4 (one per FAQ item)

**Step 5: Commit**

```bash
git add page-cours.php assets/css/cours.css
git commit -m "feat(seo): add FAQ accordion to Cours page"
```

---

## Task 7: Add SEO section to admin editor

**Files:**
- Modify: `inc/admin/admin-editor.js` — add SEO section to all page schemas + FAQ section to cours
- Modify: `inc/admin/admin-editor.css` — styles for SEO fields (counters)
- Modify: `inc/admin/admin-api.php` — add `seo` and `faq` to schema validation

**Step 1: Add SEO section to all SCHEMAS in admin-editor.js**

Add a new section to each page schema in the SCHEMAS object. Insert a `seo` section as the LAST section of each page schema (before the closing `]`):

For ALL pages (home, about, contact, cours, revendeurs), add this section:

```javascript
      {
        key: 'seo', title: 'SEO & Partage', icon: 'dashicons-search',
        fields: [
          { key: 'title', label: 'Title SEO (max 60 car.)', type: 'seo-text', maxLength: 60 },
          { key: 'description', label: 'Meta description (max 160 car.)', type: 'seo-textarea', maxLength: 160 },
          { key: 'og_image', label: 'Image OG (1200x630)', type: 'image' },
          { key: 'canonical', label: 'URL canonique', type: 'url' }
        ]
      }
```

For `cours` page ONLY, also add a `faq` section BEFORE the `seo` section:

```javascript
      {
        key: 'faq', title: 'FAQ', icon: 'dashicons-editor-help',
        fields: [],
        repeater: {
          fields: [
            { key: 'question', label: 'Question', type: 'text' },
            { key: 'answer', label: 'Réponse', type: 'textarea' }
          ]
        }
      }
```

**Step 2: Add `seo-text` and `seo-textarea` field types in buildField()**

In the `buildField()` function, add after the `textarea` type handler (after the `else if (field.type === 'textarea')` block):

```javascript
    else if (field.type === 'seo-text') {
      var val3 = value != null ? value : '';
      var max = field.maxLength || 60;
      var len = val3.length;
      var cls = len > max ? ' zz-counter--over' : '';
      html += '<input type="text" class="zz-input zz-seo-input" data-path="' + path + '" value="' + escAttr(String(val3)) + '" maxlength="' + (max + 20) + '">';
      html += '<span class="zz-counter' + cls + '" data-path="' + path + '" data-max="' + max + '">' + len + '/' + max + '</span>';
    }

    else if (field.type === 'seo-textarea') {
      var val4 = value != null ? value : '';
      var max2 = field.maxLength || 160;
      var len2 = val4.length;
      var cls2 = len2 > max2 ? ' zz-counter--over' : '';
      html += '<textarea class="zz-input zz-seo-input" data-path="' + path + '" rows="3" maxlength="' + (max2 + 20) + '">' + escHtml(String(val4)) + '</textarea>';
      html += '<span class="zz-counter' + cls2 + '" data-path="' + path + '" data-max="' + max2 + '">' + len2 + '/' + max2 + '</span>';
    }
```

**Step 3: Add counter update binding in buildForm()**

In the `buildForm()` function, after the existing `bind*` calls (after `bindGallery();`), add:

```javascript
    bindSeoCounters();
```

**Step 4: Add bindSeoCounters() function**

Add this function alongside the other bind functions:

```javascript
  /* --------------------------------------------------------
     SEO COUNTERS — live character count
     -------------------------------------------------------- */

  function bindSeoCounters() {
    var inputs = formEl.querySelectorAll('.zz-seo-input');
    for (var i = 0; i < inputs.length; i++) {
      inputs[i].addEventListener('input', function () {
        var path = this.getAttribute('data-path');
        var counter = formEl.querySelector('.zz-counter[data-path="' + path + '"]');
        if (!counter) return;
        var max = parseInt(counter.getAttribute('data-max'), 10);
        var len = this.value.length;
        counter.textContent = len + '/' + max;
        if (len > max) {
          counter.classList.add('zz-counter--over');
        } else {
          counter.classList.remove('zz-counter--over');
        }
      });
    }
  }
```

**Step 5: Handle `seo-text` and `seo-textarea` in collectData()**

In the `collectData()` function, the existing handlers for `text`/`url`/`number` and `textarea` use `.zz-input[data-path="..."]` selectors. Since `seo-text` and `seo-textarea` also use `.zz-input` class, they will be collected automatically by the existing logic. No changes needed.

**Step 6: Add CSS for SEO fields**

Append to `inc/admin/admin-editor.css`:

```css
/* ======================== SEO FIELDS ======================== */

.zz-counter {
  display: block;
  margin-top: 4px;
  font-size: 12px;
  color: rgba(26, 26, 26, 0.4);
  text-align: right;
}

.zz-counter--over {
  color: #E53935;
  font-weight: 600;
}
```

**Step 7: Verify**

Open the admin editor for any page and check that the "SEO & Partage" section appears with title, description (with counters), OG image picker, and canonical URL fields.

Check the Cours page editor has an additional "FAQ" section with repeater.

**Step 8: Commit**

```bash
git add inc/admin/admin-editor.js inc/admin/admin-editor.css inc/admin/admin-api.php
git commit -m "feat(seo): add SEO section to admin editor with character counters"
```

---

## Task 8: Disable WordPress default title for SEO-managed pages

**Files:**
- Modify: `inc/seo.php` (add title filter)

**Step 1: Override document title for pages with custom SEO title**

Append to `inc/seo.php`:

```php
/**
 * Override le title WordPress pour les pages avec un titre SEO custom
 */
add_filter('document_title_parts', function ($title_parts) {
	$page = zz_seo_get_current_page_data();
	if ($page && !empty($page['seo']['title'])) {
		// Remplacer tout le title par notre titre SEO
		return ['title' => $page['seo']['title']];
	}
	return $title_parts;
});

/**
 * Retirer le separateur du title quand on a un titre SEO custom
 */
add_filter('document_title_separator', function ($sep) {
	$page = zz_seo_get_current_page_data();
	if ($page && !empty($page['seo']['title'])) {
		return '';
	}
	return $sep;
});
```

**Step 2: Verify**

```bash
curl -sk https://zougzoug.lan/ | grep '<title>'
curl -sk https://zougzoug.lan/cours/ | grep '<title>'
```

Expected:
- Homepage: `<title>Charlotte Auroux, céramiste à Brioude — Atelier ZougZoug</title>`
- Cours: `<title>Cours de céramique à Brioude — Atelier ZougZoug</title>`

**Step 3: Commit**

```bash
git add inc/seo.php
git commit -m "feat(seo): override document title with custom SEO titles"
```

---

## Task 9: Final verification

**Step 1: Validate all pages have meta + OG + Schema**

```bash
for page in '' 'a-propos/' 'contact/' 'cours/' 'revendeurs/' 'collaborations/'; do
  echo "=== $page ==="
  curl -sk "https://zougzoug.lan/$page" | grep -cE '(og:|twitter:|description|canonical|ld\+json)'
done
```

**Step 2: Validate JSON-LD with Google validator**

Copy the JSON-LD blocks from the homepage source and paste into https://validator.schema.org/ or Google Rich Results Test.

**Step 3: Check sitemap and robots**

```bash
curl -sk https://zougzoug.lan/sitemap.xml | grep '<loc>'
curl -sk https://zougzoug.lan/robots.txt
```

**Step 4: Check admin editor loads correctly for all pages**

Test in browser: each page editor should show the SEO section, counters work, OG image picker works.

**Step 5: Check FAQ accordion on Cours page**

Visit https://zougzoug.lan/cours/ — FAQ section should appear before the CTA, questions expand/collapse.

**Step 6: Final commit (if any remaining changes)**

```bash
git add -A
git commit -m "feat(seo): final verification and cleanup"
```
