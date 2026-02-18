# Atelier ZougZoug

Site vitrine WordPress pour **Charlotte Auroux**, ceramiste sous la marque **Atelier ZougZoug**, basee a Brioude (Auvergne).

Site B2B/B2C : collaborations sur-mesure (hotels, restaurants, architectes) + vente via revendeurs + cours de ceramique.

---

## Stack technique

| Element | Choix |
|---------|-------|
| CMS | WordPress 6.x |
| PHP | 8.0+ |
| Theme | Custom from scratch (classique, pas FSE) |
| CSS | Vanilla, BEM-like, pas de preprocesseur |
| JS | Vanilla ES6+, pas de build step |
| Donnees | JSON (`data/*.json`) + CPT natifs |
| Admin | Editeur split-screen custom (Vanilla JS) |
| Fonts | General Sans (Fontshare), servie en local |
| Animations | GSAP + ScrollTrigger + Swiper (en local) |
| Plugins | CF7, Rank Math, WP Rocket, Imagify, Wordfence, Safe SVG |

**Pas de ACF** — tout est gere via fichiers JSON et Custom Post Types natifs.

---

## Structure du repository

Seuls le theme custom et les mu-plugins sont versiones :

```
wp-content/
  themes/zougzoug/         <-- Theme custom (committe)
  mu-plugins/              <-- Plugins must-use (committe)
```

WordPress core, plugins tiers, uploads et secrets sont exclus via `.gitignore`.

### Arborescence du theme

```
themes/zougzoug/
  style.css                  Metadata theme WP
  functions.php              Setup, includes, hooks CF7
  header.php / footer.php    Layout global
  front-page.php             Accueil (hero Swiper, statement, sections)
  page-collaborations.php    Projets (grille filtrable + lightbox)
  page-a-propos.php          A propos (portrait, diptyques sticky scroll)
  page-contact.php           Contact (CF7 + infos + photos)
  page-cours.php             Cours ceramique (offres, galerie)
  page-revendeurs.php        Points de vente + agenda evenements
  page-mentions-legales.php  Mentions legales
  404.php                    Page 404

  data/                      Contenus JSON editables via admin
    home.json                Accueil
    about.json               A propos
    contact.json             Contact
    cours.json               Cours
    revendeurs.json          Revendeurs
    collaborations.json      Collaborations
    mentions.json            Mentions legales
    global.json              Donnees globales (footer, social)

  assets/
    css/                     1 fichier CSS par page + main.css global
    js/                      1 fichier JS par page + main.js global
    js/vendor/               GSAP, ScrollTrigger, Swiper (local)
    fonts/                   General Sans (woff2)
    img/                     Logo SVG, favicon

  inc/
    setup.php                Theme supports, menus, image sizes
    enqueue.php              Enqueue scripts/styles
    json-loader.php          Helpers : zz_get_data(), zz_img()
    cpt-projet.php           CPT Projet + taxonomie + galerie admin
    cpt-evenement.php        CPT Evenement + taxonomie
    image-optimizer.php      Conversion WebP auto + redimensionnement
    seo.php                  Meta, OG, Schema.org, sitemap, robots.txt
    admin/
      admin-api.php          REST API custom (lecture/sauvegarde JSON)
      admin-customize.php    Branding admin (logo, couleurs, sidebar)
      admin-dashboard.php    Dashboard custom ZougZoug
      admin-pages.php        Editeur JSON split-screen
      generate-og-mosaic.php Generation image OG mosaique
```

### Mu-plugins

```
mu-plugins/
  zz-security.php        Hardening (XML-RPC off, version masquee, REST restreinte, anti brute-force)
  zz-maintenance.php     Mode maintenance / coming soon (toggle dans Reglages)
```

---

## Fonctionnalites

### Pages

- **Accueil** — Hero fullscreen Swiper vertical (4 slides, 8 images), statement avec logo SVG trace au scroll, sections luminaires/vaisselle, galerie Instagram, CTA
- **Collaborations** — Grille de projets filtrable (art de la table / luminaires / accessoires), lightbox avec navigation, 13 projets importes
- **A propos** — Portrait hero sticky, 3 blocs diptyques alternes avec scroll sticky (GSAP)
- **Contact** — Formulaire CF7 + coordonnees + bande photos, protege par honeypot + Turnstile
- **Cours** — 4 offres de cours ceramique, integration WeCanDoo, galerie
- **Revendeurs** — 3 points de vente + agenda evenements (CPT Evenement)
- **404** — Page d'erreur animee

### Custom Post Types

**Projet** (`/collaborations/`)
- Taxonomie : `categorie_projet` (art-de-la-table, luminaires, accessoires)
- Meta fields : client, annee, lieu, description, materiaux, Instagram, site web, collaborateurs
- Galerie drag & drop dans l'admin (images + videos avec poster FFmpeg)
- Tri par drag & drop (menu_order)

**Evenement**
- Taxonomie : `type_evenement`
- Meta fields : date, lieu, description, lien
- Affichage automatique "a venir" / "passe"

