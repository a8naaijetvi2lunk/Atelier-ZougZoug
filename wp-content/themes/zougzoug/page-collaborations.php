<?php
/**
 * Template Name: Collaborations
 * Page : Collaborations & Sur-mesure — Grille projets + Lightbox
 */

get_header();
?>

  <!-- ======================== HERO PROJETS ======================== -->
  <section class="projets-hero">
    <div class="projets-hero-inner">
      <span class="section-label">Collaborations & Sur-mesure</span>
      <h1 class="projets-title">Le sur-mesure comme point de départ.</h1>
      <p class="projets-intro">Restaurants, hôtels, maisons de parfums — entre matière brute et précision d'usage, des pièces conçues en dialogue avec chaque univers.</p>
      <div class="projets-filters">
        <button class="filter-btn is-active" data-filter="all">Tous</button>
        <button class="filter-btn" data-filter="table">Art de la Table</button>
        <button class="filter-btn" data-filter="luminaires">Luminaires</button>
        <button class="filter-btn" data-filter="accessoires">Accessoires</button>
      </div>
    </div>
  </section>

  <!-- ======================== GRILLE PROJETS ======================== -->
  <section class="projets-grid-section">
    <div class="projets-grid" id="projets-grid">
      <!-- Cards generees par projets.js -->
    </div>
  </section>

  <!-- ======================== LIGHTBOX ======================== -->
  <div class="lightbox" id="lightbox" aria-hidden="true">
    <button class="lightbox-close" id="lightbox-close" aria-label="Fermer la galerie">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </button>
    <div class="lightbox-layout">
      <div class="lightbox-sidebar" id="lightbox-sidebar">
        <!-- Injecte par projets.js -->
      </div>
      <div class="lightbox-gallery" id="lightbox-grid">
        <!-- Injecte par projets.js -->
      </div>
    </div>
  </div>

  <!-- ======================== CTA FINAL ======================== -->
  <section class="cta-final">
    <p class="cta-final-text">Un projet sur mesure en céramique ?<br>Parlons-en.</p>
    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="cta-button cta-button--light">Me contacter</a>
  </section>

<?php get_footer(); ?>
