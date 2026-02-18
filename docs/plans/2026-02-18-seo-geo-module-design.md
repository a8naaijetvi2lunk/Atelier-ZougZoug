# Design : Module SEO/GEO — Atelier ZougZoug

**Date :** 2026-02-18
**Approche :** Module custom dans le theme (zero plugin SEO)
**Reference :** DOCUMENTATIONS/AUDIT-SEO.md

---

## 1. Architecture

Un fichier PHP `inc/seo.php` inclus dans `functions.php`. Il :

- Lit les donnees SEO depuis `data/*.json` (bloc `seo` par page)
- Utilise `data/global.json` comme fallback
- Injecte dans `wp_head` : meta description, canonical, OG, Twitter Card, Schema JSON-LD
- Genere `/sitemap.xml` dynamiquement
- Configure `robots.txt` via le filtre WP

Pas de plugin, pas de table DB supplementaire. Tout vit dans les JSON existants.

---

## 2. Stockage des donnees

### Pages statiques (JSON)

Chaque `data/*.json` recoit un bloc `seo` :

```json
{
  "seo": {
    "title": "Charlotte Auroux, ceramiste a Brioude — Atelier ZougZoug",
    "description": "Charlotte Auroux, ceramiste a Brioude (Auvergne). Luminaires, vaisselle et art de la table en ceramique sur mesure.",
    "og_image": 245,
    "canonical": ""
  },
  "hero": { "..." }
}
```

- `title` : titre SEO (max 60 car). Si vide → `wp_title()` par defaut
- `description` : meta description (max 160 car). Si vide → `global.json` fallback
- `og_image` : attachment ID. Si vide → `global.json` → `meta.og_image`
- `canonical` : URL canonique. Si vide → URL courante auto

### Page Cours — FAQ additionnelle

`cours.json` recoit aussi un bloc `faq` :

```json
{
  "faq": [
    {
      "question": "Quel materiel est fourni pour les cours ?",
      "answer": "Tout le materiel est fourni : argile, outils de tournage, emaux. Vous repartez avec votre piece apres cuisson."
    }
  ]
}
```

### Projets (CPT)

Meta WordPress sur chaque post `projet` :
- `_projet_seo_title` (string)
- `_projet_seo_description` (string)
- `_projet_seo_og_image` (int — attachment ID)

### Global (fallbacks)

`global.json` — section `meta` existante, etendue :

```json
{
  "meta": {
    "description": "Atelier ZougZoug — Charlotte Auroux, ceramiste a Brioude...",
    "og_image": "og-default.webp",
    "site_name": "Atelier ZougZoug",
    "locale": "fr_FR"
  }
}
```

---

## 3. Meta tags — Sortie HTML

Injectes dans `wp_head` (priorite 1, avant les autres) :

```html
<!-- SEO -->
<meta name="description" content="...">
<link rel="canonical" href="...">

<!-- Open Graph -->
<meta property="og:type" content="website">
<meta property="og:locale" content="fr_FR">
<meta property="og:site_name" content="Atelier ZougZoug">
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:url" content="...">
<meta property="og:image" content="...">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="...">
<meta name="twitter:description" content="...">
<meta name="twitter:image" content="...">
```

### Logique de resolution

1. Page statique → `data/{page}.json` → `seo.title` / `seo.description` / `seo.og_image`
2. CPT projet → `get_post_meta($id, '_projet_seo_title')` etc.
3. Fallback → `global.json` → `meta.description` / `meta.og_image`
4. Dernier fallback → `wp_title()` / pas de description

---

## 4. Schema.org JSON-LD — 6 types

Injectes via `<script type="application/ld+json">` dans `wp_head`.

### 4.1 Organization (toutes les pages)

```json
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "@id": "https://atelierzougzoug.fr/#organization",
  "name": "Atelier ZougZoug",
  "alternateName": "Charlotte Auroux Ceramiste",
  "description": "Ceramiste a Brioude, Auvergne...",
  "url": "https://atelierzougzoug.fr",
  "telephone": "+33660199818",
  "email": "atelierzougzoug@gmail.com",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "6 rue de la Terrasse",
    "addressLocality": "Brioude",
    "postalCode": "43100",
    "addressRegion": "Auvergne-Rhone-Alpes",
    "addressCountry": "FR"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 45.2949,
    "longitude": 3.3853
  },
  "sameAs": ["https://www.instagram.com/atelier_zougzoug/"],
  "founder": {
    "@type": "Person",
    "@id": "https://atelierzougzoug.fr/#person",
    "name": "Charlotte Auroux"
  }
}
```

Donnees lues depuis `global.json` (footer.address, footer.phone, etc.).

### 4.2 Person (page A propos)

```json
{
  "@context": "https://schema.org",
  "@type": "Person",
  "@id": "https://atelierzougzoug.fr/#person",
  "name": "Charlotte Auroux",
  "jobTitle": "Ceramiste",
  "worksFor": { "@id": "https://atelierzougzoug.fr/#organization" },
  "knowsAbout": ["ceramique", "poterie", "tournage", "emaux", "luminaires"],
  "workLocation": {
    "@type": "Place",
    "name": "Atelier ZougZoug",
    "address": { "..." }
  }
}
```