### Admin custom

- **Editeur split-screen** : formulaire Vanilla JS a gauche + iframe preview a droite
- **REST API custom** : `GET/POST /wp-json/zougzoug/v1/page/{slug}` pour lire/sauvegarder les JSON
- **Dashboard** : panneau ZougZoug avec liens rapides, prochains evenements, raccourcis edition
- **Branding** : logo custom sur la page de login, sidebar admin coloree, barre admin avec liens directs

### SEO (sans plugin)

- Meta description, canonical, Open Graph, Twitter Card
- Schema.org : Organization, Person, BreadcrumbList, Course, Event, FAQPage
- Sitemap XML custom (`/sitemap.xml`)
- Robots.txt custom
- Image OG mosaique auto-generee (Imagick)
- Title override propre

### Securite

- XML-RPC desactive
- Version WP masquee
- REST API restreinte (routes publiques whitelistees)
- Anti brute-force (rate limiting sur le login)
- Enumeration utilisateurs bloquee
- Nonces + capabilities sur tous les formulaires/AJAX
- Prefixe tables `zz_` (pas `wp_`)
- Secrets dans `.env` (jamais dans le code)

### Performance

- Images auto-converties en WebP (85% qualite, max 1600px)
- Fonts servies en local (pas de Google Fonts CDN)
- GSAP/Swiper en local (pas de CDN)
- Pas de jQuery
- 1 CSS + 1 JS par page (pas de bundle global lourd)

### Mode maintenance

- Toggle dans Reglages > Maintenance
- Page "Bientot de retour" avec slideshow hero en fond + overlay + logo anime (trace SVG)
- HTTP 503 + Retry-After pour les moteurs de recherche
- Robots.txt → Disallow, sitemap → 503, meta noindex
- Admins connectes non impactes
- Pastille rouge dans la barre admin

---

## Installation en local

```bash
# 1. Cloner le repo
git clone git@github.com:VOTRE_USER/zougzoug.lan.git
cd zougzoug.lan

# 2. Installer WordPress
# Telecharger WP et extraire wp-admin/, wp-includes/, wp-*.php, index.php

# 3. Creer le fichier env-loader.php a la racine
# (voir section Configuration ci-dessous)

# 4. Creer le fichier .env a la racine
cp .env.example .env
# Remplir les valeurs (DB, URLs, salts)

# 5. Creer la base de donnees
mysql -u root -e "CREATE DATABASE zougzoug CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Installer les plugins requis via WP Admin > Extensions
# Contact Form 7, Rank Math SEO, Safe SVG

# 7. Activer le theme dans Apparence > Themes

# 8. Importer les contenus
# Les JSON sont deja dans data/ — les CPT se creent automatiquement
```

---

## Configuration pour la production

### 1. Fichier `.env`

Creer un fichier `.env` a la racine WordPress avec ces variables :

```env
# Environnement
WP_ENV=production
WP_DEBUG=false
WP_DEBUG_LOG=false
WP_DEBUG_DISPLAY=false

# Base de donnees (fournies par OVH)
DB_NAME=votre_db
DB_USER=votre_user
DB_PASSWORD=votre_mot_de_passe
DB_HOST=votre_host_mysql
DB_PREFIX=zz_

# URLs (IMPORTANT : mettre le vrai domaine)
WP_HOME=https://www.atelierzougzoug.fr
WP_SITEURL=https://www.atelierzougzoug.fr

# Salts (generer des nouvelles sur https://api.wordpress.org/secret-key/1.1/salt/)
AUTH_KEY='...'
SECURE_AUTH_KEY='...'
LOGGED_IN_KEY='...'
NONCE_KEY='...'
AUTH_SALT='...'
SECURE_AUTH_SALT='...'
LOGGED_IN_SALT='...'
NONCE_SALT='...'

# Securite
DISALLOW_FILE_EDIT=true
DISALLOW_FILE_MODS=false

# Cloudflare Turnstile (anti-spam formulaire contact)
# Creer les cles sur : https://dash.cloudflare.com/turnstile
TURNSTILE_SITE_KEY=votre_site_key
TURNSTILE_SECRET_KEY=votre_secret_key
```

### 2. Fichier `env-loader.php`

Ce fichier n'est pas dans le repo (secret). Le creer a la racine WordPress :

```php
<?php
$env_file = __DIR__ . '/.env';
if (!file_exists($env_file)) return;
$lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#')) continue;
    if (strpos($line, '=') === false) continue;
    list($key, $value) = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value, " \t\n\r\0\x0B'\"");
    if (!getenv($key)) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}
```

### 3. Fichier `wp-config.php`

Ce fichier n'est pas dans le repo. Le creer a la racine :

