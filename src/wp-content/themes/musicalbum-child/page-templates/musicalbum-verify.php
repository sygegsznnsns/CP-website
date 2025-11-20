<?php
/*
Template Name: Musicalbum Verify
Description: 用于验证子主题与集成插件是否正常生效。
*/

defined('ABSPATH') || exit;

get_header();
?>

<main id="primary" class="site-main">
  <section class="musicalbum-verify" style="padding:2rem;">
    <h1>Musicalbum 验证页</h1>
    <p>当前模板来自子主题：<strong>musicalbum-child / page-templates/musicalbum-verify.php</strong></p>

    <hr />
    <h2>短码输出（来自插件 musicalbum-integrations）：</h2>
    <div class="musicalbum-shortcode">
      <?php echo do_shortcode('[musicalbum_hello]'); ?>
    </div>

    <hr />
    <p>如果你能看到上面的“Hello Musicalbum”，说明插件已启用并正常工作。</p>
  </section>
</main>

<?php
get_footer();
?>