<?php
if (!function_exists('add_action')) { function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {} }
if (!function_exists('add_filter')) { function add_filter($hook, $callback, $priority = 10, $accepted_args = 1) {} }
if (!function_exists('add_shortcode')) { function add_shortcode($tag, $func) {} }
if (!function_exists('do_shortcode')) { function do_shortcode($content) { return $content; } }
if (!function_exists('wp_enqueue_style')) { function wp_enqueue_style($handle, $src = false, $deps = array(), $ver = false, $media = 'all') {} }
if (!function_exists('wp_register_style')) { function wp_register_style($handle, $src, $deps = array(), $ver = false, $media = 'all') {} }
if (!function_exists('wp_register_script')) { function wp_register_script($handle, $src, $deps = array(), $ver = false, $in_footer = false) {} }
if (!function_exists('wp_enqueue_script')) { function wp_enqueue_script($handle, $src = false, $deps = array(), $ver = false, $in_footer = false) {} }
if (!function_exists('plugins_url')) { function plugins_url($path = '', $plugin = null) { return $path; } }
if (!function_exists('get_template_directory_uri')) { function get_template_directory_uri() { return ''; } }
if (!function_exists('get_stylesheet_uri')) { function get_stylesheet_uri() { return ''; } }
if (!class_exists('WP_Theme')) { class WP_Theme { public function get($field) { return ''; } } }
if (!function_exists('wp_get_theme')) { function wp_get_theme() { return new WP_Theme(); } }
if (!function_exists('get_template_part')) { function get_template_part($slug, $name = null) {} }
if (!function_exists('get_header')) { function get_header($name = null) {} }
if (!function_exists('get_footer')) { function get_footer($name = null) {} }
