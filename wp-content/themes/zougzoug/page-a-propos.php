<?php
/**
 * Template Name: À propos
 * Page : À propos — Charlotte Auroux
 */

get_header();
$data = zz_get_data('about');
?>

  <!-- ======================== HERO — Portrait + Intro ======================== -->
  <section class="about-hero">
    <div class="about-hero-half about-hero-visual">
      <img src="<?php echo esc_url(zz_img($data['hero']['portrait'])); ?>" alt="<?php echo esc_attr($data['hero']['portrait_alt']); ?>">
    </div>
    <div class="about-hero-half about-hero-content">
      <span class="about-label"><?php echo esc_html($data['hero']['label']); ?></span>
      <h1 class="about-hero-title"><?php echo esc_html($data['hero']['name']); ?></h1>
      <div class="about-hero-text"><?php echo wp_kses_post($data['hero']['text']); ?></div>
    </div>
  </section>

  <!-- Spacer pour le hero fixe -->
  <div class="about-hero-spacer"></div>

  <!-- ======================== BLOCS DIPTYQUES ALTERNÉS ======================== -->
  <div class="about-sections">
    <?php foreach ($data['blocs'] as $bloc) : ?>
    <section class="about-block<?php echo !empty($bloc['reverse']) ? ' about-block--reverse' : ''; ?>">
      <div class="about-half about-half--visual">
        <img src="<?php echo esc_url(zz_img($bloc['image'])); ?>" alt="<?php echo esc_attr($bloc['image_alt']); ?>">
      </div>
      <div class="about-half about-half--content">
        <span class="section-label"><?php echo esc_html($bloc['label']); ?></span>
        <h2 class="about-tagline"><?php echo esc_html($bloc['tagline']); ?></h2>
        <?php foreach ($bloc['texts'] as $text) : ?>
        <div class="about-text"><?php echo wp_kses_post($text); ?></div>
        <?php endforeach; ?>
        <?php if (!empty($bloc['cta'])) : ?>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="cta-button">Discuter d'un projet</a>
        <?php endif; ?>
      </div>
    </section>
    <?php endforeach; ?>
  </div>

  <!-- Spacer pour permettre au dernier bloc sticky de rester visible -->
  <div class="about-sections-spacer"></div>

  <!-- ======================== CTA FINAL ======================== -->
  <section class="cta-final">
    <div class="cta-final-text"><?php echo wp_kses_post($data['cta']['text']); ?></div>
    <a href="<?php echo esc_url(home_url($data['cta']['url'])); ?>" class="cta-button cta-button--light"><?php echo esc_html($data['cta']['button']); ?></a>
  </section>

<?php get_footer(); ?>
