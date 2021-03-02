<?php

if (!defined('ABSPATH')) exit; //Exit if accessed directly

function stm_lms_str_replace_once($str_pattern, $str_replacement, $string)
{

    if (strpos($string, $str_pattern) !== false) {
        $occurrence = strpos($string, $str_pattern);
        return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
    }

    return $string;
}


function stm_lms_time_elapsed_string($datetime, $full = false)
{

    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => esc_html__('year', 'masterstudy-lms-learning-management-system'),
        'm' => esc_html__('month', 'masterstudy-lms-learning-management-system'),
        'w' => esc_html__('week', 'masterstudy-lms-learning-management-system'),
        'd' => esc_html__('day', 'masterstudy-lms-learning-management-system'),
        'h' => esc_html__('hour', 'masterstudy-lms-learning-management-system'),
        'i' => esc_html__('minute', 'masterstudy-lms-learning-management-system'),
        's' => esc_html__('second', 'masterstudy-lms-learning-management-system'),
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? esc_html__('s', 'masterstudy-lms-learning-management-system') : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? sprintf(esc_html__('%s ago', 'masterstudy-lms-learning-management-system'), implode(', ', $string)) : esc_html__('just now', 'masterstudy-lms-learning-management-system');
}

function stm_lms_register_style($style, $deps = array(), $inline_css = '')
{
    $default_path = STM_LMS_URL . 'assets/css/parts/';
    if (stm_lms_has_custom_colors()) $default_path = stm_lms_custom_styles_url();

    wp_enqueue_style('stm-lms-' . $style, $default_path . $style . '.css', $deps, stm_lms_custom_styles_v());

    if (!empty($inline_css)) wp_add_inline_style('stm-lms-' . $style, $inline_css);
}

function stm_lms_register_script($script, $deps = array(), $footer = false, $inline_scripts = '')
{
    $handle = "stm-lms-{$script}";
    wp_enqueue_script($handle, STM_LMS_URL . 'assets/js/' . $script . '.js', $deps, stm_lms_custom_styles_v(), $footer);
    if (!empty($inline_scripts)) wp_add_inline_script($handle, $inline_scripts);
}

function stm_lms_module_styles($handle, $style = 'style_1', $deps = array(), $inline_styles = '')
{
    $path = STM_LMS_URL . 'assets/css/vc_modules/' . $handle . '/' . $style . '.css';
    $handle = 'stm-' . $handle . '-' . $style;
    wp_enqueue_style($handle, $path, $deps, stm_lms_custom_styles_v(), 'all');

    if (!empty($inline_styles)) wp_add_inline_style($handle, $inline_styles);
}

function stm_lms_module_scripts($handle, $style = 'style_1', $deps = array('jquery'), $folder = 'js')
{
    $path = STM_LMS_URL . 'assets/' . $folder . '/vc_modules/' . $handle . '/' . $style . '.js';
    wp_enqueue_script('stm-' . $handle, $path, $deps, stm_lms_custom_styles_v(), 'all');
}


add_action('after_setup_theme', 'stm_lms_plugin_setups');

function stm_lms_plugin_setups()
{
    add_image_size('img-1100-450', 1100, 450, true);
    add_image_size('img-1120-800', 1120, 800, true);
    add_image_size('img-870-440', 870, 440, true);
    add_image_size('img-850-600', 850, 600, true);
    add_image_size('img-480-380', 480, 380, true);
    add_image_size('img-300-225', 300, 225, true);
    add_image_size('img-75-75', 75, 75, true);
}

add_action('admin_init', 'stm_lms_instructors');

function stm_lms_instructors()
{
    add_role(
        'stm_lms_instructor',
        __('Instructor', 'masterstudy-lms-learning-management-system'),
        array(
            'read' => true,
            'upload_files' => true,
            'publish_stm_lms_posts' => true,
            'edit_stm_lms_posts' => true,
            'delete_stm_lms_posts' => true,
            'edit_stm_lms_post' => true,
            'delete_stm_lms_post' => true,
            'read_stm_lms_posts' => true,
            'delete_others_stm_lms_posts' => false,
            'edit_others_stm_lms_posts' => false,
            'read_private_stm_lms_posts' => false
        )
    );
}

function stm_lms_get_terms_array($id, $taxonomy, $filter, $link = false, $args = '')
{
    $terms = wp_get_post_terms($id, $taxonomy);

    if (!is_wp_error($terms) and !empty($terms)) {
        if ($link) {
            $links = array();
            if (!empty($args)) $args = stm_lms_array_as_string($args);
            foreach ($terms as $term) {
                $url = get_term_link($term);
                $links[] = "<a {$args} href='{$url}' title='{$term->name}'>{$term->name}</a>";
            }
            $terms = $links;
        } else {
            $terms = wp_list_pluck($terms, $filter);
        }
    } else {
        $terms = array();
    }

    return apply_filters('pearl_get_terms_array', $terms);
}

