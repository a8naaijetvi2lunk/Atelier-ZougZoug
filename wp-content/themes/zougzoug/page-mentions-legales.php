<?php
/**
 * Template Name: Mentions légales
 * Page : Politique de confidentialité & Mentions légales
 */

get_header();
$data = zz_get_data('mentions');
?>

  <!-- ======================== HERO ======================== -->
  <section class="mentions-hero">
    <div class="mentions-hero-inner">
      <h1 class="mentions-hero-title"><?php echo esc_html($data['title']); ?></h1>
      <p class="mentions-updated">Dernière mise à jour : <?php echo esc_html($data['last_updated']); ?></p>
    </div>
  </section>

  <!-- ======================== CONTENU ======================== -->
  <section class="mentions-content">
    <div class="mentions-content-inner">
      <?php foreach ($data['sections'] as $i => $section) : ?>
      <article class="mentions-section">
        <h2 class="mentions-section-title"><?php echo esc_html($section['title']); ?></h2>
        <div class="mentions-section-body">
          <?php echo wp_kses_post($section['content']); ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ======================== CTA FINAL ======================== -->
  <section class="cta-final">
    <p class="cta-final-text">Une question sur vos données ?<br>Contactez-nous.</p>
    <a href="/contact/" class="cta-button cta-button--light">Prendre contact</a>
  </section>

<?php get_footer(); ?>
