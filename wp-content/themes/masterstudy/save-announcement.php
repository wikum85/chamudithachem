<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: save-announcement
*/

get_header();

$is_instructor = STM_LMS_Instructor::is_instructor();

if(!is_user_logged_in() || !$is_instructor){

    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}

$announcement_id = $_GET['ancId'];

global $wpdb;

$announceQ = "SELECT *
            FROM sb_announcement sn
            WHERE sn.id = $announcement_id
            LIMIT 1;";

$announcement = $wpdb->get_results( $announceQ, ARRAY_A )[0];

$current_year = date("Y");

$courseQ = "SELECT p.Id, p.post_title
            FROM sb_posts p
            WHERE p.post_type = 'stm-courses' and p.post_status = 'publish';";

$courses = $wpdb->get_results( $courseQ, ARRAY_A );

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

            <h3>Save Announcement</h3>

            <hr>

            <div class="row">

              <div class="row">

                <form action="update_announcement_data" id="edit-announcement-form" method="post">

                  <input type="hidden" name="announcement-id" id="announcement-table-id"
                    value="<?php echo $announcement_id;?>">

                  <div class="col-md-12">

                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control bg-transparent" id="title" name="title"
                          placeholder="Announcement Title" value="<?php echo $announcement['title']; ?>">
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="form-label">Year</label>
                        <select name="year" id="year" class="form-control">
                          <option value="0">All Year</option>
                          <?php for ($cr_yr = 0; $cr_yr <= 3; $cr_yr++){ ?>
                          <option value="<?php echo $current_year; ?>"
                            <?php echo $announcement['year'] == $current_year ? "selected" : ""; ?>>
                            <?php echo $current_year; ?></option>';
                          <?php $current_year++;} ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="form-label">Class</label>
                        <select name="course_id" id="course_id" class="form-control">
                          <option value="0">All Class</option>
                          <?php foreach($courses as $course): ?>
                          <option value="<?php echo $course['Id']; ?>"
                            <?php echo $course['Id'] == $announcement['course_id'] ? "selected":""; ?>>
                            <?php echo $course['post_title']; ?>
                          </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="form-label">Start Date</label>
                        <input type="text" class="form-control bg-transparent" id="anc-start-date" name="start-date"
                          placeholder="Start Date" value="<?php echo $announcement['start_date']; ?>">
                      </div>
                    </div>

                    <div class="col-md-2">
                      <div class="form-group">
                        <label class="form-label">End Date</label>
                        <input type="text" class="form-control bg-transparent" id="anc-end-date" name="end-date"
                          placeholder="End Date" value="<?php echo $announcement['end_date']; ?>">
                      </div>
                    </div>

                    <div class="col-md-12">
                      <div class="form-group">
                        <label class="form-label">Announcement</label>
                        <textarea class="form-control bg-transparent" name="announcement" id="announcement" cols="30"
                          rows="10"><?php echo $announcement['announcement']; ?></textarea>
                      </div>
                    </div>

                  </div>

                  <div class="col-md-12 text-right" style="padding-right:30px;">

                    <a href="" id="" type="submit" class="btn-info btn-sm save-announcement">Save</a>

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