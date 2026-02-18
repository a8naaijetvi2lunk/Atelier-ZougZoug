<?php
/**
 * Template Name: Cours
 * Page : Cours de céramique
 */

get_header();
$data = zz_get_data('cours');
$img  = get_template_directory_uri() . '/assets/img/';
?>

  <!-- ======================== HERO — Photo immersive ======================== -->
  <section class="cours-hero">
    <img src="<?php echo esc_url($img . $data['hero']['image']); ?>" alt="<?php echo esc_attr($data['hero']['image_alt']); ?>" class="cours-hero-img">
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
      <p class="cours-intro-text"><?php echo esc_html($data['intro']['text']); ?></p>
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
          <p class="offre-desc"><?php echo esc_html($offre['description']); ?></p>
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
        <a href="mailto:atelierzougzoug@gmail.com" class="cta-button">M'écrire</a>
      </div>

      <div class="cours-wecandoo-note">
        <p><?php echo esc_html($data['wecandoo']['text']); ?> <a href="<?php echo esc_url($data['wecandoo']['url']); ?>" target="_blank" rel="noopener"><?php echo esc_html($data['wecandoo']['link_text']); ?></a>.</p>
      </div>
    </div>
  </section>

  <!-- ======================== GALERIE PHOTOS ======================== -->
  <section class="cours-galerie">
    <div class="cours-galerie-grid">
      <?php foreach ($data['galerie'] as $photo) : ?>
      <div class="cours-galerie-photo">
        <img src="<?php echo esc_url($img . $photo['src']); ?>" alt="<?php echo esc_attr($photo['alt']); ?>">
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ======================== CTA FINAL ======================== -->
  <section class="cta-final">
    <p class="cta-final-text"><?php echo nl2br(esc_html($data['cta']['text'])); ?></p>
    <a href="<?php echo esc_url($data['cta']['url']); ?>" class="cta-button cta-button--light"><?php echo esc_html($data['cta']['button']); ?></a>
  </section>

<?php get_footer(); ?>
