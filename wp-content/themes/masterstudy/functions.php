<?php
$theme_info = wp_get_theme();
define('STM_THEME_VERSION', ( WP_DEBUG ) ? time() : $theme_info->get( 'Version' ) );
define('STM_MS_SHORTCODES', '1' );

$inc_path = get_template_directory() . '/inc';

$widgets_path = get_template_directory() . '/inc/widgets';
// Theme setups

// Custom code and theme main setups
require_once($inc_path . '/setup.php');

// Enqueue scripts and styles for theme
require_once($inc_path . '/scripts_styles.php');

/*Theme configs*/
require_once($inc_path . '/theme-config.php');

// Visual composer custom modules
if (defined('WPB_VC_VERSION')) {
	require_once($inc_path . '/visual_composer.php');
}

require_once($inc_path . '/elementor.php');

// Custom code for any outputs modifying
//require_once($inc_path . '/payment.php');
require_once($inc_path . '/custom.php');

// Custom code for woocommerce modifying
if (class_exists('WooCommerce')) {
	require_once($inc_path . '/woocommerce_setups.php');
}

if(defined('STM_LMS_URL')) {
	require_once($inc_path . '/lms/main.php');
}
function stm_glob_pagenow(){
    global $pagenow;
    return $pagenow;
}
function stm_glob_wpdb(){
    global $wpdb;
    return $wpdb;
}

if(class_exists('BuddyPress')) {
    require_once($inc_path . '/buddypress.php');
}

//Announcement banner
if (is_admin()) {
	require_once($inc_path . '/admin/generate_styles.php');
	require_once($inc_path . '/admin/admin_helpers.php');
	require_once($inc_path . '/admin/review/review-notice.php');
	require_once($inc_path . '/admin/product_registration/admin.php');
	require_once($inc_path . '/tgm/tgm-plugin-registration.php');
}

define('SAM_REALSE_VERSION', '1.0.22' );

wp_enqueue_style('datatable', get_template_directory_uri() . '/assets/css/jquery.dataTables.min.css', NULL, SAM_REALSE_VERSION, 'all');
wp_enqueue_style('jqueryUicss', get_template_directory_uri() . '/assets/css/jquery-ui.css', NULL, SAM_REALSE_VERSION, 'all');
wp_enqueue_style('growl', get_template_directory_uri() . '/assets/notify/css/jquery.growl.css', NULL, SAM_REALSE_VERSION, 'all');
wp_enqueue_style('notifIt', get_template_directory_uri() . '/assets/notify/css/notifIt.css', NULL, SAM_REALSE_VERSION, 'all');

