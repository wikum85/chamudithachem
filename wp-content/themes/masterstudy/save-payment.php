<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: save-payment
*/

get_header();

$is_instructor = STM_LMS_Instructor::is_instructor();

if(!is_user_logged_in() || !$is_instructor){

    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

$user_id = $_GET['std-id'];

$payment_id = $_GET['pay-id'];

if($payment_id == 0){
    $payment_year = $_GET['pay-year'];
}

global $wpdb;

$sudentQ = "SELECT ur.nic, ur.nic_pic, ur.year, ur.id
            FROM sb_users u 
            INNER JOIN sb_user_record ur ON u.id = ur.user_id
            WHERE u.id = $user_id
            LIMIT 1;";

$student = $wpdb->get_results( $sudentQ, ARRAY_A )[0];

$payHisQ = "SELECT uph.month
            FROM sb_user_payments uph
            WHERE uph.user_id = $user_id AND uph.year = $payment_year";

$payHis = $wpdb->get_results( $payHisQ, ARRAY_A);

$paidMonths = array();

foreach($payHis as $his){
    array_push($paidMonths, $his['month']);
}

if( $payment_id != 0 ){

    $paymentQ = "SELECT *
                 FROM sb_user_payments up
                 WHERE up.id = $payment_id";

    $payment = $wpdb->get_results( $paymentQ, ARRAY_A)[0];

    $recp_id = $payment['receipt_id'];
    $recieiptQ = "SELECT *
                 FROM sb_user_receipt ur
                 WHERE ur.id = $recp_id";

    $receipt = $wpdb->get_results( $recieiptQ, ARRAY_A)[0];

    $dateObj   = DateTime::createFromFormat('!m', $payment['month']+1);
    $monthName = $dateObj->format('F');
}


$months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "Noveber", "December"];

$alphabet = range('A', 'J');
$array = str_split($student['year']);
$student_id = $alphabet[$array[3]].$student['id'];

?>

<?php get_template_part('partials/title_box'); ?>

<div class="container">

    <div class="post_type_exist clearfix">

        <div class="pb-10">

            <div class="container">

                <div class="row">

                    <div class="col-md-12 col-sm-12">

                        <?php get_template_part( 'template-parts/admin-sidebar');;?>

                    </div>

                    <div class="col-md-12 col-sm-12">

                        <h3>Save Payment (<?php echo $student_id; ?>)</h3>

                        <hr>

                        <div class="row">

                            <div class="row">

                                <form action="save_payment_data" id="save-payment-form" method="post">

                                    <input type="hidden" name="user-id" id="user-id" value="<?php echo $user_id;?>">
                                    <input type="hidden" name="payment-id" value="<?php echo $payment_id;?>">

                                    <div class="col-md-12">

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Year</label>
                                                <label class="form-label">Year</label>
                                                <?php if($payment_id != 0):?>

                                                <input type="text" class="form-control bg-transparent" id="month"
                                                    name="month" value="<?php echo $payment['year']; ?>" readonly>

                                                <?php else: ?>

                                                <select name="year" id="admin-year" class="form-control">
                                                    <?php foreach (range(date('Y')-1, date('Y') + 1) as $year): ?>
                                                    <option value="<?php echo $year; ?>"
                                                        <?php echo $year == $payment_year? "selected" : "" ; ?>>
                                                        <?php echo $year; ?></option>
                                                    <?php endforeach; ?>
                                                </select>

                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="col-md-3">

                                            <div class="form-group">
                                                <label class="form-label">Month</label>

                                                <?php if($payment_id != 0):?>

                                                <input type="text" class="form-control bg-transparent" id="month"
                                                    name="month" value="<?php echo $monthName; ?>" readonly>

                                                <?php else: ?>

                                                <select name="month" id="month" class="form-control">
                                                    <?php foreach ($months as $key => $month): 
                                                        
                                                        if( in_array($key ,$paidMonths) ){
                                                            continue;
                                                        }
                                                    
                                                    ?>
                                                    <option value="<?php echo $key; ?>"><?php echo $month; ?></option>

                                                    <?php endforeach; ?>
                                                </select>

                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Status</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="0"
                                                        <?php echo $payment['status'] == 0 ? "selected" : ""; ?>>Not
                                                        Paid</option>
                                                    <option value="1"
                                                        <?php echo $payment['status'] == 1 ? "selected" : ""; ?>>Paid
                                                    </option>
                                                    <option value="2"
                                                        <?php echo $payment['status'] == 2 ? "selected" : ""; ?>>Free
                                                        Card</option>
                                                    <option value="3"
                                                        <?php echo $payment['status'] == 3 ? "selected" : ""; ?>>New Payment</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">Receipt</label>
                                                <div class="row">
                                                    <input type="file" name="receipt-upload" id="receipt-upload"
                                                        multiple="false" value="" accept=".png, .jpg, .jpeg," /
                                                        <?php echo $receipt ? "style='display:none'":""; ?>>
                                                    <img src="<?php echo $receipt ? "data:image/png;base64,".$receipt['receipt']:""; ?>"
                                                        alt="" name="receipt-image" id="receipt-image">
                                                    <input type="hidden" name="receipt-id" id="receipt-id"
                                                        value="<?php echo $payment['receipt_id']; ?>">
                                                </div>
                                                <div class="row">
                                                    <a href="" id="btn-receipt-upload" type="button"
                                                        class="btn-info btn-sm"
                                                        <?php echo $receipt ? "style='display:none'":""; ?>>Upload</a>
                                                    <a href="" id="btn-receipt-remove" type="button"
                                                        class="btn-danger btn-sm"
                                                        <?php echo $receipt ? "":"style='display:none'"; ?>>Remove</a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="form-label">NIC Number</label>
                                                <input type="text" class="form-control bg-transparent" placeholder="NIC Number"
                                                    value="<?php echo $student['nic']; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                            <label class="form-label">Profile Picture</label>
                                                <img src="<?php echo $student['nic_pic'] ? "data:image/png;base64,".$student['nic_pic']:""; ?>"
                                                    alt="" >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 text-right" style="padding-right:30px;">

                                        <a href="" type="submit" class="btn-info btn-sm save-payment">Save</a>

                                        <a href="<?php echo( home_url( '/student-payments/' ) ); ?>"
                                            class="btn-danger btn-sm">Cancel</a>

                                    </div>

                                </form>

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