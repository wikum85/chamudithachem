<?php

$user_id = get_current_user_id();

global $wpdb;

$courseQ = "SELECT p.Id, p.post_title
            FROM sb_posts p
            WHERE p.post_type = 'stm-courses' and p.post_status = 'publish';";

$courses = $wpdb->get_results( $courseQ, ARRAY_A );

$userRecordQ = "SELECT ur.*
                FROM sb_user_record ur
                WHERE ur.user_id = $user_id
                LIMIT 1;";

$userRecord = $wpdb->get_results( $userRecordQ, ARRAY_A )[0];

$current_year = date("Y");

?>

<div class="row">

    <div class="row">

        <form action="update_student_data" id="register-student-form" method="post">

            <input type="hidden" name="user-id" id="user-table-id" value="<?php echo $user_id;?>">

            <input type="hidden" name="record-id" id="user-record-id"
                value="<?php echo $userRecord['phone'] ? $userRecord['phone'] : 0;?>">

            <div class="col-md-12">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control bg-transparent" id="phone" name="phone"
                            placeholder="Phone Number" value="<?php echo $userRecord['phone']; ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control bg-transparent" id="address" name="address"
                            placeholder="Address" value="<?php echo $userRecord['address']; ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">NIC Number</label>
                        <input type="text" class="form-control bg-transparent" id="nic" name="nic"
                            placeholder="NIC Number" value="<?php echo $userRecord['nic']; ?>">
                    </div>
                </div>

            </div>

            <div class="col-md-12">

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="0" <?php echo $userRecord['gender'] == 0 ? "selected" : ""; ?>>Female
                            </option>
                            <option value="1" <?php echo $userRecord['gender'] == 1 ? "selected" : ""; ?>>Male</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Year</label>
                        <select name="year" id="year" class="form-control">
                            <option value="">Please Select Academic Year</option>
                            <?php for ($cr_yr = 0; $cr_yr <= 3; $cr_yr++){ ?>
                            <option value="<?php echo $current_year; ?>"
                                <?php echo $userRecord['year'] == $current_year ? "selected" : ""; ?>>
                                <?php echo $current_year; ?></option>';
                            <?php $current_year++;} ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Class</label>
                        <select name="course_id" id="course_id" class="form-control">
                            <option value="">Please Select Your Class</option>
                          <?php foreach($courses as $course): ?>
                          <option value="<?php echo $course['Id']; ?>" <?php echo $course['Id'] == $userRecord['course_id'] ? "selected":""; ?>><?php echo $course['post_title']; ?>
                          </option>
                          <?php endforeach; ?>
                        </select>
                    </div>
                </div>                

            </div>
            
            <div class="col-md-12 text-right" style="padding-right:30px;">

                <a href="" id="btnSubmit" type="submit" class="btn-info btn-sm register-student">Save</a>

            </div>

        </form>

    </div>

</div>