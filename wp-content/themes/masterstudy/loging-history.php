<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: loging-history
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

$hitoryQ = "SELECT ur.id as user_rid, ur.year as student_year, ur.phone, u.user_login, ur.nic, ul.date, u.id as student_uid
              FROM sb_user_log ul
              INNER JOIN sb_user_record ur ON ur.user_id = ul.user_id
              INNER JOIN sb_users u ON u.id = ur.user_id;";

$histories = $wpdb->get_results( $hitoryQ, ARRAY_A);

$alphabet = range('A', 'J');
?>

<?php get_template_part('partials/title_box'); ?>

<style type="text/css">
  table.dataTable thead th, table.dataTable thead td {
    padding: 5px 5px !important;
  }
</style>

<div class="container">

  <div class="post_type_exist clearfix">

    <div class="pb-10">

      <div class="container">

        <div class="row">

          <div class="col-md-12 col-sm-12">

            <?php get_template_part( 'template-parts/admin-sidebar');;?>

          </div>

          <div class="col-md-12 col-sm-12">

            <div class="card">

              <h3>Loging History</h3>

              <hr>

              <div class="table-responsive">

                <table id="history-list" width="100%">

                <thead>
                  <tr>
                    <th>Student Id</th>
                    <th>Student Year</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Date</th>
                    <th>NIC</th>
                  </tr>

                  <tr>
                    <th class="filterhead">Student Id</th>
                    <th class="filterhead">Student Year</th>
                    <th class="filterhead">Name</th>
                    <th class="filterhead">Phone</th>
                    <th class="filterhead">Date</th>
                    <th class="filterhead">NIC</th>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($histories as $history): ?>
                  <?php 
                        $array = str_split($history['student_year']);
                        $student_id = $alphabet[$array[3]].$history['user_rid'];

                        // $dateObj   = DateTime::createFromFormat('!m', $payment['month']+1);
                        // $monthName = $dateObj->format('F');
                  ?>
                  <tr>
                    <td>
                      <a href="<?php echo ( home_url( '/sampath/edit-student/?std-id='.$history['student_uid'])); ?>"
                        target=”_blank” class="btn-success btn-sm">
                        <?php echo $student_id; ?>                          
                      </a>
                    </td>
                    <td><?php echo $history['student_year']; ?></td>
                    <td><?php echo $history['user_login']; ?></td>
                    <td><?php echo $history['phone']; ?></td>
                    <td><?php echo $history['date']; ?></td>
                    <td><?php echo $history['nic']; ?></td>
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

  </div>

  <div class="clearfix" style="padding-bottom: 20px;">
  </div>

</div>

<?php
get_footer();