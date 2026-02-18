<?php
/**
 * Template Name: Contact
 * Page : Contact — Formulaire + infos
 */

get_header();
$data = zz_get_data('contact');
?>

  <!-- ======================== FORMULAIRE DE CONTACT ======================== -->
  <section class="contact-form-section">
    <div class="contact-form-inner">
      <div class="contact-form-header">
        <span class="section-label"><?php echo esc_html($data['form']['label']); ?></span>
        <div class="contact-form-title"><?php echo wp_kses_post($data['form']['title']); ?></div>
      </div>
      <?php echo do_shortcode('[contact-form-7 id="265" title="Contact ZougZoug" html_class="contact-form"]'); ?>
    </div>
  </section>

  <!-- ======================== INFOS CONTACT — Photo + coordonnées ======================== -->
  <section class="contact-hero">
    <div class="contact-half contact-half--visual">
      <img src="<?php echo esc_url(zz_img($data['info']['photo'])); ?>" alt="<?php echo esc_attr($data['info']['photo_alt']); ?>">
    </div>
    <div class="contact-half contact-half--info">
      <span class="section-label"><?php echo esc_html($data['info']['label']); ?></span>
      <h2 class="contact-name"><?php echo esc_html($data['info']['name']); ?></h2>
      <p class="contact-studio"><?php echo esc_html($data['info']['studio']); ?></p>
      <div class="contact-details">
        <a href="mailto:<?php echo esc_attr($data['info']['email']); ?>" class="contact-email"><?php echo esc_html($data['info']['email']); ?></a>
        <a href="tel:<?php echo esc_attr($data['info']['phone_link']); ?>" class="contact-tel"><?php echo esc_html($data['info']['phone']); ?></a>
        <div class="contact-address"><?php echo wp_kses_post($data['info']['address']); ?></div>
      </div>
      <div class="contact-social">
        <a href="<?php echo esc_url($data['info']['instagram']); ?>" target="_blank" rel="noopener" class="contact-insta">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5"/></svg>
          <span><?php echo esc_html($data['info']['instagram_handle']); ?></span>
        </a>
      </div>
    </div>
  </section>

  <!-- ======================== BANDE PHOTOS ======================== -->
  <section class="contact-photos">
    <?php foreach ($data['photos'] as $photo) : ?>
    <div class="contact-photo">
      <img src="<?php echo esc_url(zz_img($photo['attachment_id'] ?? $photo['src'] ?? '')); ?>" alt="<?php echo esc_attr($photo['alt'] ?? ''); ?>">
    </div>
    <?php endforeach; ?>
  </section>

  <!-- ======================== CTA FINAL ======================== -->
  <section class="cta-final">
    <div class="cta-final-text"><?php echo wp_kses_post($data['cta']['text']); ?></div>
    <a href="<?php echo esc_url($data['cta']['url']); ?>" class="cta-button cta-button--light"><?php echo esc_html($data['cta']['button']); ?></a>
  </section>

<?php get_footer(); ?>