wp_enqueue_script('datatable', get_template_directory_uri() . '/assets/js/jquery.dataTables.min.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('custom_bio_js', get_template_directory_uri() . '/assets/js/sampath_bio.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('datatableBtn', get_template_directory_uri() . '/assets/js/dataTables.buttons.min.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('datatablejZ', get_template_directory_uri() . '/assets/js/jszip.min.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('datatablePDF', get_template_directory_uri() . '/assets/js/pdfmake.min.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('datatableVFS', get_template_directory_uri() . '/assets/js/vfs_fonts.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('datatableBtnHtml', get_template_directory_uri() . '/assets/js/buttons.html5.min.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('formValidation', get_template_directory_uri() . '/assets/js/jquery.validate.min.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('jqueryUijs', get_template_directory_uri() . '/assets/js/jquery-ui.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('rainbow', get_template_directory_uri() . '/assets/notify/js/rainbow.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('growljs', get_template_directory_uri() . '/assets/notify/js/jquery.growl.js', array('jquery'), SAM_REALSE_VERSION, TRUE);
wp_enqueue_script('notifItjs', get_template_directory_uri() . '/assets/notify/js/notifIt.js', array('jquery'), SAM_REALSE_VERSION, TRUE);


add_action( 'wp_ajax_student_delete_by_id', 'student_delete_by_id' );
add_action( 'wp_ajax_nopriv_student_delete_by_id','student_delete_by_id' );

function student_delete_by_id() {
	$student_id = $_POST['id'];
	
	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!is_user_logged_in() || !$is_instructor){
		wp_send_json_error("You are not authorized");
	}

	global $wpdb;

	if($student_id > 0){
		
		$wpdb->delete( sb_users, array( 'id' => $student_id ) );

		if ( $wpdb->last_error == '') {  		 		
			wp_send_json_success("Student Deleted Successfully.");
		}else{
			wp_send_json_error("Error in Deleting Student");
		}
	}else{
		wp_send_json_error("Student not in table.");
	}	

    die();
}

add_action( 'wp_ajax_payment_delete_by_id', 'payment_delete_by_id' );
add_action( 'wp_ajax_nopriv_payment_delete_by_id','payment_delete_by_id' );

function payment_delete_by_id() {
	$payment_id = $_POST['id'];
	
	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!is_user_logged_in() || !$is_instructor){
		wp_send_json_error("You are not authorized");
	}

	global $wpdb;

	$wpdb->delete( sb_user_payments, array( 'id' => $payment_id ) );

  	if ( $wpdb->last_error == '') {  		 		
  		wp_send_json_success("Payment Deleted Successfully.");
	}else{
		wp_send_json_error("Error in Deleting Payment.");
	}

    die();
}

add_action( 'wp_ajax_update_student_data', 'update_student_data' );
add_action( 'wp_ajax_nopriv_update_student_data','update_student_data' );

function update_student_data() {

	$record_id = $_POST['record-id'];
	$user_id = $_POST['user-id'];
	$user_login = $_POST['user_login'];
	$user_email = $_POST['user_email'];
	$phone = $_POST['phone'];
	$address = $_POST['address'];
	$nic = $_POST['nic'];
	$gender = $_POST['gender'];
	$course_id = $_POST['course_id'];
	$year = $_POST['year'];
	
	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!is_user_logged_in()){
		wp_send_json_error("You are not authorized");
	}

	global $wpdb;

	if($is_instructor){

		if($user_login ==null || $user_email == null ){
			wp_send_json_error("Student Name or Email can't be empty");
		}

		$userUpdateQ = "UPDATE sb_users SET user_login = '$user_login', display_name = '$user_login', user_email = '$user_email'WHERE id = $user_id;";
		$studentUpdateQ = "UPDATE sb_user_record SET phone = '$phone', address = '$address', nic = '$nic', gender = '$gender', course_id = '$course_id', year = '$year' WHERE user_id = $user_id;";

		$wpdb->query($userUpdateQ);
		$userErr = $wpdb->last_error;

		$wpdb->query($studentUpdateQ);
		$stdErr = $wpdb->last_error;

		if ( $userErr == '' && $stdErr == '' ) {  		 		
			wp_send_json_success("Student Saved Successfully.");
		}else{
			wp_send_json_error("Error in Saving Student.");
		}	

	}else{

		if($record_id == 0){
			$studentUpdateQ = "INSERT INTO sb_user_record (user_id, status, phone, address, year, gender, nic, course_id) VALUES  ('$user_id', '1', '$phone', '$address', '$year', '$gender', '$nic', '$course_id');";
		}else{
			$studentUpdateQ = "UPDATE sb_user_record SET phone = '$phone', address = '$address', nic = '$nic', gender = '$gender', course_id = '$course_id', year = '$year', status = '1' WHERE user_id = $user_id;";
		}
		
		$wpdb->query($studentUpdateQ);

		if ( $wpdb->last_error == '' ) {  		 		
			wp_send_json_success("Registered Successfully.");
		}else{
			wp_send_json_error("Error in Register.");
		}	
	}	

    die();
}

add_action( 'wp_ajax_save_payment_data', 'save_payment_data' );
add_action( 'wp_ajax_nopriv_save_payment_data','save_payment_data' );

function save_payment_data() {
	
	$id = $_POST['payment-id'];
	$user_id = $_POST['user-id'];
	$receipt_id = $_POST['receipt-id'];
	$month = $_POST['month'];
	$year = $_POST['year'];
	$status = $_POST['status'];
	
	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!is_user_logged_in()){
		wp_send_json_error("You are not authorized");
	}

	global $wpdb;
	
	if($is_instructor){
		if($id == 0){
			$savePayQ = "INSERT INTO sb_user_payments (user_id, year, month, status, receipt_id, date) VALUES  ('$user_id', '$year', '$month', '$status', '$receipt_id', now());";
		}else{
			$savePayQ = "UPDATE sb_user_payments SET status = '$status', receipt_id = '$receipt_id' WHERE id = $id;";
		}
	}else{
		$status = '3';
		if($id == 0){
			$savePayQ = "INSERT INTO sb_user_payments (user_id, year, month, receipt_id, date, status) VALUES  ('$user_id', '$year', '$month', '$receipt_id', now(), '$status');";
		}else{
			$savePayQ = "UPDATE sb_user_payments SET receipt_id = '$receipt_id', status = '$status' WHERE id = '$id';";
		}
	}
		

	$wpdb->query($savePayQ);

	if ( $wpdb->last_error == '' ) {
		wp_send_json_success("Payment Saved Successfully.");
	}else{
		wp_send_json_error("Error in Saving Payment.");
	}

    die();
}


add_action( 'wp_ajax_receipt_upload', 'receipt_upload' );
add_action( 'wp_ajax_nopriv_receipt_upload','receipt_upload' );

function receipt_upload() {

	if(!is_user_logged_in()){
		wp_send_json_error("You are not authorized");
	}

	$arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');

	if (!in_array($_FILES['receipt']['type'], $arr_img_ext)) {
		wp_send_json_error("Not Supported Format.");
	}

	
	$image = base64_encode(file_get_contents($_FILES['receipt']['tmp_name'])); 
	$user_id = $_POST['user-id'];

	global $wpdb;

	$table = 'sb_user_receipt';	
	$data = array('user_id' => $user_id, 'receipt' => $image);
	$format = array('%s');
	$wpdb->insert($table,$data,$format);

	if ( $wpdb->last_error == '' ) {
		$receipt_id = $wpdb->insert_id;
		$return = array('image' => $image, 'receipt_id' => $receipt_id);
		wp_send_json_success($return);
	}else{
		wp_send_json_error("Error in Saving Payment.");
	}
}

add_action( 'wp_ajax_remove_receipt', 'remove_receipt' );
add_action( 'wp_ajax_nopriv_remove_receipt','remove_receipt' );

function remove_receipt() {
	$id = $_POST['receipt-id'];
	
	if(!is_user_logged_in()){
		wp_send_json_error("You are not authorized");
	}
	
	global $wpdb;

	$wpdb->delete( sb_user_receipt, array( 'id' => $id ) );

  	if ( $wpdb->last_error == '' ) {  		 		
  		wp_send_json_success("Receipt Deleted Successfully.");
	}else{
		wp_send_json_error("Error in Deleting Receipt.");
	}

    die();
}

add_action( 'wp_ajax_schedule_delete_by_id', 'schedule_delete_by_id' );
add_action( 'wp_ajax_nopriv_schedule_delete_by_id','schedule_delete_by_id' );

function schedule_delete_by_id() {
	$schedule_id = $_POST['id'];
	
	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!is_user_logged_in() || !$is_instructor){
		wp_send_json_error("You are not authorized");
	}

	global $wpdb;

	if($schedule_id > 0){
		
		$wpdb->delete( sb_student_calender, array( 'id' => $schedule_id ) );

		if ( $wpdb->last_error == '') {  		 		
			wp_send_json_success("Schedule Deleted Successfully.");
		}else{
			wp_send_json_error("Error in Deleting Schedule");
		}
	}else{
		wp_send_json_error("Schedule not in table.");
	}	

    die();
}

