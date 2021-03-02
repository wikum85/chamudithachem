<?php
/**
 * Plugin Name:       Student Management
 * Plugin URI:        https://sampathbiology.com
 * Description:       Handle the students and their payments by syncing with WordPress user account use the [List Students] short code to impliment this. only admin users will be able to see the content.
 * Version:           4.9
 * Requires at least: 5.4
 * Requires PHP:      7.2
 * Author:            Jeevaka Nuwan Fernando
 * License:           Exclusive Licence for sampathbiology.com
 */
 date_default_timezone_set('IST');


//enqueue the script which will use the api
function api_callings_scripts() {
    wp_enqueue_script('studentmgt-script', plugin_dir_url( __FILE__ ) . 'studentmgt.js', ['jquery'], NULL, TRUE);
    // Pass nonce to JS.
    wp_localize_script('studentmgt-script', 'studentmgtSettings', [
      'nonce' => wp_create_nonce('wp_rest'),
    ]);
}
add_action( 'wp_enqueue_scripts', 'api_callings_scripts' ); 


 //TODO: Create a Short code and register for student management
	function student_card($student){
		$student_card = '';
		$student_card .= '<div class="card student-profile-card" id="user-'.$student->id .'" data-year="'.$student->a_level_year.'">';
		$student_card .= '<div class="card-header">Student ID '.$student->a_level_year.'-'.$student->id .'</div>';
		$student_card .= '<div class="card-body">';
        $student_card .= '<h5 class="card-title"><div class="name" contentEditable="true" data-obj="false" data-id="'.$student->id .'" data-meta="first_name" >'.$student->first_name.'</div><div class="mail" contentEditable="false" data-obj="false" data-id="'.$student->id .'" data-meta="email"> '.$student->user_email.'</div></h5>';
        $student_card .= '<h5 class="card-title"><div class="name" contentEditable="true" data-obj="true" data-id="'.$student->id .'" data-meta="mobile_number" >'.$student->mobile_number.'</div></h5>';
        $student_card .= '<p class="card-text"><b>Old System ID:<span contentEditable="true" data-obj="true" data-id="'.$student->id .'" data-meta="meta_legecy_id">'.$student->meta_legecy_id.'</span></b><br/><span contentEditable="true" data-obj="true" data-id="'.$student->id .'" data-meta="postal_address">'.$student->postal_address.'</span></p>';
        $student_card .= '<button" class="btn btn-primary" data-toggle="modal" data-target="#payment_of_'.$student->id .'">Payment History</button>';		
		$student_card .= '<div class="modal fade" id="payment_of_'.$student->id .'" tabindex="-1" role="dialog" aria-labelledby="payment_of_'.$student->id .'_by_month" aria-hidden="true">';
		$student_card .= '  <div class="modal-dialog" role="document">';
		$student_card .= '    <div class="modal-content">';
		$student_card .= '      <div class="modal-header">';
 		$student_card .= '       <h5 class="modal-title" id="payment_of_'.$student->id .'_by_month">Payment History of Student ID '.$student->a_level_year.'-'.$student->id .'</h5>';
		$student_card .= '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">';
		$student_card .= '          <span aria-hidden="true">&times;</span>';
		$student_card .= '        </button>';
 		$student_card .= '     </div>';
		$student_card .= '     <div class="modal-body"><div class="container">'.studentPaymnet($student->id);
 		$student_card .= '     </div></div>';
		$student_card .= '      <div class="modal-footer">';
		$student_card .= '        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
		$student_card .= '      </div>';
		$student_card .= '    </div>';
		$student_card .= '  </div>';
		$student_card .= '</div></div></div>';
		return $student_card;
	}

