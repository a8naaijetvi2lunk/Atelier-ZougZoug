<?php
/**
 * Template Name: Revendeurs
 * Page : Revendeurs & Évènements
 */

get_header();
$data = zz_get_data('revendeurs');
$img  = get_template_directory_uri() . '/assets/img/';
?>

  <!-- ======================== HERO — Titre centré ======================== -->
  <section class="rev-hero">
    <div class="rev-hero-inner">
      <span class="section-label"><?php echo esc_html($data['hero']['label']); ?></span>
      <h1 class="rev-hero-title"><?php echo nl2br(esc_html($data['hero']['title'])); ?></h1>
      <p class="rev-hero-text"><?php echo esc_html($data['hero']['text']); ?></p>
    </div>
  </section>

  <!-- ======================== POINTS DE VENTE ======================== -->
  <section class="rev-lieux">
    <div class="rev-lieux-inner">
      <span class="section-label"><?php echo esc_html($data['lieux_label']); ?></span>
      <h2 class="rev-lieux-title"><?php echo esc_html($data['lieux_title']); ?></h2>

      <div class="rev-lieux-grid">
        <?php foreach ($data['lieux'] as $lieu) : ?>
        <div class="rev-lieu-card<?php echo !empty($lieu['is_atelier']) ? ' rev-lieu-card--atelier' : ''; ?>">
          <span class="rev-lieu-type"><?php echo esc_html($lieu['type']); ?></span>
          <h3 class="rev-lieu-nom"><?php echo esc_html($lieu['nom']); ?></h3>
          <p class="rev-lieu-adresse"><?php echo nl2br(esc_html($lieu['adresse'])); ?></p>
          <?php if (!empty($lieu['note'])) : ?>
          <p class="rev-lieu-note"><?php echo esc_html($lieu['note']); ?></p>
          <?php endif; ?>
          <?php if (!empty($lieu['link'])) : ?>
          <a href="<?php echo esc_url($lieu['link']); ?>" class="rev-lieu-link"><?php echo esc_html($lieu['link_text']); ?></a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ======================== BANDE PHOTO ======================== -->
  <section class="rev-photos">
    <?php foreach ($data['photos'] as $photo) : ?>
    <div class="rev-photo">
      <img src="<?php echo esc_url($img . $photo['src']); ?>" alt="<?php echo esc_attr($photo['alt']); ?>">
    </div>
    <?php endforeach; ?>
  </section>

  <!-- ======================== AGENDA / ÉVÉNEMENTS ======================== -->
  <section class="rev-agenda">
    <div class="rev-agenda-inner">
      <span class="section-label"><?php echo esc_html($data['agenda_label']); ?></span>
      <h2 class="rev-agenda-title"><?php echo esc_html($data['agenda_title']); ?></h2>

      <div class="rev-agenda-list">
        <?php foreach ($data['evenements'] as $event) : ?>
        <div class="rev-event">
          <div class="rev-event-date">
            <span class="rev-event-month"><?php echo esc_html($event['month']); ?></span>
            <span class="rev-event-year"><?php echo esc_html($event['year']); ?></span>
          </div>
          <div class="rev-event-content">
            <h3 class="rev-event-nom"><?php echo esc_html($event['nom']); ?></h3>
            <?php if (!empty($event['details'])) : ?>
            <p class="rev-event-details"><?php echo esc_html($event['details']); ?></p>
            <?php endif; ?>
            <p class="rev-event-lieu"><?php echo esc_html($event['lieu']); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <p class="rev-agenda-note"><?php echo esc_html($data['agenda_note']); ?> <a href="<?php echo esc_url($data['agenda_note_url']); ?>" target="_blank" rel="noopener"><?php echo esc_html($data['agenda_note_link']); ?></a>.</p>

      <!-- Expositions passées -->
      <?php if (!empty($data['expos'])) : ?>
      <div class="rev-expo">
        <span class="section-label"><?php echo esc_html($data['expos_label']); ?></span>
        <?php foreach ($data['expos'] as $expo) : ?>
        <div class="rev-event rev-event--past">
          <div class="rev-event-date">
            <span class="rev-event-month"><?php echo esc_html($expo['month']); ?></span>
            <span class="rev-event-year"><?php echo esc_html($expo['year']); ?></span>
          </div>
          <div class="rev-event-content">
            <h3 class="rev-event-nom"><?php echo esc_html($expo['nom']); ?></h3>
            <p class="rev-event-lieu"><?php echo esc_html($expo['lieu']); ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ======================== CTA FINAL ======================== -->
  <section class="cta-final">
    <p class="cta-final-text"><?php echo nl2br(esc_html($data['cta']['text'])); ?></p>
    <a href="<?php echo esc_url($data['cta']['url']); ?>" class="cta-button cta-button--light"><?php echo esc_html($data['cta']['button']); ?></a>
  </section>

<?php get_footer(); ?>