### 4.3 BreadcrumbList (toutes sauf accueil)

```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    { "@type": "ListItem", "position": 1, "name": "Accueil", "item": "https://atelierzougzoug.fr/" },
    { "@type": "ListItem", "position": 2, "name": "Cours de ceramique", "item": "https://atelierzougzoug.fr/cours/" }
  ]
}
```

### 4.4 Course (page Cours)

Genere depuis `cours.json` → `offres[]` :

```json
{
  "@context": "https://schema.org",
  "@type": "Course",
  "name": "Cours de ceramique a Brioude",
  "provider": { "@id": "https://atelierzougzoug.fr/#organization" },
  "hasCourseInstance": [
    {
      "@type": "CourseInstance",
      "name": "Initiation tournage",
      "courseMode": "onsite",
      "offers": { "@type": "Offer", "price": "55", "priceCurrency": "EUR" }
    }
  ]
}
```

### 4.5 Event (page Revendeurs)

Genere depuis `revendeurs.json` → `agenda[]` :

```json
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "Marche des potiers de Craponne",
  "startDate": "2026-07-12",
  "location": { "@type": "Place", "name": "Craponne-sur-Arzon" },
  "organizer": { "@id": "https://atelierzougzoug.fr/#organization" }
}
```

### 4.6 FAQPage (page Cours uniquement)

Genere depuis `cours.json` → `faq[]` :

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Quel materiel est fourni ?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Tout le materiel est fourni..."
      }
    }
  ]
}
```

La FAQ est aussi affichee en HTML visible (accordeon) sur la page.

---

## 5. GEO — Optimisations IA

- **Schema complet** avec `@id` cross-references entre Organization, Person, Course, Event
- **sameAs** vers Instagram (et futur Google Business Profile)
- **Robots.txt** autorise GPTBot et ChatGPT-User
- **FAQ visible** sur la page Cours (accordeon) + Schema FAQPage
- **Contenu structure** : H1 optimises, paragraphes courts, listes

---

## 6. Images OG

- Taille WP custom : `og-image` (1200x630, hard crop)
- Enregistree via `add_image_size('og-image', 1200, 630, true)` dans setup.php
- Chaque page a `seo.og_image` (attachment ID) dans son JSON
- Le module lit l'ID, recupere l'URL du crop `og-image`
- Fallback : `global.json` → `meta.og_image`

---

## 7. Backoffice — Section SEO dans chaque editeur

Ajout d'une section "SEO & Partage" dans l'editeur existant :

- **Title SEO** : input text + compteur caracteres (cible 60 max)
- **Meta description** : textarea + compteur caracteres (cible 160 max)
- **Image OG** : bouton media library + preview 600x315 (ratio 1200x630)
- **URL canonique** : input pre-rempli auto, editable

Pour la page Cours, section supplementaire :
- **FAQ** : blocs question/reponse (ajout/suppression dynamique)

Schema dans `admin-api.php` etendu pour valider les blocs `seo` et `faq`.

---

## 8. Sitemap XML

Fichier `inc/seo.php` genere `/sitemap.xml` via `template_redirect` :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>https://atelierzougzoug.fr/</loc>
    <lastmod>2026-02-18</lastmod>
    <priority>1.0</priority>
  </url>
  <!-- Pages statiques -->
  <!-- Projets CPT -->
</urlset>
```

---

## 9. Robots.txt

Via filtre WP `robots_txt` :

```
User-agent: *
Allow: /
Sitemap: https://atelierzougzoug.fr/sitemap.xml

User-agent: GPTBot
Allow: /

User-agent: ChatGPT-User
Allow: /

User-agent: Bingbot
Allow: /

User-agent: OAI-SearchBot
Allow: /
```

---

## Fichiers a creer/modifier

| Fichier | Action |
|---------|--------|
| `inc/seo.php` | **NOUVEAU** — Module complet |
| `inc/setup.php` | Ajouter `add_image_size('og-image', 1200, 630, true)` |
| `functions.php` | Ajouter `require inc/seo.php` |
| `data/global.json` | Etendre section `meta` |
| `data/home.json` | Ajouter bloc `seo` |
| `data/about.json` | Ajouter bloc `seo` |
| `data/contact.json` | Ajouter bloc `seo` |
| `data/cours.json` | Ajouter blocs `seo` + `faq` |
| `data/revendeurs.json` | Ajouter bloc `seo` |
| `inc/admin/admin-editor.js` | Ajouter section SEO dans l'editeur |
| `inc/admin/admin-editor.css` | Styles section SEO |
| `inc/admin/admin-api.php` | Ajouter `seo`/`faq` aux schemas |
| `page-cours.php` | Ajouter bloc FAQ HTML (accordeon) |
| `assets/css/cours.css` | Styles accordeon FAQ |
| `assets/js/cours.js` | Toggle accordeon FAQ |
