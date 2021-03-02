<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: admin-portal
*/

get_header();

$is_instructor = STM_LMS_Instructor::is_instructor();

if(!is_user_logged_in() || !$is_instructor){

    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

global $wpdb;

$sudentQ = "SELECT u.id as student_uid, u.user_login, u.user_email, u.user_status, ur.phone, ur.id, ur.year , ur.status
            FROM sb_users u 
            INNER JOIN sb_user_record ur ON u.id = ur.user_id
            ORDER BY ur.id;";

$students = $wpdb->get_results( $sudentQ, ARRAY_A);

$alphabet = range('A', 'J');

$std_status = ["New", "Registered", "Reviewed"];

?>

<?php get_template_part('partials/title_box'); ?>


<div class="container">

  <div class="post_type_exist clearfix">

    <div class="pb-10">

      <div class="container">

        <div class="row">

          <div class="col-md-12 col-sm-12">

            <?php get_template_part( 'template-parts/admin-sidebar');?>

          </div>

          <div class="col-md-12 col-sm-12">

            <div class="card">

              <h3>Students</h3>

              <hr>

              <table id="student-list" class="display">

                <thead>
                  <tr>
                    <th>Student Id</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Year</th>                    
                    <th>Status</th>
                    <th>Options</th>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($students as $student): ?>
                  <?php $array = str_split($student['year']);
                        $student_id = $alphabet[$array[3]].$student['id'];
                  ?>
                  <tr>
                    <td>
                      <a href="<?php echo ( home_url( '/sampath/edit-student/?std-id='.$student['student_uid'])); ?>"
                        target=”_blank” class="<?php echo $student['user_status']==0 ? "btn-success" : "btn-danger"; ?> btn-sm btn-edit"><?php echo $student_id; ?></a>
                    </td>
                    <td><?php echo $student['user_login']; ?></td>
                    <td><?php echo $student['user_email']; ?></td>
                    <td><?php echo $student['phone']; ?></td>
                    <td><?php echo $student['year']; ?></td>
                    <td><?php echo $std_status[$student['status']]; ?></td>
                    <td>
                      <div style="display: inline-flex !important;">
                        <a href="" id="<?php echo $student['student_uid']; ?>"
                          style="margin-right: 5px; min-width: 50px !important;"
                          data-student-id="<?php echo $student_id; ?>"
                          class="btn-danger btn-sm btn-ul-delete">Delete</a>
                        <a href="<?php echo ( home_url( '/sampath/save-payment/?std-id='.$student['student_uid'].'&pay-id=0&pay-year='.date("Y")) ); ?>"
                          target=”_blank” class="btn-warning btn-sm btn-payment"
                          style="margin-right: 5px; min-width: 50px !important;">Pay</a>
                        <a href="" id="<?php echo $student['student_uid']; ?>" data-student-status="<?php echo $student['user_status']; ?>"
                          data-student-id="<?php echo $student_id; ?>" style="min-width: 50px !important;"
                          class="btn-sm btn-ul-status <?php echo $student['user_status']==1 ? "btn-success" : "btn-danger"; ?>">
                          <?php echo $student['user_status']==1 ? "Activate" : "Deactivate"; ?>
                        </a>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>

              </table>

            </div>

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