function studentPaymnet($student_id){
	$student = new WP_User($student_id);
	$begin = new DateTime($student->user_registered);
	$end = new DateTime(date('Y-m-d H:i:s'));

	$interval = DateInterval::createFromDateString('1 month');
	$period = new DatePeriod($begin, $interval, $end);
	$payment = '';
	foreach ($period as $dt) {
		$payment_key = "payment_".$dt->format("Y")."_".$dt->format("M");	
		/*if($student->has_prop($payment_key) == true){
			$payment .= '<div class="payment col-4 alert alert-success" data-year="'.$dt->format("Y").'" data-month="'.$dt->format("F").'" data-status="'.$student->get($payment_key).'"><h4>'.$dt->format("Y").'</h4><h5>'.$dt->format("F").'</h5></div>';
		}
		else{
			$payment .= '<div class="payment payment-pending col-4 alert alert-danger" data-year="'.$dt->format("Y").'" data-month="'.$dt->format("F").'" data-status="Payment Pending"><h4>'.$dt->format("Y").'</h4><h5>'.$dt->format("F").'</h5><p>Payment Pending</p></div>';
		}*/
		$payment_status = "unpaid";
		$alert_class = "alert-danger";
		if($student->has_prop($payment_key) == true){
			$payment_status = $student->get($payment_key);
			
			if($payment_status == "paid"){
				$alert_class = "alert-success";
				$payment_status = "paid";

			}
			elseif($payment_status == "free card"){
				$alert_class = "alert-success";
				$payment_status = "free card";

			}
			else{
				$alert_class = "alert-danger";
				$payment_status = "unpaid";

			}
		}
		$payment .= '<div class="payment payment-pending col-4 alert '.$alert_class.'" data-year="'.$dt->format("Y").'" data-month="'.$dt->format("F").'" data-status="Payment Pending"><h5>'.$dt->format("Y").' '.$dt->format("F").': '.$payment_status.'</h5><button data-term="'.$payment_key.'" data-stdid="'.$student_id.'" data-statuschange="paid" class="btn btn-primary btn-sm ">Paid</button>&nbsp;<button data-term="'.$payment_key.'" data-stdid="'.$student_id.'" data-statuschange="free card" class="btn btn-success  btn-sm ">Free Card</button>&nbsp;<button data-term="'.$payment_key.'" data-stdid="'.$student_id.'" data-statuschange="unpaid" class="btn btn-sm btn-danger">Un Paid</button></div>';
		
	}	
	
	return $payment;
}

 function list_students()
 {
	$studentUITable = "";
	 if(current_user_can('administrator'))
	 {
	 //TODO: Create UI for student listing 
	 
 		$studentUITable = '<div class="row">';
		 
$counter = 0; //simple counter
	 //TODO: Bind data filtering and data listing for students		
		$allStudents = get_users( [ 'role__in' => [ 'student' ] ] );
		foreach ( $allStudents as $student ) {
			
			if($counter>3){
					$studentUITable .= '</div><div class="row">';	
				$counter = 0;
			}
			$counter = $counter + 1;
					$studentUITable .= '<div class="col-md-3 my-3 mx-3">';
			$studentUITable .= student_card($student);
		$studentUITable .= "</div>";
		}
		if($counter>0){ 
		$studentUITable .= "</div>";
		}
	 }
	 else{
		wp_redirect( home_url());	 
	 }
		
		return $studentUITable;
		
	 }
	 add_shortcode( 'list_students', 'list_students' );
	 
	 
	 
	 
	 /*Grid Layout*/
 function list_students_grid()
 {	
 	if(current_user_can('administrator'))
	{

		$studentUITable = "";
		$studentUITable .= "<table id='students_grid'>";
		$studentUITable .= "	<thead>";
		$studentUITable .= "		<tr>";
		$studentUITable .= "			<th>Student ID</th>";
		$studentUITable .= "			<th>Old ID</th>";
		$studentUITable .= "			<th>Delete</th>";
		$studentUITable .= "			<th>Name</th>";
		$studentUITable .= "			<th>email</th>";
		$studentUITable .= "			<th>Phone</th>";
		$studentUITable .= "			<th>Address</th>";
		$studentUITable .= "			<th>Year</th>";
		$studentUITable .= "			<th>Fees</th>";
		$studentUITable .= "		</tr>";
		$studentUITable .= "	</thead>";
		$studentUITable .= "	<tbody>";
		/*Loop through students*/
		$allStudents = get_users( [ 'role__in' => [ 'student' ] ] );
		foreach ( $allStudents as $student ) {
		$studentUITable .= '		<tr>';
		$studentUITable .= '			<td>'.$student->a_level_year.'-'.$student->id .'</td>';
		$studentUITable .= '			<td contentEditable="true" data-obj="true" data-id="'.$student->id .'" data-meta="meta_legecy_id">'.$student->meta_legecy_id.'</td>';
		$studentUITable .= '			<td><button class="btn btn-danger"><i class="fa fa-power-off" aria-hidden="true"></i></button></td>';//TODO: Check account status and change the button and funton call accordingly
		$studentUITable .= '			<td contentEditable="true" data-obj="false" data-id="'.$student->id .'" data-meta="first_name">'.$student->first_name.'</td>';
		$studentUITable .= '			<td>'.$student->user_email.'</td>';
		$studentUITable .= '			<td contentEditable="true" data-obj="true" data-id="'.$student->id .'" data-meta="mobile_number">'.$student->mobile_number.'</td>';
		$studentUITable .= '			<td contentEditable="true" data-obj="true" data-id="'.$student->id .'" data-meta="postal_address">'.$student->postal_address.'</td>';
		$studentUITable .= '			<td contentEditable="true" data-obj="true" data-id="'.$student->id .'" data-meta="a_level_year">'.$student->a_level_year.'</td>';
		$studentUITable .= '			<td>';
		
		
//************************************************
//<a href="#" onclick="$('#myModal').modal({'backdrop': 'static'});"  class="btn">Launch demo modal</a>

        $studentUITable .= '<button class="btn btn-primary" data-toggle="modal" data-target="#payment_of_'.$student->id .'">Payment History</button>';		
		$studentUITable .= '<div class="modal hide fade" id="payment_of_'.$student->id .'" tabindex="-1" role="dialog" aria-labelledby="payment_of_'.$student->id .'_by_month" aria-hidden="true" data-backdrop="static">';
		$studentUITable .= '  <div class="modal-dialog" role="document">';
		$studentUITable .= '    <div class="modal-content">';
		$studentUITable .= '      <div class="modal-header">';
 		$studentUITable .= '       <h5 class="modal-title" id="payment_of_'.$student->id .'_by_month">Payment History of Student ID '.$student->a_level_year.'-'.$student->id .'</h5>';
		$studentUITable .= '        <button type="button" class="close" data-dismiss="modal" aria-label="Close">';
		$studentUITable .= '          <span aria-hidden="true">&times;</span>';
		$studentUITable .= '        </button>';
 		$studentUITable .= '     </div>';
		$studentUITable .= '     <div class="modal-body"><div class="container">'.studentPaymnet($student->id);
 		$studentUITable .= '     </div></div>';
		$studentUITable .= '      <div class="modal-footer">';
		$studentUITable .= '        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
		$studentUITable .= '      </div>';
		$studentUITable .= '    </div>';
		$studentUITable .= '  </div>';
//************************************************		
		
		$studentUITable .= '			</td>';
		$studentUITable .= '		</tr>';
		
		}

		/*End Loop through students*/
		$studentUITable .= "	</tbody>";
		$studentUITable .= "</table>";


		
		return $studentUITable;
	}
		
}
	 add_shortcode( 'list_students_grid', 'list_students_grid' );
	 /*End of grid*/

