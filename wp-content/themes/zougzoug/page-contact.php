<?php
/**
 * Template Name: Contact
 * Page : Contact — Formulaire + infos
 */

get_header();
$data = zz_get_data('contact');
$img  = get_template_directory_uri() . '/assets/img/';
?>

  <!-- ======================== FORMULAIRE DE CONTACT ======================== -->
  <section class="contact-form-section">
    <div class="contact-form-inner">
      <div class="contact-form-header">
        <span class="section-label"><?php echo esc_html($data['form']['label']); ?></span>
        <h1 class="contact-form-title"><?php echo nl2br(esc_html($data['form']['title'])); ?></h1>
      </div>
      <?php
      // Contact Form 7 shortcode — will be configured later
      $cf7_form = do_shortcode('[contact-form-7 id="" title="Contact"]');
      if ($cf7_form && strpos($cf7_form, '[contact-form-7') === false) {
        echo $cf7_form;
      } else {
        // Fallback form matching the maquette
      ?>
      <form class="contact-form" action="#" method="POST">
        <div class="form-row">
          <div class="form-group">
            <label for="contact-nom">Nom</label>
            <input type="text" id="contact-nom" name="nom" required>
          </div>
          <div class="form-group">
            <label for="contact-prenom">Prénom</label>
            <input type="text" id="contact-prenom" name="prenom" required>
          </div>
        </div>
        <div class="form-group">
          <label for="contact-email-field">Email</label>
          <input type="email" id="contact-email-field" name="email" required>
        </div>
        <div class="form-group">
          <label for="contact-sujet">Sujet</label>
          <select id="contact-sujet" name="sujet">
            <option value="" disabled selected>Choisir un sujet</option>
            <option value="sur-mesure">Projet sur mesure</option>
            <option value="luminaires">Luminaires & architecture</option>
            <option value="vaisselle">Vaisselle & art de la table</option>
            <option value="cours">Cours de céramique</option>
            <option value="autre">Autre</option>
          </select>
        </div>
        <div class="form-group">
          <label for="contact-message">Message</label>
          <textarea id="contact-message" name="message" rows="6" required></textarea>
        </div>
        <button type="submit" class="cta-button">Envoyer</button>
      </form>
      <?php } ?>
    </div>
  </section>

  <!-- ======================== INFOS CONTACT — Photo + coordonnées ======================== -->
  <section class="contact-hero">
    <div class="contact-half contact-half--visual">
      <img src="<?php echo esc_url($img . $data['info']['photo']); ?>" alt="<?php echo esc_attr($data['info']['photo_alt']); ?>">
    </div>
    <div class="contact-half contact-half--info">
      <span class="section-label"><?php echo esc_html($data['info']['label']); ?></span>
      <h2 class="contact-name"><?php echo esc_html($data['info']['name']); ?></h2>
      <p class="contact-studio"><?php echo esc_html($data['info']['studio']); ?></p>
      <div class="contact-details">
        <a href="mailto:<?php echo esc_attr($data['info']['email']); ?>" class="contact-email"><?php echo esc_html($data['info']['email']); ?></a>
        <a href="tel:<?php echo esc_attr($data['info']['phone_link']); ?>" class="contact-tel"><?php echo esc_html($data['info']['phone']); ?></a>
        <p class="contact-address"><?php echo nl2br(esc_html($data['info']['address'])); ?></p>
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
      <img src="<?php echo esc_url($img . $photo['src']); ?>" alt="<?php echo esc_attr($photo['alt']); ?>">
    </div>
    <?php endforeach; ?>
  </section>

  <!-- ======================== CTA FINAL ======================== -->
  <section class="cta-final">
    <p class="cta-final-text"><?php echo nl2br(esc_html($data['cta']['text'])); ?></p>
    <a href="<?php echo esc_url($data['cta']['url']); ?>" class="cta-button cta-button--light"><?php echo esc_html($data['cta']['button']); ?></a>
  </section>

<?php get_footer(); ?>
