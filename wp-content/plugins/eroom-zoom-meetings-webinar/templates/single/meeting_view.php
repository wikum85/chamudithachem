<!DOCTYPE html>
<head>
    <title><?php the_title(); ?></title>
    <meta charset="utf-8"/>
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.7.7/css/bootstrap.css"/>
    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/1.7.7/css/react-select.css"/>
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
</head>

<body>
<?php
$post_id            = get_the_ID();
$post_type          = get_post_type($post_id);
$assets             = STM_ZOOM_URL . '/assets/';
$meeting_data       = get_post_meta( $post_id, 'stm_zoom_data', true );
$meeting_password   = get_post_meta( $post_id, 'stm_password', true );
$meeting_id         = '';
$settings           = get_option( 'stm_zoom_settings', array() );
$api_key            = !empty( $settings[ 'api_key' ] ) ? $settings[ 'api_key' ] : '';
$api_secret         = !empty( $settings[ 'api_secret' ] ) ? $settings[ 'api_secret' ] : '';

if ( ! empty( $meeting_data ) ) {
    $meeting_id = !empty( $meeting_data[ 'id' ] ) ? $meeting_data[ 'id' ] : '';
}

$username   = esc_attr__( 'Guest', 'eroom-zoom-meetings-webinar' );
$email      = '';

if ( is_user_logged_in() ) {
    $user       = wp_get_current_user();
    $username   = $user->user_login;
    $email      = $user->user_email;
}
?>
<style>
    body {
        padding-top: 50px;
    }
    .selectpicker {
        height: 34px;
        border-radius: 4px;
    }
</style>

<nav id="nav-tool" class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#"><?php the_title(); ?></a>
        </div>
        <div id="navbar">
            <form class="navbar-form navbar-right" id="meeting_form">
                <div class="form-group">
                    <input type="text" name="display_name" id="display_name" readonly
                           value="<?php echo esc_attr( $username ); ?>" maxLength="100"
                           placeholder="Name" class="form-control" required>
                </div>
                <?php
                if ($post_type === 'stm-zoom-webinar') {
                    ?>
                    <div class="form-group">
                        <input type="email" name="email" id="email" 
                               value="<?php echo esc_attr( $email ); ?>" maxLength="100"
                               placeholder="Email" class="form-control" required>
                    </div>
                    <?php
                }
                ?>
                <div class="form-group" style="display:none">
                    <input type="text" name="meeting_number" id="meeting_number"
                           value="<?php echo esc_attr( $meeting_id ); ?>" maxLength="11"
                           style="width:150px" placeholder="Meeting Number" class="form-control" required>
                </div>
                <div class="form-group" style="display:none">
                    <input type="text" name="meeting_pwd" id="meeting_pwd"
                           value="<?php echo esc_attr( $meeting_password ); ?>" style="width:150px"
                           maxLength="32" placeholder="Meeting Password" class="form-control">
                </div>

                <div class="form-group" style="display:none">
                    <select id="meeting_role" class="selectpicker">
                        <option value=0>Attendee</option>
                        <option value=1>Host</option>
                        <option value=5>Assistant</option>
                    </select>
                </div>
                <div class="form-group" style="display:none">
                    <select id="meeting_lang" class="selectpicker dropdown">
                        <option value="en-US">English</option>
                        <option value="de-DE">German Deutsch</option>
                        <option value="es-ES">Spanish Español</option>
                        <option value="fr-FR">French Français</option>
                        <option value="jp-JP">Japanese 日本語</option>
                        <option value="pt-PT">Portuguese Portuguese</option>
                        <option value="ru-RU">Russian Русский</option>
                        <option value="zh-CN">Chinese 简体中文</option>
                        <option value="zh-TW">Chinese 繁体中文</option>
                        <option value="ko-KO">Korean 한국어</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success" id="join_meeting" style="padding: 6px 25px; text-transform: uppercase; font-weight: 700;"><?php esc_html_e( 'Join', 'eroom-zoom-meetings-webinar' ); ?></button>
                <button type="submit" style="display:none" class="btn btn-default" id="clear_all"><?php esc_html_e( 'Clear', 'eroom-zoom-meetings-webinar' ); ?></button>
            </form>
        </div>
        <!--/.navbar-collapse -->
    </div>
</nav>

<script>
    var API_KEY = '<?php echo esc_js( $api_key ); ?>';
    var leaveUrl = '<?php echo get_home_url( '/' ); ?>';
    var endpoint = '<?php echo esc_url(get_site_url()); ?>/wp-admin/admin-ajax.php?action=stm_zoom_meeting_sign'
</script>

<script src="<?php echo esc_url( $assets ); ?>js/frontend/zoom/vendor/react.min.js"></script>
<script src="<?php echo esc_url( $assets ); ?>js/frontend/zoom/vendor/react-dom.min.js"></script>
<script src="<?php echo esc_url( $assets ); ?>js/frontend/zoom/vendor/redux.min.js"></script>
<script src="<?php echo esc_url( $assets ); ?>js/frontend/zoom/vendor/redux-thunk.min.js"></script>
<script src="<?php echo esc_url( $assets ); ?>js/frontend/zoom/vendor/jquery.min.js"></script>
<script src="<?php echo esc_url( $assets ); ?>js/frontend/zoom/vendor/lodash.min.js"></script>

<script src="<?php echo esc_url( $assets ); ?>js/frontend/zoom/vendor/zoom-meeting-1.7.7.min.js"></script>
<script src="<?php echo esc_url( $assets ); ?>js/frontend/zoom/tool.js"></script>
<script src="<?php echo esc_url( $assets ); ?>js/frontend/zoom/index.js"></script>

<script>

</script>
</body>

</html>
