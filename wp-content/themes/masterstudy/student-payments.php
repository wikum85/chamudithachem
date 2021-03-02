<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: student-payments
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

$paymentsQ = "SELECT up.*, ur.id as user_rid, ur.year as student_year, ur.phone, u.user_login, ur.nic, u.id as student_uid
              FROM sb_user_payments up
              INNER JOIN sb_user_record ur ON ur.user_id = up.user_id
              INNER JOIN sb_users u ON u.id = up.user_id
              ORDER BY up.date desc;";

$payments = $wpdb->get_results( $paymentsQ, ARRAY_A);

$alphabet = range('A', 'J');

$pay_status = ["Rejected", "Paid", "Free Card", "New Payment"];

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

              <h3>Payments</h3>

              <hr>

              <div class="table-responsive">

                <table id="payment-list" width="100%">

                <thead>
                  <tr>
                    <th>Student Id</th>
                    <th>Student Year</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Year/Month</th>
                    <th>NIC</th>
                    <th>Status</th>
                    <th>Acttions</th>
                  </tr>

                  <tr>
                    <th class="filterhead">Student Id</th>
                    <th class="filterhead">Student Year</th>
                    <th class="filterhead">Name</th>
                    <th class="filterhead">Phone</th>
                    <th class="filterhead">Year/Month</th>
                    <th class="filterhead">NIC</th>
                    <th class="filterhead">Status</th>
                    <th class="filterhead">Acttions</th>
                  </tr>
                </thead>

                <tbody>
                  <?php foreach ($payments as $payment): ?>
                  <?php 
                        $array = str_split($payment['student_year']);
                        $student_id = $alphabet[$array[3]].$payment['user_rid'];

                        // $dateObj   = DateTime::createFromFormat('!m', $payment['month']+1);
                        // $monthName = $dateObj->format('F');
                  ?>
                  <tr>
                    <td>
                      <a href="<?php echo ( home_url( '/sampath/edit-student/?std-id='.$payment['student_uid'])); ?>"
                        target=”_blank” class="btn-success btn-sm">
                        <?php echo $student_id; ?>                          
                      </a>
                    </td>
                    <td><?php echo $payment['student_year']; ?></td>
                    <td><?php echo $payment['user_login']; ?></td>
                    <td><?php echo $payment['phone']; ?></td>
                    <td><?php echo ($payment['year']).'/'.($payment['month']+1); ?></td>
                    <td><?php echo $payment['nic']; ?></td>
                    <td><?php echo $pay_status[$payment['status']]; ?></td>
                    <td>
                      <div style="display: inline-table;">
                        <a href="<?php echo ( home_url( '/sampath/save-payment/?std-id='.$payment['user_id'].'&pay-id='.$payment['id']) ); ?>"
                          target="_blank" class="btn-warning btn-sm onclick" style="margin-right: 5px;">Edit</a>
                        <a href="" id="<?php echo $payment['id']; ?>" data-receipt-id="<?php echo $payment['receipt_id']; ?>"
                          class="btn-danger btn-sm btn-pay-delete">Delete</a>
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

  </div>

  <div class="clearfix" style="padding-bottom: 20px;">
  </div>

</div>

<?php
get_footer();