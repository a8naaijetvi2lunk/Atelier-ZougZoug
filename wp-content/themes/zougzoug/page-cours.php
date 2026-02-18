<?php
/**
 * Template Name: Cours
 * Page : Cours de céramique
 */

get_header();
$data = zz_get_data('cours');
?>

  <!-- ======================== HERO — Photo immersive ======================== -->
  <section class="cours-hero">
    <img src="<?php echo esc_url(zz_img($data['hero']['image'])); ?>" alt="<?php echo esc_attr($data['hero']['image_alt']); ?>" class="cours-hero-img">
    <div class="cours-hero-overlay"></div>
    <div class="cours-hero-content">
      <span class="section-label section-label--light"><?php echo esc_html($data['hero']['label']); ?></span>
      <h1 class="cours-hero-title"><?php echo esc_html($data['hero']['title']); ?></h1>
    </div>
  </section>

  <!-- ======================== INTRO ======================== -->
  <section class="cours-intro">
    <div class="cours-intro-inner">
      <p class="cours-intro-accroche"><?php echo esc_html($data['intro']['accroche']); ?></p>
      <div class="cours-intro-text"><?php echo wp_kses_post($data['intro']['text']); ?></div>
    </div>
  </section>

  <!-- ======================== OFFRES ======================== -->
  <section class="cours-offres">
    <div class="cours-offres-inner">
      <span class="section-label"><?php echo esc_html($data['offres_label']); ?></span>
      <h2 class="cours-offres-title"><?php echo esc_html($data['offres_title']); ?></h2>

      <div class="cours-offres-grid">
        <?php foreach ($data['offres'] as $offre) : ?>
        <div class="offre-card">
          <h3 class="offre-nom"><?php echo esc_html($offre['nom']); ?></h3>
          <div class="offre-desc"><?php echo wp_kses_post($offre['description']); ?></div>
          <p class="offre-prix"><?php echo esc_html($offre['prix']); ?><span class="offre-devise">€</span></p>
          <div class="offre-infos">
            <?php foreach ($offre['infos'] as $info) : ?>
            <span><?php echo esc_html($info); ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="cours-offres-cta">
        <p class="cours-offres-note"><?php echo esc_html($data['privatisation']); ?></p>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="cta-button">M'écrire</a>
      </div>

      <div class="cours-wecandoo-note">
        <p><?php echo esc_html($data['wecandoo']['text']); ?> <a href="<?php echo esc_url($data['wecandoo']['url']); ?>" target="_blank" rel="noopener"><?php echo esc_html($data['wecandoo']['link_text']); ?></a>.</p>
      </div>
    </div>
  </section>

  <!-- ======================== GALERIE PHOTOS ======================== -->
  <section class="cours-galerie">
    <div class="cours-galerie-grid">
      <?php foreach ($data['galerie']['images'] as $photo) : ?>
      <div class="cours-galerie-photo">
        <img src="<?php echo esc_url(zz_img($photo['attachment_id'] ?? $photo['src'] ?? '')); ?>" alt="<?php echo esc_attr($photo['alt'] ?? ''); ?>">
      </div>
      <?php endforeach; ?>
    </div>
  </section>

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
            <div><?php echo wp_kses_post($faq['answer']); ?></div>
          </div>
        </details>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ======================== CTA FINAL ======================== -->
  <section class="cta-final">
    <div class="cta-final-text"><?php echo wp_kses_post($data['cta']['text']); ?></div>
    <a href="<?php echo esc_url($data['cta']['url']); ?>" class="cta-button cta-button--light"><?php echo esc_html($data['cta']['button']); ?></a>
  </section>

<?php get_footer(); ?>
