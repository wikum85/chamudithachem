<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: not-paid
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

$notPaidQ = "SELECT ur.id as user_rid, ur.year as student_year, ur.phone, u.user_login, ur.nic, d.year, d.month, u.id as student_uid
				      FROM (SELECT l.user_id, l.year, l.month
						        FROM sb_user_log l
						        LEFT OUTER JOIN sb_user_payments p ON p.user_id = l.user_id AND p.month = l.month AND p.year = l.year
						        WHERE p.user_id IS NULL
			        			GROUP BY l.user_id, l.year, l.month) d
			        INNER JOIN sb_user_record ur ON ur.user_id = d.user_id
			        INNER JOIN sb_users u ON u.id = ur.user_id;";

$notPaids = $wpdb->get_results( $notPaidQ, ARRAY_A);

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

              <h3>Not Paid Students</h3>

              <hr>

              <div class="table-responsive">

                <table id="not-paid-list" width="100%">

                <thead>
                  <tr>
                    <th>Student Id</th>
                    <th>Student Year</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Year/Month</th>
                    <th>NIC</th>
                  </tr>

                  <tr>
                    <th class="filterhead">Student Id</th>
                    <th class="filterhead">Student Year</th>
                    <th class="filterhead">Name</th>
                    <th class="filterhead">Phone</th>
                    <th class="filterhead">Year/Month</th>
                    <th class="filterhead">NIC</th>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($notPaids as $notpaid): ?>
                  <?php 
                        $array = str_split($notpaid['student_year']);
                        $student_id = $alphabet[$array[3]].$notpaid['user_rid'];

                        // $dateObj   = DateTime::createFromFormat('!m', $payment['month']+1);
                        // $monthName = $dateObj->format('F');
                  ?>
                  <tr>
                    <td>
                      <a href="<?php echo ( home_url( '/sampath/edit-student/?std-id='.$notpaid['student_uid'])); ?>"
                        target=”_blank” class="btn-success btn-sm">
                        <?php echo $student_id; ?>                          
                      </a>
                    </td>
                    <td><?php echo $notpaid['student_year']; ?></td>
                    <td><?php echo $notpaid['user_login']; ?></td>
                    <td><?php echo $notpaid['phone']; ?></td>
                    <td><?php echo ($notpaid['year']).'/'.($notpaid['month']+1); ?></td>
                    <td><?php echo $notpaid['nic']; ?></td>
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