<?php
if (!defined('ABSPATH')) { exit; }

add_action('wp_enqueue_scripts', function() {
    $parent_style = 'parent-style';
    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css', [], null);
    wp_enqueue_style(

        'musicalbum-child-style',
        get_stylesheet_uri(),
        [$parent_style],
        wp_get_theme()->get('Version')
    );
});

// 在此添加你的子主题钩子、模板函数等