add_shortcode('USER_META', 'user_meta_shortcode_handler');
/**
 * User Meta Shortcode handler
 * usage: [USER_META meta="first_name"]
 * @param  string $content
 * @return stirng
 */
function user_meta_shortcode_handler(){
	//TODO: Construct string
	$outval = '<table class="table">';
	$outval .= '<tr><td><strong>Student Name</strong></td><td>'.get_user_meta(get_current_user_id() ,"nickname", true).'</td></tr>';	
	$outval .= '<tr><td><strong>Student ID</strong></td><td>'.get_user_meta(get_current_user_id() ,"a_level_year", true).'-'.get_current_user_id() .'</td></tr>';
	$outval .= '<tr><td><strong>Previous Student ID</strong></td><td>'.get_user_meta(get_current_user_id() ,"meta_legecy_id", true).'</td></tr>';	
	$outval .= '</table>';
    return $outval;
	//return get_current_user_id();

}

///Register Meta values

//a_level_year
register_meta('user', 'a_level_year', array(
    "type" => "string",
    "show_in_rest" => true,
    "single" => true,
));
//mobile_number
register_meta('user', 'mobile_number', array(
    "type" => "string",
    "show_in_rest" => true,
    "single" => true,
));

//previous_exp
register_meta('user', 'previous_exp', array(
    "type" => "string",
    "show_in_rest" => true,
    "single" => true,
));
//postal_address
register_meta('user', 'postal_address', array(
    "type" => "string",
    "show_in_rest" => true,
    "single" => true,
));

//previous_exp
register_meta('user', 'previous_exp', array(
    "type" => "string",
    "show_in_rest" => true,
    "single" => true,
));

//school
register_meta('user', 'school', array(
    "type" => "string",
    "show_in_rest" => true,
    "single" => true,
));

//meta_legecy_id
register_meta('user', 'meta_legecy_id', array(
    "type" => "string",
    "show_in_rest" => true,
    "single" => true,
));

//Payment terms
$Pay_year = date("Y")+1; 
for($yr =2020;$yr<=$Pay_year;$yr++){
	for($iM =1;$iM<=12;$iM++){
	$Pay_month = date("M", strtotime("$iM/12/10"));
		//meta_payments
		register_meta('user', 'payment_'.$yr.'_'.$Pay_month, array(
			"type" => "string",
			"show_in_rest" => true,
			"single" => true,
		));
	}
}