function stm_lms_array_as_string($arr)
{
    $r = implode(' ', array_map('stm_lms_array_map', $arr, array_keys($arr)));

    return $r;
}

function stm_lms_array_map($v, $k)
{
    return $k . '="' . $v . '"';
}

function stm_lms_minimize_word($word, $length = '40', $affix = '...')
{

    if (!empty(intval($length))) {
        $w_length = mb_strlen($word);
        if ($w_length > $length) {
            $word = mb_strimwidth($word, 0, $length, $affix);
        }
    }

    return sanitize_text_field($word);
}

function stm_lms_has_custom_colors()
{
    $main_color = STM_LMS_Options::get_option('main_color', '');
    $secondary_color = STM_LMS_Options::get_option('secondary_color', '');

    return (!empty($main_color) or !empty($secondary_color));
}

function stm_lms_custom_styles_url($main = false, $get_dir = false)
{
    $upload = wp_upload_dir();
    $upload_url = $upload['baseurl'];
    if (is_ssl()) $upload_url = str_replace('http://', 'https://', $upload_url);
    if ($get_dir) $upload_url = $upload['basedir'];
    $parts = (!$main) ? 'parts/' : '';
    return $upload_url . "/stm_lms_styles/{$parts}";
}

function stm_lms_custom_styles_v()
{
    return (WP_DEBUG) ? time() : get_option('stm_lms_styles_v', 1);
}

add_filter('vc_iconpicker-type-fontawesome', 'stm_lms_add_vc_icons');

function stm_lms_add_vc_icons($fonts)
{

    if (empty($fonts)) $fonts = array();

    $icons = json_decode(file_get_contents(STM_LMS_PATH . '/assets/icons/selection.json', true), true);
    $icons = $icons['icons'];

    $fonts['STM LMS Icons'] = array();

    foreach ($icons as $icon) {
        $icon_name = $icon['properties']['name'];
        $fonts['STM LMS Icons'][] = array(
            "stmlms-{$icon_name}" => $icon_name
        );
    }

    return $fonts;
}

function enqueue_login_script()
{
    wp_enqueue_script('stm_grecaptcha');
    stm_lms_register_script('login');
    do_action('stm_lms_enqueue_login_script');
}

function enqueue_register_script()
{
    if (STM_LMS_Helpers::g_recaptcha_enabled()) wp_enqueue_script('vue-recaptcha');
    stm_lms_register_script('register');
    do_action('stm_lms_enqueue_register_script');
}

//add_action("pmpro_account_bullets_top", 'stm_lms_remove_pmpro_account_shortcode', 1000);

function stm_lms_remove_pmpro_account_shortcode()
{ ?>

    <script type="text/javascript">
        var locationUrl = "<?php echo esc_url(STM_LMS_User::user_page_url()); ?>" + window.location.hash;
        if (window.location.href !== locationUrl) window.location = locationUrl;
    </script>
<?php }

add_filter('body_class', 'stm_lms_body_class', 1, 100);

function stm_lms_body_class($classes)
{
    $classes[] = 'stm_lms_' . STM_LMS_Options::get_option('load_more_type', 'button');

    return $classes;
}

/**
 * @param $file
 * @param array $args
 * @param null $show default null
 *
 * @return string
 */
function stm_lms_render($file, $args = array(), $show = null)
{
    $file .= ".php";
    if (!file_exists($file))
        return '';
    if (is_array($args))
        extract($args);
    ob_start();
    include $file;
    if (!$show)
        return ob_get_clean();
    echo ob_get_clean();
}


/**
 * @param $content
 *
 * @return string
 */
function stm_lms_convert_content($content)
{
    return trim(preg_replace('/\s\s+/', ' ', addslashes($content)));
}

/**
 * @return bool
 */
function stm_lms_is_https()
{
    return (isset($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) == 'on') ? true : false;
}

function rand_color($opacity = 1)
{
    return "rgba(" . rand(0, 255) . ", " . rand(50, 255) . ", " . rand(10, 255) . ", " . $opacity . ")";
}

function stm_lms_lazyload_image($image)
{
    if (!function_exists('stm_conf_layload_image')) return $image;

    return stm_conf_layload_image($image);
}

