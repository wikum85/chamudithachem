<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>
<?php
/*
  Template Name: sampath_announcement
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

$announceQ = "SELECT * FROM sb_announcement ;";

$announcements = $wpdb->get_results( $announceQ, ARRAY_A);

?>

<?php get_template_part('partials/title_box'); ?>


<div class="container">

    <div class="post_type_exist clearfix">

        <div class="pb-10">

            <div class="container">

                <div class="row">

                    <div class="col-md-12 col-sm-12">

                        <?php get_template_part( 'template-parts/admin-sidebar');?>

                    </div>

                    <div class="col-md-12 col-sm-12">

                        <div class="card">

                            <div class="row">

                                <div class="col-md-4 col-sm-12">

                                    <h3 style="margin: 0 !important;">Announcements</h3>

                                </div>

                                <div class="col-md-8 col-sm-12 text-right">

                                    <a href="<?php echo( home_url( '/save-announcement?ancId=0' ) ); ?>" target="_blank"
                                        class="btn-success btn-sm">Add New</a>

                                </div>

                            </div>

                            <hr>

                            <table id="announcement-list" class="display">

                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($announcements as $announcement): ?>
                                    <tr>
                                        <td><?php echo $announcement['title']; ?></td>
                                        <td><?php echo $announcement['start_date']; ?></td>
                                        <td><?php echo $announcement['end_date']; ?></td>
                                        <td>
                                            <div style="display: inline-table;">
                                                <a href="" id="<?php echo $announcement['id']; ?>"
                                                    style="margin-right: 5px;"
                                                    class="btn-danger btn-sm btn-anc-delete">Delete</a>
                                                <a href="<?php echo ( home_url( '/save-announcement/?ancId='.$announcement['id']) ); ?>"
                                                    target=”_blank” class="btn-warning btn-sm btn-anc-edit">Edit</a>
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

    <div class="clearfix" style="padding-bottom: 20px;">
    </div>

</div>

<?php
get_footer();