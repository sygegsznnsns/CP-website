<?php
/*
Plugin Name: Musicalbum Integrations
Description: 自定义集成层，用于与第三方主题与插件协作。
Version: 0.1.0
Author: chen pan
*/

defined('ABSPATH') || exit;

final class Musicalbum_Integrations {
    public static function init() {
        add_action('init', [__CLASS__, 'register_shortcodes']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        // 示例：与第三方插件交互（替换为实际钩子）
        // add_filter('some_plugin_output', [__CLASS__, 'filter_some_plugin_output'], 10, 1);
    }

    public static function register_shortcodes() {
        add_shortcode('musicalbum_hello', function($atts = [], $content = '') {
            return '<div class="musicalbum-hello">Hello Musicalbum</div>';
        });
    }

    public static function enqueue_assets() {
        wp_register_style('musicalbum-integrations', plugins_url('assets/integrations.css', __FILE__), [], '0.1.0');
        wp_enqueue_style('musicalbum-integrations');
        wp_register_script('musicalbum-integrations', plugins_url('assets/integrations.js', __FILE__), ['jquery'], '0.1.0', true);
        wp_enqueue_script('musicalbum-integrations');
    }

    public static function filter_some_plugin_output($output) {
        // 在此处理第三方插件输出
        return $output;
    }
}

Musicalbum_Integrations::init();