```php
<?php
require_once __DIR__ . '/env-loader.php';

define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

$table_prefix = getenv('DB_PREFIX') ?: 'wp_';

define('AUTH_KEY', getenv('AUTH_KEY'));
define('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY', getenv('LOGGED_IN_KEY'));
define('NONCE_KEY', getenv('NONCE_KEY'));
define('AUTH_SALT', getenv('AUTH_SALT'));
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT', getenv('LOGGED_IN_SALT'));
define('NONCE_SALT', getenv('NONCE_SALT'));

if (getenv('WP_HOME')) define('WP_HOME', getenv('WP_HOME'));
if (getenv('WP_SITEURL')) define('WP_SITEURL', getenv('WP_SITEURL'));

define('WP_DEBUG', filter_var(getenv('WP_DEBUG'), FILTER_VALIDATE_BOOLEAN));
define('WP_DEBUG_LOG', filter_var(getenv('WP_DEBUG_LOG'), FILTER_VALIDATE_BOOLEAN));
define('WP_DEBUG_DISPLAY', filter_var(getenv('WP_DEBUG_DISPLAY'), FILTER_VALIDATE_BOOLEAN));

define('DISALLOW_FILE_EDIT', filter_var(getenv('DISALLOW_FILE_EDIT'), FILTER_VALIDATE_BOOLEAN));
define('DISALLOW_FILE_MODS', filter_var(getenv('DISALLOW_FILE_MODS'), FILTER_VALIDATE_BOOLEAN));
define('FS_METHOD', 'direct');

if (getenv('TURNSTILE_SITE_KEY')) define('TURNSTILE_SITE_KEY', getenv('TURNSTILE_SITE_KEY'));
if (getenv('TURNSTILE_SECRET_KEY')) define('TURNSTILE_SECRET_KEY', getenv('TURNSTILE_SECRET_KEY'));

define('WP_POST_REVISIONS', 5);
define('EMPTY_TRASH_DAYS', 7);
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
require_once ABSPATH . 'wp-settings.php';
```

### 4. Checklist de mise en production

#### Avant le deploiement

- [ ] Generer de **nouveaux salts** sur https://api.wordpress.org/secret-key/1.1/salt/
- [ ] Creer les cles **Cloudflare Turnstile** sur https://dash.cloudflare.com/turnstile
- [ ] Mettre `WP_DEBUG=false` dans le `.env`
- [ ] Changer le mot de passe admin (ne pas garder celui de dev)

#### Apres le deploiement

- [ ] Installer les **plugins** : Contact Form 7, Rank Math SEO, Safe SVG, WP Rocket ou LiteSpeed Cache, Imagify, Wordfence
- [ ] Verifier que le formulaire de contact **envoie les emails** (tester en reel)
- [ ] Si emails en spam → installer **WP Mail SMTP** et configurer avec SMTP OVH ou Brevo
- [ ] Configurer **Rank Math** : verifier titres, descriptions, sitemap
- [ ] Configurer **Wordfence** : activer le firewall, scanner
- [ ] Configurer **WP Rocket** : activer le cache, minification CSS/JS, lazy load images
- [ ] Importer les **images** dans `/wp-content/uploads/` (pas dans le repo)
- [ ] Verifier les **permaliens** : Reglages > Permaliens > "Nom de l'article"
- [ ] Desactiver le **mode maintenance** : Reglages > Maintenance > decocher
- [ ] Tester toutes les pages en navigation privee

#### DNS & SSL

- [ ] Pointer le domaine vers l'hebergement OVH
- [ ] Activer le certificat SSL (Let's Encrypt via OVH)
- [ ] Forcer HTTPS dans `.htaccess`

#### Emails

Le formulaire CF7 utilise `wp_mail()`. Sur OVH mutualise, les emails passent par `mail()` PHP et risquent d'arriver en spam. Solutions :

1. **WP Mail SMTP** + SMTP OVH (inclus dans l'hebergement)
2. **WP Mail SMTP** + service externe (Brevo, Mailgun, SendGrid)
3. Configurer les enregistrements **SPF**, **DKIM** et **DMARC** dans la zone DNS

---

## Design tokens

```
Couleurs :
  --color-dark:     #1A1A1A
  --color-white:    #FFFFFF
  --color-bg-warm:  #F8F6F3
  --color-bg-main:  #FFFFFF
  --color-muted:    rgba(26,26,26,0.4)
  --color-border:   rgba(26,26,26,0.1)

Typographie :
  --font-family:    'General Sans', sans-serif
  --font-size-body: 18px
  --line-height:    1.7
  Poids : 400 (regular), 500 (medium), 600 (semibold)

Breakpoints :
  Tablet : 1024px
  Mobile : 768px
```

---

## Qualite du code

Dernier audit (2026-02-19) : **Note A (90.5%)**

| Axe | Score |
|-----|-------|
| Securite | 88% |
| Duplication | 66% |
| Consistance | 97% |
| Dependances | 100% |
| Code mort | 100% |

- Zero SQL injection, zero code mort, zero dependance inutile
- 100% nonces + capabilities sur les formulaires
- 27 fonctions definies, 27 utilisees
- Prefixe `zz_` sur toutes les fonctions et hooks custom

---

## Licence

Theme custom — Tous droits reserves.
Charlotte Auroux / Atelier ZougZoug.
