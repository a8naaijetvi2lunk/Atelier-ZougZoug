<?php
/**
 * Template Name: Revendeurs
 * Page : Revendeurs & Évènements
 */

get_header();
$data = zz_get_data('revendeurs');
?>

  <!-- ======================== HERO — Titre centré ======================== -->
  <section class="rev-hero">
    <div class="rev-hero-inner">
      <span class="section-label"><?php echo esc_html($data['hero']['label']); ?></span>
      <div class="rev-hero-title"><?php echo wp_kses_post($data['hero']['title']); ?></div>
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
          <div class="rev-lieu-adresse"><?php echo wp_kses_post($lieu['adresse']); ?></div>
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
    <?php foreach ($data['photos']['images'] as $photo) : ?>
    <div class="rev-photo">
      <img src="<?php echo esc_url(zz_img($photo['attachment_id'] ?? $photo['src'] ?? '')); ?>" alt="<?php echo esc_attr($photo['alt'] ?? ''); ?>">
    </div>
    <?php endforeach; ?>
  </section>

  <!-- ======================== AGENDA / ÉVÉNEMENTS (CPT) ======================== -->
  <?php
  $today = date('Y-m-d');

  // Événements à venir (tous types)
  $upcoming = new WP_Query([
    'post_type'      => 'evenement',
    'posts_per_page' => -1,
    'meta_key'       => '_event_date_start',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
    'meta_query'     => [
      'relation' => 'AND',
      [
        'key'     => '_event_date_start',
        'value'   => $today,
        'compare' => '>=',
        'type'    => 'DATE',
      ],
      [
        'relation' => 'OR',
        [
          'key'     => '_event_passed',
          'compare' => 'NOT EXISTS',
        ],
        [
          'key'     => '_event_passed',
          'value'   => '1',
          'compare' => '!=',
        ],
      ],
    ],
  ]);

  // Expositions passées
  $past_expos = new WP_Query([
    'post_type'      => 'evenement',
    'posts_per_page' => -1,
    'meta_key'       => '_event_date_start',
    'orderby'        => 'meta_value',
    'order'          => 'DESC',
    'tax_query'      => [
      [
        'taxonomy' => 'type_evenement',
        'field'    => 'slug',
        'terms'    => 'exposition',
      ],
    ],
    'meta_query'     => [
      'relation' => 'OR',
      [
        'key'     => '_event_passed',
        'value'   => '1',
        'compare' => '=',
      ],
      [
        'key'     => '_event_date_start',
        'value'   => $today,
        'compare' => '<',
        'type'    => 'DATE',
      ],
    ],
  ]);
  ?>

  <section class="rev-agenda">
    <div class="rev-agenda-inner">
      <span class="section-label">Agenda</span>
      <h2 class="rev-agenda-title">Marchés, salons &amp; expositions.</h2>

      <?php if ($upcoming->have_posts()) : ?>
      <div class="rev-agenda-list">
        <?php while ($upcoming->have_posts()) : $upcoming->the_post();
          $start = get_post_meta(get_the_ID(), '_event_date_start', true);
          $end   = get_post_meta(get_the_ID(), '_event_date_end', true);
          $lieu  = get_post_meta(get_the_ID(), '_event_lieu', true);
          $ville = get_post_meta(get_the_ID(), '_event_ville', true);
          $month = $start ? date_i18n('M', strtotime($start)) : '';
          $year  = $start ? date_i18n('Y', strtotime($start)) : '';
          $details = '';
          if ($start && $end) {
            $details = date_i18n('j', strtotime($start)) . ' — ' . date_i18n('j F', strtotime($end));
          } elseif ($start) {
            $details = date_i18n('j F', strtotime($start));
          }
          $location = $lieu ? $lieu : '';
          if ($ville) $location .= $location ? ', ' . $ville : $ville;
          $url       = get_post_meta(get_the_ID(), '_event_url', true);
          $url_label = get_post_meta(get_the_ID(), '_event_url_label', true);
        ?>
        <div class="rev-event">
          <div class="rev-event-date">
            <span class="rev-event-month"><?php echo esc_html(ucfirst($month)); ?></span>
            <span class="rev-event-year"><?php echo esc_html($year); ?></span>
          </div>
          <div class="rev-event-content">
            <h3 class="rev-event-nom"><?php the_title(); ?></h3>
            <?php if ($details) : ?>
            <p class="rev-event-details"><?php echo esc_html($details); ?></p>
            <?php endif; ?>
            <?php if ($location) : ?>
            <p class="rev-event-lieu"><?php echo esc_html($location); ?></p>
            <?php endif; ?>
            <?php if ($url) : ?>
            <a href="<?php echo esc_url($url); ?>" class="rev-event-link" target="_blank" rel="noopener"><?php echo esc_html($url_label ?: 'En savoir plus'); ?> →</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
      <?php else : ?>
      <p class="rev-agenda-empty">Aucun événement à venir pour le moment.</p>
      <?php endif; ?>

      <!-- Expositions passées -->
      <?php if ($past_expos->have_posts()) : ?>
      <div class="rev-expo">
        <span class="section-label">Expositions</span>
        <?php while ($past_expos->have_posts()) : $past_expos->the_post();
          $start = get_post_meta(get_the_ID(), '_event_date_start', true);
          $lieu  = get_post_meta(get_the_ID(), '_event_lieu', true);
          $ville = get_post_meta(get_the_ID(), '_event_ville', true);
          $month = $start ? date_i18n('M', strtotime($start)) : '';
          $year  = $start ? date_i18n('Y', strtotime($start)) : '';
          $location = $lieu ? $lieu : '';
          if ($ville) $location .= $location ? ', ' . $ville : $ville;
          $url       = get_post_meta(get_the_ID(), '_event_url', true);
          $url_label = get_post_meta(get_the_ID(), '_event_url_label', true);
        ?>
        <div class="rev-event rev-event--past">
          <div class="rev-event-date">
            <span class="rev-event-month"><?php echo esc_html(ucfirst($month)); ?></span>
            <span class="rev-event-year"><?php echo esc_html($year); ?></span>
          </div>
          <div class="rev-event-content">
            <h3 class="rev-event-nom"><?php the_title(); ?></h3>
            <?php if ($location) : ?>
            <p class="rev-event-lieu"><?php echo esc_html($location); ?></p>
            <?php endif; ?>
            <?php if ($url) : ?>
            <a href="<?php echo esc_url($url); ?>" class="rev-event-link" target="_blank" rel="noopener"><?php echo esc_html($url_label ?: 'En savoir plus'); ?> →</a>
            <?php endif; ?>
          </div>
        </div>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ======================== CTA FINAL ======================== -->
  <section class="cta-final">
    <div class="cta-final-text"><?php echo wp_kses_post($data['cta']['text']); ?></div>
    <a href="<?php echo esc_url($data['cta']['url']); ?>" class="cta-button cta-button--light"><?php echo esc_html($data['cta']['button']); ?></a>
  </section>

<?php get_footer(); ?>
