<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: time-table
*/

get_header();

global $wpdb;

$calenderQ = "SELECT * FROM sb_student_calender c Where c.date between CURDATE() and DATE_ADD(CURDATE(), INTERVAL 6 DAY);";

$schedules = $wpdb->get_results( $calenderQ, ARRAY_A);

?>

<?php get_template_part('partials/title_box'); ?>


<div class="container">

    <div class="post_type_exist clearfix">

        <div class="pb-10">

            <div class="container">

                <div class="row">

                    <div class="col-md-12 col-sm-12">

                        <div class="card">

                            <div class="row">

                                <div class="col-md-4 col-sm-12">

                                    <h3 style="margin: 0 !important;">Time Table</h3>

                                </div>

                                <div class="col-md-8 col-sm-12 text-right">
                                
                                </div>

                            </div>

                            <hr>

                            <table id="time-table-list" class="display">

                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Year</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($schedules as $schedule): ?>
                                    <tr>
                                        <td><?php echo $schedule['title']; ?></td>
                                        <td><?php echo $schedule['year']; ?></td>
                                        <td><?php echo $schedule['date']; ?></td>
                                        <td><?php echo $schedule['time']; ?></td>
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

    <div class="clearfix" style="padding-bottom: 20px;">
    </div>

</div>

<?php
get_footer();