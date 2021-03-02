<?php

$user_id = get_current_user_id();

global $wpdb;

$userRecordQ = "SELECT ur.nic_pic
                FROM sb_user_record ur
                WHERE ur.user_id = $user_id
                LIMIT 1;";

$userRecord = $wpdb->get_results( $userRecordQ, ARRAY_A )[0];

$payHisQ = "SELECT *
            FROM sb_user_payments up
            WHERE up.user_id = $user_id";

$payHistory = $wpdb->get_results( $payHisQ, ARRAY_A);

$pay_status = ["Rejected", "Paid", "Free Card", "New Payment"];

$current_year = date("Y");

?>

<div class="row">

  <div class="row">

    <div class="col-md-12">

    <input type="hidden" name="user-id" id="user-table-id" value="<?php echo $user_id;?>">

      <div class="col-md-12" style="padding-left: 0;">
        <div class="col-md-4">
            <div class="row">
              <input type="file" name="nic-pic-upload" id="nic-pic-upload" multiple="false" value=""
                accept=".png, .jpg, .jpeg," / <?php echo $userRecord['nic_pic'] ? "style='display:none'":""; ?>>
              <img src="<?php echo $userRecord['nic_pic'] ? "data:image/png;base64,".$userRecord['nic_pic']:""; ?>" alt=""
                name="nic-pic-image" id="nic-pic-image">
            </div>
            <div class="row">
              <a href="" id="btn-pic-upload" type="button" class="btn-info btn-sm"
                <?php echo $userRecord['nic_pic'] ? "style='display:none'":""; ?>>Upload</a>
            </div>
        </div>        
      </div>

      <div class="col-md-12" style="padding: 0;">

        <hr>

        <table id="my-pay-list" class="display">

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
          
              $dateObj   = DateTime::createFromFormat('!m', $history['month'] + 1);
              $monthName = $dateObj->format('F');

            ?>
            <tr>
              <td><?php echo $history['year']; ?></td>
              <td><?php echo $monthName; ?></td>
              <td><?php echo $pay_status[$history['status']]; ?></td>
              <td>
                <?php if($history['status'] == 0 || $history['status'] == 3):?>
                <a href="<?php echo ( home_url( '/sampath/my-payment/?payId='.$history['id']) ); ?>" target="_blank"
                  class="btn-info btn-sm">Edit</a>
                <?php endif;?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>

        </table>

      </div>

    </div>

  </div>

</div>