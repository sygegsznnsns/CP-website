<?php
defined('ABSPATH') || exit;
get_header();
?>
<main id="primary" class="site-main" style="padding:2rem;">
  <h1><?php the_title(); ?></h1>
  <div class="viewing-meta">
    <?php $view_date = function_exists('get_field') ? get_field('view_date') : ''; ?>
    <?php $theater = function_exists('get_field') ? get_field('theater') : ''; ?>
    <?php $cast = function_exists('get_field') ? get_field('cast') : ''; ?>
    <?php $price = function_exists('get_field') ? get_field('price') : ''; ?>
    <p><?php echo esc_html($view_date); ?></p>
    <p><?php echo esc_html($theater); ?></p>
    <p><?php echo esc_html($cast); ?></p>
    <p><?php echo esc_html($price); ?></p>
  </div>
  <div class="viewing-ticket">
    <?php $img = function_exists('get_field') ? get_field('ticket_image') : null; if ($img && isset($img['url'])) echo '<img src="'.esc_url($img['url']).'" alt="" style="max-width:100%;height:auto;" />'; ?>
  </div>
  <div class="viewing-notes">
    <?php $notes = function_exists('get_field') ? get_field('notes') : ''; echo wpautop(esc_html($notes)); ?>
  </div>
</main>
<?php get_footer(); ?>
