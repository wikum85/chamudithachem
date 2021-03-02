<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: edit-student
*/

get_header();

$is_instructor = STM_LMS_Instructor::is_instructor();

if(!is_user_logged_in() || !$is_instructor){

    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

$student_id = $_GET['std-id'];

global $wpdb;

$courseQ = "SELECT p.Id, p.post_title
            FROM sb_posts p
            WHERE p.post_type = 'stm-courses' and p.post_status = 'publish';";

$courses = $wpdb->get_results( $courseQ, ARRAY_A );

$sudentQ = "SELECT u.id as student_uid, u.user_login, u.user_email, u.user_status, ur.phone, ur.id, ur.year, ur.nic, ur.gender, ur.course_id, ur.address, ur.nic_pic, ur.status
            FROM sb_users u 
            INNER JOIN sb_user_record ur ON u.id = ur.user_id
            WHERE u.id = $student_id
            LIMIT 1;";
            
$student = $wpdb->get_results( $sudentQ, ARRAY_A )[0];

$alphabet = range('A', 'J');

?>

<?php get_template_part('partials/title_box'); ?>

<div class="container pb-10">

  <div class="post_type_exist clearfix">

    <div class="pb-10">

      <div class="container">

        <div class="row">

          <div class="col-md-12 col-sm-12">

            <?php get_template_part( 'template-parts/admin-sidebar');;?>

          </div>

          <div class="col-md-12 col-sm-12">

            <?php if($student != null){

              $array = str_split($student['year']);
              $student_id = $alphabet[$array[3]].$student['id'];
              $usr_id = $student['student_uid'];

              $payHisQ = "SELECT *
                          FROM sb_user_payments up
                          WHERE up.user_id = $usr_id";

              $payHistory = $wpdb->get_results( $payHisQ, ARRAY_A);
              
              $pay_status = ["Rejected", "Paid", "Free Card", "New Payment"];

              $current_year = date("Y");

              $logHisQ = "SELECT ul.year, ul.month, ul.date
                          FROM sb_user_log ul
                          WHERE ul.user_id = $usr_id";

              $logHistory = $wpdb->get_results( $logHisQ, ARRAY_A);
              
            ?>

            <h3>Edit Student #<?php echo $student_id; ?></h3>

            <hr>

            <div class="row">

              <div class="row">

                <form action="update_student_data" id="edit-student-form" method="post">

                  <input type="hidden" name="user-id" id="user-table-id" value="<?php echo $usr_id;?>">

                  <div class="col-md-12">

                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label">Student Name</label>
                        <input type="text" class="form-control bg-transparent" id="user_login" name="user_login"
                          placeholder="Student Name" value="<?php echo $student['user_login']; ?>">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control bg-transparent" id="user_email" name="user_email"
                          placeholder="Email" value="<?php echo $student['user_email']; ?>">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control bg-transparent" id="phone" name="phone"
                          placeholder="Phone Number" value="<?php echo $student['phone']; ?>">
                      </div>
                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control bg-transparent" id="address" name="address"
                          placeholder="Address" value="<?php echo $student['address']; ?>">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label">NIC Number</label>
                        <input type="text" class="form-control bg-transparent" id="nic" name="nic"
                          placeholder="NIC Number" value="<?php echo $student['nic']; ?>">
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-control">
                          <option value="0" <?php echo $student['gender'] == 0 ? "selected" : ""; ?>>Female</option>
                          <option value="1" <?php echo $student['gender'] == 1 ? "selected" : ""; ?>>Male</option>
                        </select>
                      </div>
                    </div>

                  </div>

                  <div class="col-md-12">

                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label">Class</label>
                        <select name="course_id" id="course_id" class="form-control">
                          <option value="">Please Select Class</option>
                          <?php foreach($courses as $course): ?>
                          <option value="<?php echo $course['Id']; ?>" <?php echo $course['Id'] == $student['course_id'] ? "selected":""; ?>><?php echo $course['post_title']; ?>
                          </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label">Year</label>
                        <select name="year" id="year" class="form-control">
                          <?php for ($cr_yr = 0; $cr_yr <= 3; $cr_yr++){ ?>
                          <option value="<?php echo $current_year; ?>"
                            <?php echo $student['year'] == $current_year ? "selected" : ""; ?>>
                            <?php echo $current_year; ?></option>';
                          <?php $current_year++;} ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="form-label">Profile Picture</label>
                        <div class="row">
                          <input type="file" name="nic-pic-upload" id="nic-pic-upload" multiple="false" value=""
                            accept=".png, .jpg, .jpeg," /
                            <?php echo $student['nic_pic'] ? "style='display:none'":""; ?>>
                          <img
                            src="<?php echo $student['nic_pic'] ? "data:image/png;base64,".$student['nic_pic']:""; ?>"
                            alt="" name="nic-pic-image" id="nic-pic-image">
                        </div>
                        <div class="row">
                          <a href="" id="btn-pic-upload" type="button" class="btn-info btn-sm"
                            <?php echo $student['nic_pic'] ? "style='display:none'":""; ?>>Upload</a>
                          <a href="" id="btn-pic-remove" type="button" class="btn-danger btn-sm"
                            <?php echo $student['nic_pic'] ? "":"style='display:none'"; ?>>Remove</a>
                        </div>
                      </div>
                    </div>

                  </div>

                  <div class="col-md-12 text-right" style="padding-right:30px;">

            		<a href="" id="<?php echo $student['student_uid']; ?>" data-student-status="2"
                  		data-student-id="<?php echo $student_id; ?>"
                  		class="btn-sm btn-rw-status btn-success btn-aprove" <?php echo $student['status'] == 1 ? '' : 'style="display:none;"'; ?> >
                  		Aprove</a>
                  	<a href="" id="<?php echo $student['student_uid']; ?>" data-student-status="0"
                      	data-student-id="<?php echo $student_id; ?>"
                      	class="btn-sm btn-rw-status btn-danger btn-decline" <?php echo $student['status'] == 0 ? 'style="display:none;"':'' ; ?>>
                      	Decline</a>                         

                    <a href="" id="<?php echo $student['student_uid']; ?>" data-student-status="<?php echo $student['user_status']; ?>"
                          data-student-id="<?php echo $student_id; ?>"
                          class="btn-sm btn-ul-statuss <?php echo $student['user_status']==1 ? "btn-success" : "btn-danger"; ?>">
                          <?php echo $student['user_status']==1 ? "Activate" : "Deactivate"; ?></a>

                    <a href="<?php echo ( home_url( '/sampath/save-payment/?std-id='.$student['student_uid'].'&pay-id=0&pay-year='.date("Y")) ); ?>"
                          target=”_blank” class="btn-warning btn-sm btn-payment">Pay</a>

                    <a href="" id="btnSubmit" type="submit" class="btn-info btn-sm save-student">Save</a>

                    <a href="<?php echo( home_url( '/admin-portal/' ) ); ?>" class="btn-danger btn-sm">Cancel</a>

                  </div>

                </form>

              </div>

              <div class="row">

                <hr>

                <div class="col-md-7">                  

                  <div class="col-md-12">

                    <h4>Payment History</h4>

                    <table id="user-pay-list" class="display">

                      <thead>
                        <tr>
                          <th>Year</th>
                          <th>Month</th>
                          <th>Status</th>
                          <th>Actions</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach ($payHistory as $history): 
                          
                          $dateObj   = DateTime::createFromFormat('!m', $history['month']+1);
                          $monthName = $dateObj->format('F');

                        ?>

                        <tr>
                          <td><?php echo $history['year']; ?></td>
                          <td><?php echo $monthName; ?></td>
                          <td><?php echo $pay_status[$history['status']]; ?></td>
                          <td>
                            <a href="<?php echo ( home_url( '/sampath/save-payment/?std-id='.$usr_id.'&pay-id='.$history['id']) ); ?>"
                              target="_blank" class="btn-info btn-sm">Edit</a>
                            <a href="" id="<?php echo $history['id']; ?>"
                              data-receipt-id="<?php echo $history['receipt_id']; ?>"
                              class="btn-danger btn-sm btn-pay-delete">Delete</a>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>

                    </table>

                  </div>

                </div>

                <div class="col-md-5">                  

                  <div class="col-md-12">

                    <h4>Login History</h4>

                    <table id="user-log-list" class="display">

                      <thead>
                        <tr>
                          <th>Year</th>
                          <th>Month</th>
                          <th>Date</th>
                        </tr>
                      </thead>

                      <tbody>
                        <?php foreach ($logHistory as $history): 
                          
                          $dateObj   = DateTime::createFromFormat('!m', $history['month']+1);
                          $monthName = $dateObj->format('F');

                        ?>

                        <tr>
                          <td><?php echo $history['year']; ?></td>
                          <td><?php echo $monthName; ?></td>
                          <td><?php echo $history['date']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>

                    </table>

                  </div>

                </div>

              </div>


            </div>

            <hr>

            <?php }else{ ?>

            <h3>No Student Data</h3>

            <?php };?>

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