function stm_lms_get_string_between($str, $startDelimiter, $endDelimiter)
{
    $contents = array();
    $startDelimiterLength = strlen($startDelimiter);
    $endDelimiterLength = strlen($endDelimiter);
    $startFrom = $contentStart = $contentEnd = 0;
    while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
        $contentStart += $startDelimiterLength;
        $contentEnd = strpos($str, $endDelimiter, $contentStart);
        if (false === $contentEnd) {
            break;
        }
        $contents[] = array(
            'start' => $contentStart,
            'end' => $contentEnd,
            'answer' => substr($str, $contentStart, $contentEnd - $contentStart)
        );
        $startFrom = $contentEnd + $endDelimiterLength;
    }

    return $contents;
}

function stm_lms_str_replace_first($from, $to, $content)
{
    $from = '/'.preg_quote($from, '/').'/';

    return preg_replace($from, $to, $content, 1);
}

function stm_lms_filtered_output($data)
{
    return apply_filters('stm_lms_filter_output', $data);
}

function stm_lms_return($params)
{
    return $params;
}

add_filter('wpml_active_languages', function ($langs) {

    if (empty(get_queried_object()) and !empty($langs)) {

        $current_url = STM_LMS_Helpers::get_current_url();
        $home_url = get_home_url();

        $sub_path = str_replace($home_url, '', $current_url);

        foreach ($langs as &$lang) {
            if ($lang['active']) continue;
            $lang['url'] = $lang['url'] . $sub_path;
        }

    };

    return $langs;

});

function stm_lms_course_files_data()
{
    return array(
        'type' => 'repeater',
        'label' => esc_html__('Course materials', 'masterstudy-lms-learning-management-system'),
        'hint' => esc_html__('Add Course materials available for all users. Lesson specified materials you can add directly to lesson.', 'masterstudy-lms-learning-management-system'),
        'fields' => array(
            'course_files_label' => array(
                'type' => 'text',
                'label' => esc_html__('Course file title', 'masterstudy-lms-learning-management-system'),
            ),
            'course_files' => array(
                'label' => esc_html__('Upload Course File', 'masterstudy-lms-learning-management-system'),
                'type' => 'file',
                'load_labels' => array(
                    'label' => esc_html__('Choose file', 'masterstudy-lms-learning-management-system'),
                    'loading' => esc_html__('Uploading file', 'masterstudy-lms-learning-management-system'),
                    'loaded' => esc_html__('View file', 'masterstudy-lms-learning-management-system'),
                    'delete' => esc_html__('Delete file', 'masterstudy-lms-learning-management-system'),
                ),
                'accept' => array(
                    '.zip',
                    '.pdf',
                    '.doc',
                    '.docx',
                    '.mp3',
                    '.mp4',
                    '.mov',
                    '.jpg',
                    '.jpeg',
                    '.png',
                    '.psd',
                    '.xls',
                    '.xlsx',
                    '.ppt',
                    '.pptx',
                ),
                'mimes' => array(
                    'zip',
                    'pdf',
                    'doc',
                    'docx',
                    'mp3',
                    'mp4',
                    'mov',
                    'jpg',
                    'jpeg',
                    'png',
                    'psd',
                    'xls',
                    'xlsx',
                    'ppt',
                    'pptx',
                ),
            ),
        )
    );
}

function stm_lms_lesson_files_data()
{
    return array(
        'type' => 'repeater',
        'label' => esc_html__('Lesson materials', 'masterstudy-lms-learning-management-system'),
        'fields' => array(
            'lesson_files' => array(
                'label' => esc_html__('Upload Lesson File', 'masterstudy-lms-learning-management-system'),
                'type' => 'file',
                'load_labels' => array(
                    'label' => esc_html__('Choose file', 'masterstudy-lms-learning-management-system'),
                    'loading' => esc_html__('Uploading file', 'masterstudy-lms-learning-management-system'),
                    'loaded' => esc_html__('View file', 'masterstudy-lms-learning-management-system'),
                    'delete' => esc_html__('Delete file', 'masterstudy-lms-learning-management-system'),
                ),
                'accept' => array(
                    '.zip',
                    '.pdf',
                    '.doc',
                    '.docx',
                    '.mp3',
                    '.mp4',
                    '.mov',
                    '.jpg',
                    '.jpeg',
                    '.png',
                    '.psd',
                    '.xls',
                    '.xlsx',
                    '.ppt',
                    '.pptx',
                ),
                'mimes' => array(
                    'zip',
                    'pdf',
                    'doc',
                    'docx',
                    'mp3',
                    'mp4',
                    'mov',
                    'jpg',
                    'jpeg',
                    'png',
                    'psd',
                    'xls',
                    'xlsx',
                    'ppt',
                    'pptx',
                ),
            ),
            'lesson_files_label' => array(
                'type' => 'text',
                'label' => esc_html__('Lesson file title', 'masterstudy-lms-learning-management-system'),
            ),
        )
    );
}