add_action( 'wp_ajax_update_schedule_data', 'update_schedule_data' );
add_action( 'wp_ajax_nopriv_update_schedule_data','update_schedule_data' );

function update_schedule_data() {
	
	$id = $_POST['schedule-id'];
	$title = $_POST['title'];
	$year = $_POST['year'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	
	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!is_user_logged_in()){
		wp_send_json_error("You are not authorized");
	}

	global $wpdb;
	
	
	if($id == 0){
		$saveScheduleQ = "INSERT INTO sb_student_calender (title, year, date, time) VALUES  ('$title', '$year', '$date', '$time');";
	}else{
		$saveScheduleQ = "UPDATE sb_student_calender SET title = '$title', year = '$year', date = '$date', time = '$time' WHERE id = '$id';";
	}
		

	$wpdb->query($saveScheduleQ);

	if ( $wpdb->last_error == '' ) {
		wp_send_json_success("Schedule Saved Successfully.");
	}else{
		wp_send_json_error("Error in Saving Schedule.");
	}

    die();
}

add_action( 'wp_ajax_student_change_status', 'student_change_status' );
add_action( 'wp_ajax_nopriv_student_change_status','student_change_status' );

function student_change_status() {
	$student_id = $_POST['id'];
	$status = $_POST['status'] == 0 ? 1 : 0;
	
	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!is_user_logged_in() || !$is_instructor){
		wp_send_json_error("You are not authorized");
	}

	global $wpdb;

	if($student_id > 0){

		$studentUpdateQ = "UPDATE sb_users SET user_status = '$status' WHERE id = '$student_id'";

		$wpdb->query($studentUpdateQ);

		if ( $wpdb->last_error == '' ) {  		 		
			wp_send_json_success("Student Updated Successfully.");
		}else{
			wp_send_json_error("Error in Update Student");
		}

	}else{
		wp_send_json_error("Student not in table.");
	}	

    die();
}

add_action( 'wp_ajax_nic_upload', 'nic_upload' );
add_action( 'wp_ajax_nopriv_nic_upload','nic_upload' );

