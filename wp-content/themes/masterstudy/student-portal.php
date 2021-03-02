<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: student-portal
*/

get_header();

if(!is_user_logged_in()){

  global $wp_query;
  $wp_query->set_404();
  status_header( 404 );
  get_template_part( 404 ); exit();
}

$user_id = get_current_user_id();

$userStatusQ = "SELECT ur.status, ur.id, ur.year, ur.course_id
                FROM sb_user_record ur
                WHERE ur.user_id = $user_id ";

$userStatus = $wpdb->get_results( $userStatusQ, ARRAY_A)[0];

$alphabet = range('A', 'J');
$array = str_split($userStatus['year']);
$student_id = $alphabet[$array[3]].$userStatus['id'];

$current_year = date("Y");
$current_month = date("m") - 1;

$currentStatusQ = "SELECT up.status
                  FROM sb_user_payments up
                  WHERE up.user_id = $user_id AND year = '$current_year' AND month = '$current_month'";

$currentStatus = $wpdb->get_results( $currentStatusQ, ARRAY_A)[0];

$status = "Not Paid";

if($currentStatus !== null && ($currentStatus['status'] == 1 || $currentStatus['status'] == 2)){
  $status = "Paid";
}

$stu_year = $userStatus['year'];
$stu_course_id = $userStatus['course_id'];
$announceQ = "SELECT title, announcement FROM sb_announcement WHERE (year = $stu_year OR course_id = $stu_course_id OR course_id = 0 OR year = 0) AND (CURDATE() between start_date AND end_date) ORDER BY id;";

$announcements = $wpdb->get_results( $announceQ, ARRAY_A);
?>
<?php get_template_part('partials/title_box'); ?>

<div class="container">

  <div class="post_type_exist clearfix">

    <div class="pb-10">

      <div class="container">

        <div class="row">

          <div class="col-md-12 col-sm-12">

            <?php if($userStatus['status'] == 2):?>

            <div class="row">

               <?php foreach($announcements as $anc):?>
                <div class="alert alert-danger alert-dismissible show" role="alert">
                  <strong><?php echo $anc['title'];?></strong> <br/> <?php echo $anc['announcement'];?>
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true" style="color: black;">&times;</span>
                  </button>
                </div>
              <?php endforeach;?>

              <div class="col-md-4 col-sm-12">

                <div class="row">
                  <h3 style="margin: 0 !important;">Student #<?php echo $student_id; ?></h3>
                </div>
                <div class="row">
                  <h3 style="margin: 0 !important;">Status : <?php echo $status; ?></h3>
                </div>

              </div>

              <div class="col-md-8 col-sm-12 text-right">
                <a href="<?php echo get_post_permalink( $userStatus['course_id'] ); ?>" class="btn btn-info"
                  style="margin-right:5px;">View
                  Course</a>
                <a href="<?php echo( home_url( '/my-payment?payId=0&pay-year='.date("Y") ) ); ?>" target="_blank"
                  class="btn-success btn">Add Payment</a>

              </div>

            </div>

            <?php get_template_part( 'template-parts/student-portal');?>

            <?php elseif($userStatus['status'] == 1):?>

            <div class="alert alert-warning text-center" role="alert">
              <h2>Your Profile Under Review</h2>
            </div>

            <?php else:?>

            <?php get_template_part( 'template-parts/register-user');?>

            <?php endif;?>

          </div>

        </div>

      </div>

    </div>

  </div>

  <div class="clearfix" style="padding-bottom: 20px;">
  </div>

</div>
<?php
get_footer();