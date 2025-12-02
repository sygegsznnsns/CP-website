<?php
/*
Template Name: Musicalbum Viewing Center
Description: 观演记录中心：录入表单 + 我的观演列表
*/
defined('ABSPATH') || exit;
get_header();
?>
<main id="primary" class="site-main" style="padding:2rem;">
  <section>
    <?php echo do_shortcode('[musicalbum_viewing_form]'); ?>
  </section>
  <hr />
  <section>
    <h2>我的观演记录</h2>
    <?php echo do_shortcode('[musicalbum_profile_viewings]'); ?>
  </section>
</main>
<?php get_footer(); ?>