function nic_upload() {

	if(!is_user_logged_in()){
		wp_send_json_error("You are not authorized");
	}

	$arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif');

	if (!in_array($_FILES['nic_pic']['type'], $arr_img_ext)) {
		wp_send_json_error("Not Supported Format.");
	}

	
	$image = base64_encode(file_get_contents($_FILES['nic_pic']['tmp_name'])); 
	$user_id = $_POST['user-id'];

	global $wpdb;

	$studentUpdateQ = "UPDATE sb_user_record SET nic_pic = '$image' WHERE user_id = '$user_id'";

	$wpdb->query($studentUpdateQ);

	if ( $wpdb->last_error == '' ) {
		$return = array('image' => $image);
		wp_send_json_success($return);
	}else{
		wp_send_json_error("Error in Uploading.");
	}
}

add_action( 'wp_ajax_nic_remove', 'nic_remove' );
add_action( 'wp_ajax_nopriv_nic_remove','nic_remove' );

function nic_remove() {

	if(!is_user_logged_in()){
		wp_send_json_error("You are not authorized");
	}

	$user_id = $_POST['user-id'];

	global $wpdb;

	$studentUpdateQ = "UPDATE sb_user_record SET nic_pic = '' WHERE user_id = '$user_id'";

	$wpdb->query($studentUpdateQ);

	if ( $wpdb->last_error == '' ) {
		wp_send_json_success("Successfully removed Image.");
	}else{
		wp_send_json_error("Error in Removing Image.");
	}
}

add_action( 'wp_ajax_announcement_delete_by_id', 'announcement_delete_by_id' );
add_action( 'wp_ajax_nopriv_announcement_delete_by_id','announcement_delete_by_id' );

function announcement_delete_by_id() {
	$announcement_id = $_POST['id'];
	
	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!is_user_logged_in() || !$is_instructor){
		wp_send_json_error("You are not authorized");
	}

	global $wpdb;

	if($announcement_id > 0){
		
		$wpdb->delete( sb_announcement, array( 'id' => $announcement_id ) );

		if ( $wpdb->last_error == '') {  		 		
			wp_send_json_success("Announcement Deleted Successfully.");
		}else{
			wp_send_json_error("Error in Deleting Announcement");
		}
	}else{
		wp_send_json_error("Announcement not in table.");
	}	

    die();
}

add_action( 'wp_ajax_update_announcement_data', 'update_announcement_data' );
add_action( 'wp_ajax_nopriv_update_announcement_data','update_announcement_data' );

function update_announcement_data() {
	
	$id = $_POST['announcement-id'];
	$title = $_POST['title'];
	$year = $_POST['year'];
	$course_id = $_POST['course_id'];
	$start_date = $_POST['start-date'];
	$end_date = $_POST['end-date'];
	$announcement = $_POST['announcement'];
	
	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!is_user_logged_in() && !$is_instructor){
		wp_send_json_error("You are not authorized");
	}

	global $wpdb;
	
	
	if($id == 0){
		$announcementQ = "INSERT INTO sb_announcement (title, year, course_id, start_date, end_date, announcement) VALUES  ('$title', '$year', '$course_id', '$start_date', '$end_date', '$announcement');";
	}else{
		$announcementQ = "UPDATE sb_announcement SET title = '$title', year = '$year', course_id = '$course_id', start_date = '$start_date', end_date = '$end_date', announcement = '$announcement' WHERE id = '$id';";
	}
		

	$wpdb->query($announcementQ);

	if ( $wpdb->last_error == '' ) {
		wp_send_json_success("Announcement Saved Successfully.");
	}else{
		wp_send_json_error("Error in Saving Announcement.".$wpdb->last_error);
	}

    die();
}


function sam_log_user($user_login, WP_User $user) {

	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!$is_instructor){
		global $wpdb;

		$userId = $user->ID;

		$logQ = "CALL sp_user_log('$userId')";

		$wpdb->query($logQ);
	}	
}

add_action('wp_login','sam_log_user',10,2);


add_action( 'wp_ajax_student_review_status', 'student_review_status' );
add_action( 'wp_ajax_nopriv_student_review_status','student_review_status' );

function student_review_status() {
	$student_id = $_POST['id'];
	$status = $_POST['status'];
	
	$is_instructor = STM_LMS_Instructor::is_instructor();
	
	if(!is_user_logged_in() || !$is_instructor){
		wp_send_json_error("You are not authorized");
	}

	global $wpdb;

	if($student_id > 0){

		$studentUpdateQ = "UPDATE sb_user_record SET status = '$status' WHERE user_id = '$student_id'";

		$wpdb->query($studentUpdateQ);

		if ( $wpdb->last_error == '' ) {  	
			$std_status = $wpdb->get_results( "SELECT status FROM sb_user_record WHERE user_id = '$student_id'", ARRAY_A )[0];	 		
			wp_send_json_success($std_status);
		}else{
			wp_send_json_error("Error in Update Student");
		}

	}else{
		wp_send_json_error("Student not in table.");
	}	

    die();
}