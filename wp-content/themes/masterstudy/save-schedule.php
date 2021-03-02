<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: save-schedule
*/

get_header();

$is_instructor = STM_LMS_Instructor::is_instructor();

if(!is_user_logged_in() || !$is_instructor){

    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

$schedule_id = $_GET['scheId'];

global $wpdb;

$scheduleQ = "SELECT *
            FROM sb_student_calender sc 
            WHERE sc.id = $schedule_id
            LIMIT 1;";

$schedule = $wpdb->get_results( $scheduleQ, ARRAY_A )[0];

$current_year = date("Y");

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

            <h3>Save Schedule</h3>

            <hr>

            <div class="row">

              <div class="row">

                <form action="update_schedule_data" id="edit-schedule-form" method="post">

                  <input type="hidden" name="schedule-id" id="schedule-table-id" value="<?php echo $schedule_id;?>">

                  <div class="col-md-12">

                    <div class="col-md-9">
                      <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control bg-transparent" id="title" name="title"
                          placeholder="Schedule Title" value="<?php echo $schedule['title']; ?>">
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="form-label">Year</label>
                        <select name="year" id="year" class="form-control">
                          <?php for ($cr_yr = 0; $cr_yr <= 3; $cr_yr++){ ?>
                          <option value="<?php echo $current_year; ?>"
                            <?php echo $schedule['year'] == $current_year ? "selected" : ""; ?>>
                            <?php echo $current_year; ?></option>';
                          <?php $current_year++;} ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="tel" class="form-control bg-transparent" id="schedule-date" name="date"
                          placeholder="Schedule Date" value="<?php echo $schedule['date']; ?>">
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="form-label">Time</label>
                        <input type="text" class="form-control bg-transparent" id="time" name="time"
                          placeholder="7:00 AM - 10:00 AM" value="<?php echo $schedule['time']; ?>">
                      </div>
                    </div>

                  </div>

                  <div class="col-md-12 text-right" style="padding-right:30px;">

                    <a href="" id="" type="submit" class="btn-info btn-sm save-schedule">Save</a>

                    <a href="<?php echo( home_url( '/admin-portal/' ) ); ?>" class="btn-danger btn-sm">Cancel</a>

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