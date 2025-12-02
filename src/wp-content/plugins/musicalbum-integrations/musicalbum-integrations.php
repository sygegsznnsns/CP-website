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
        add_action('init', array(__CLASS__, 'register_shortcodes'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
        add_action('init', array(__CLASS__, 'register_viewing_post_type'));
        add_action('rest_api_init', array(__CLASS__, 'register_rest_routes'));
        add_action('acf/init', array(__CLASS__, 'register_acf_fields'));
        // 示例：与第三方插件交互（替换为实际钩子）
        // add_filter('some_plugin_output', [__CLASS__, 'filter_some_plugin_output'], 10, 1);
    }

    public static function register_shortcodes() {
        add_shortcode('musicalbum_hello', array(__CLASS__, 'shortcode_musicalbum_hello'));
        add_shortcode('musicalbum_viewing_form', array(__CLASS__, 'shortcode_viewing_form'));
        add_shortcode('musicalbum_profile_viewings', array(__CLASS__, 'shortcode_profile_viewings'));
    }

    public static function shortcode_musicalbum_hello($atts = array(), $content = '') {
        return '<div class="musicalbum-hello">Hello Musicalbum</div>';
    }

    public static function enqueue_assets() {
        wp_register_style('musicalbum-integrations', plugins_url('assets/integrations.css', __FILE__), array(), '0.1.0');
        wp_enqueue_style('musicalbum-integrations');
        wp_register_script('musicalbum-integrations', plugins_url('assets/integrations.js', __FILE__), array('jquery'), '0.1.1', true);
        wp_localize_script('musicalbum-integrations', 'MusicalbumIntegrations', array(
            'rest' => array(
                'ocr' => esc_url_raw(rest_url('musicalbum/v1/ocr')),
                'nonce' => wp_create_nonce('wp_rest')
            )
        ));
        wp_enqueue_script('musicalbum-integrations');
    }

    public static function filter_some_plugin_output($output) {
        // 在此处理第三方插件输出
        return $output;
    }

    public static function register_viewing_post_type() {
        register_post_type('musicalbum_viewing', array(
            'labels' => array(
                'name' => '观演记录',
                'singular_name' => '观演记录'
            ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array('title'),
            'menu_position' => 20
        ));
    }

    public static function register_acf_fields() {
        if (!function_exists('acf_add_local_field_group')) { return; }
        acf_add_local_field_group(array(
            'key' => 'group_malbum_viewing',
            'title' => '观演字段',
            'fields' => array(
                array(
                    'key' => 'field_malbum_theater',
                    'label' => '剧院',
                    'name' => 'theater',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_malbum_cast',
                    'label' => '卡司',
                    'name' => 'cast',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_malbum_price',
                    'label' => '票价',
                    'name' => 'price',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_malbum_date',
                    'label' => '观演日期',
                    'name' => 'view_date',
                    'type' => 'date_picker',
                    'display_format' => 'Y-m-d',
                    'return_format' => 'Y-m-d'
                ),
                array(
                    'key' => 'field_malbum_ticket',
                    'label' => '票面图片',
                    'name' => 'ticket_image',
                    'type' => 'image',
                    'return_format' => 'array'
                ),
                array(
                    'key' => 'field_malbum_notes',
                    'label' => '备注',
                    'name' => 'notes',
                    'type' => 'textarea'
                )
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'musicalbum_viewing'
                    )
                )
            ),
        ));
    }

    public static function shortcode_viewing_form($atts = array(), $content = '') {
        if (!function_exists('acf_form')) { return ''; }
        ob_start();
        echo '<div class="musicalbum-viewing-form">';
        echo '<div class="musicalbum-ocr"><input type="file" id="musicalbum-ocr-file" accept="image/*" /><button type="button" id="musicalbum-ocr-button">识别票面</button></div>';
        acf_form(array(
            'post_id' => 'new_post',
            'new_post' => array(
                'post_type' => 'musicalbum_viewing',
                'post_status' => 'publish'
            ),
            'post_title' => true,
            'submit_value' => '保存观演记录'
        ));
        echo '</div>';
        return ob_get_clean();
    }

    public static function shortcode_profile_viewings($atts = array(), $content = '') {
        if (!is_user_logged_in()) { return ''; }
        $q = new WP_Query(array(
            'post_type' => 'musicalbum_viewing',
            'posts_per_page' => 20,
            'author' => get_current_user_id(),
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        ob_start();
        echo '<div class="musicalbum-viewings-list">';
        while ($q->have_posts()) { $q->the_post();
            $date = get_field('view_date', get_the_ID());
            echo '<div class="item"><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a><span class="date">' . esc_html($date) . '</span></div>';
        }
        wp_reset_postdata();
        echo '</div>';
        return ob_get_clean();
    }

    public static function register_rest_routes() {
        register_rest_route('musicalbum/v1', '/ocr', array(
            'methods' => 'POST',
            'permission_callback' => function($req){ return is_user_logged_in(); },
            'callback' => array(__CLASS__, 'rest_ocr')
        ));
        register_rest_route('musicalbum/v1', '/viewings.ics', array(
            'methods' => 'GET',
            'permission_callback' => '__return_true',
            'callback' => array(__CLASS__, 'rest_ics')
        ));
    }

    public static function rest_ocr($request) {
        $files = $request->get_file_params();
        if (empty($files['image'])) { return new WP_Error('no_image', '缺少图片', array('status' => 400)); }
        $path = $files['image']['tmp_name'];
        $data = file_get_contents($path);
        if (!$data) { return new WP_Error('bad_image', '读取图片失败', array('status' => 400)); }
        $result = apply_filters('musicalbum_ocr_process', null, $data);
        if (!is_array($result)) { $result = self::default_baidu_ocr($data); }
        return rest_ensure_response($result);
    }

    private static function default_baidu_ocr($bytes) {
        $api_key = get_option('musicalbum_baidu_api_key');
        $secret_key = get_option('musicalbum_baidu_secret_key');
        if (!$api_key || !$secret_key) { return array(); }
        $token = self::baidu_token($api_key, $secret_key);
        if (!$token) { return array(); }
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic?access_token=' . urlencode($token);
        $body = http_build_query(array('image' => base64_encode($bytes)));
        $resp = wp_remote_post($url, array('headers' => array('Content-Type' => 'application/x-www-form-urlencoded'), 'body' => $body, 'timeout' => 20));
        if (is_wp_error($resp)) { return array(); }
        $json = json_decode(wp_remote_retrieve_body($resp), true);
        $lines = array();
        if (isset($json['words_result'])) {
            foreach($json['words_result'] as $w){ $lines[] = $w['words']; }
        }
        $text = implode("\n", $lines);
        $title = self::extract_title($text);
        $theater = self::extract_theater($text);
        $cast = self::extract_cast($text);
        $price = self::extract_price($text);
        $date = self::extract_date($text);
        return array('title' => $title, 'theater' => $theater, 'cast' => $cast, 'price' => $price, 'view_date' => $date);
    }

    private static function baidu_token($api_key, $secret_key) {
        $resp = wp_remote_get('https://aip.baidubce.com/oauth/2.0/token?grant_type=client_credentials&client_id='.urlencode($api_key).'&client_secret='.urlencode($secret_key));
        if (is_wp_error($resp)) { return null; }
        $json = json_decode(wp_remote_retrieve_body($resp), true);
        return isset($json['access_token']) ? $json['access_token'] : null;
    }

    private static function extract_title($text) {
        $lines = preg_split('/\r?\n/', $text);
        return isset($lines[0]) ? $lines[0] : '';
    }
    private static function extract_theater($text) {
        if (preg_match('/(剧院|剧场|大剧院)[^\n]*/u', $text, $m)) return $m[0];
        return '';
    }
    private static function extract_cast($text) {
        if (preg_match('/(主演|卡司|演出人员)[^\n]*/u', $text, $m)) return $m[0];
        return '';
    }
    private static function extract_price($text) {
        if (preg_match('/(票价|Price)[:：]?\s*([0-9]+(\.[0-9]+)?)/u', $text, $m)) return $m[2];
        if (preg_match('/([0-9]+)[元¥]/u', $text, $m)) return $m[1];
        return '';
    }
    private static function extract_date($text) {
        if (preg_match('/(20[0-9]{2})[-年\.\/](0?[1-9]|1[0-2])[-月\.\/](0?[1-9]|[12][0-9]|3[01])/u', $text, $m)) {
            $y = $m[1]; $mth = str_pad($m[2], 2, '0', STR_PAD_LEFT); $d = str_pad($m[3], 2, '0', STR_PAD_LEFT);
            return $y.'-'.$mth.'-'.$d;
        }
        return '';
    }

    public static function rest_ics($request) {
        $args = array('post_type' => 'musicalbum_viewing', 'posts_per_page' => -1, 'post_status' => 'publish');
        $q = new WP_Query($args);
        $lines = array(
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Musicalbum//Viewing//CN'
        );
        while($q->have_posts()){ $q->the_post();
            $date = get_field('view_date', get_the_ID());
            if (!$date) { continue; }
            $dt = preg_replace('/-/', '', $date);
            $summary = get_the_title();
            $desc = trim('剧院: '.(get_field('theater', get_the_ID()) ?: '')."\n".'卡司: '.(get_field('cast', get_the_ID()) ?: '')."\n".'票价: '.(get_field('price', get_the_ID()) ?: ''));
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:' . get_the_ID() . '@musicalbum';
            $lines[] = 'DTSTART;VALUE=DATE:' . $dt;
            $lines[] = 'SUMMARY:' . self::escape_ics($summary);
            $lines[] = 'DESCRIPTION:' . self::escape_ics($desc);
            $lines[] = 'END:VEVENT';
        }
        wp_reset_postdata();
        $lines[] = 'END:VCALENDAR';
        $out = implode("\r\n", $lines);
        return new WP_REST_Response($out, 200, array('Content-Type' => 'text/calendar; charset=utf-8'));
    }

    private static function escape_ics($s){
        $s = preg_replace('/([,;])/', '\\$1', $s);
        $s = preg_replace('/\r?\n/', '\\n', $s);
        return $s;
    }
}

Musicalbum_Integrations::init();