function stm_lms_get_image_url($id, $size = 'full')
{
    $url = '';
    if (!empty($id)) {
        $image = wp_get_attachment_image_src($id, $size, false);
        if (!empty($image[0])) {
            $url = $image[0];
        }
    }

    return $url;
}

function stm_lms_autocomplete_terms($taxonomy = '', $include_all = true, $extended_label = false)
{
    $r = array();
    if (is_admin()) {
        $args = array(
            'hide_empty' => false
        );
        if (!empty($taxonomy)) {
            $args['taxonomy'] = $taxonomy;
        }
        $terms = get_terms($args);

        if (!is_wp_error($terms) and !empty($terms)) {
            foreach ($terms as $term) {
                $name = !$extended_label ? $term->name . '(Taxonomy: ' . $term->taxonomy . ')' : $term->name . ' - ' . $term->slug;
                $r[] = array(
                    'label' => $name,
                    'value' => $term->term_id
                );
            }
        }
    }


    return apply_filters('stm_autocomplete_terms', $r);
}

function stm_lms_elementor_autocomplete_terms($taxonomy = '', $include_all = true, $extended_label = false)
{
    $r = array();
    if (is_admin()) {
        $args = array(
            'hide_empty' => false
        );
        if (!empty($taxonomy)) {
            $args['taxonomy'] = $taxonomy;
        }
        $terms = get_terms($args);

        if (!is_wp_error($terms) and !empty($terms)) {
            foreach ($terms as $term) {
                $name = !$extended_label ? $term->name . '(Taxonomy: ' . $term->taxonomy . ')' : $term->name . ' - ' . $term->slug;
                $r[$term->term_id] = $name;
            }
        }
    }


    return apply_filters('stm_autocomplete_terms', $r);
}

function stm_lms_create_unique_id($atts)
{
    return 'module__' . md5(serialize($atts));
}

function stm_lms_get_VC_attachment_img_safe($attachment_id, $size_1, $size_2 = 'large', $url = false, $retina = true)
{
    if (function_exists('stm_lms_get_VC_img') && function_exists('wpb_getImageBySize') && !empty($size_1)) {
        $image = stm_lms_get_VC_img($attachment_id, $size_1, $url);
    } else {
        if ($url) {
            $image = stm_lms_get_image_url($attachment_id, $size_2);
        } else {
            $image = wp_get_attachment_image($attachment_id, $size_2);
        }
    }
    if ($retina === false && strpos($image, 'srcset') !== false) {
        $image = str_replace('srcset', 'data-retina', $image);
    }


    return $image;
}

function stm_lms_get_VC_img($img_id, $img_size, $url = false)
{
    $image = '';
    if (!empty($img_id) and !empty($img_size) and function_exists('wpb_getImageBySize')) {
        $img = wpb_getImageBySize(array(
            'attach_id' => $img_id,
            'thumb_size' => $img_size,
        ));

        if (!empty($img['thumbnail'])) {
            $image = $img['thumbnail'];

            if ($url) {
                $datas = array();
                preg_match('/src="([^"]*)"/i', $image, $datas);
                if (!empty($datas[1])) {
                    $image = $datas[1];
                } else {
                    $image = '';
                }
            }
        }
    }
    return apply_filters('stm_lms_get_VC_img', $image);
}


function stm_lms_get_lms_terms_with_meta($meta_key, $taxonomy = null, $args = array())
{

    if (empty($taxonomy)) $taxonomy = 'stm_lms_course_taxonomy';

    $term_args = array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
        'fields' => 'all',
    );

    if (!empty($meta_key)) {
        $term_args['meta_key'] = $meta_key;
        $term_args['meta_value'] = '';
        $term_args['meta_compare'] = '!=';
    }

    $term_args = wp_parse_args($args, $term_args);

    $term_query = new WP_Term_Query($term_args);

    if (empty($term_query->terms)) {
        return false;
    }

    return $term_query->terms;
}

if (!function_exists('stm_option')) {
    function stm_option($id, $fallback = false, $key = false)
    {
        global $stm_option;
        if ($fallback == false) {
            $fallback = '';
        }
        $output = (isset($stm_option[$id]) && $stm_option[$id] !== '') ? $stm_option[$id] : $fallback;
        if (!empty($stm_option[$id]) && $key) {
            $output = $stm_option[$id][$key];
        }

        return $output;
    }
}

function stm_lms_is_masterstudy_theme()
{
    $theme_info = wp_get_theme();
    return ($theme_info->get('TextDomain') === 'masterstudy' || $theme_info->get('TextDomain') === 'masterstudy-child');
}

add_action('admin_init', function () {
    stm_lms_register_style('admin/admin');
});