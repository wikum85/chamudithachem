<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: user-profile-url
*/

$is_instructor = STM_LMS_Instructor::is_instructor();

if(is_user_logged_in() && $is_instructor){

  wp_redirect( home_url('/admin-portal') );
  exit();
}

if(is_user_logged_in()){
  wp_redirect( home_url('/student-portal') );
  exit();
}

if(!is_user_logged_in()){
  global $wp_query;
  $wp_query->set_404();
  status_header( 404 );
  get_template_part( 404 ); exit();
}

?>