<?php

if (!is_user_logged_in()) {
    wp_redirect(site_url('/'));
    exit;
}

get_header();

while (have_posts()) {
    the_post();
    pageBanner();
?>


    <div class="container container--narrow page-section">
        <ul class="min-list link-list" id="my-notes">
            <?php
            $userNotes = new WP_Query(array(
                'post_type' => 'note',
                'post_per_page' => -1,
                'author' => get_current_user_id()
            ));

            while ($userNotes->have_posts()) {
                $userNotes->the_post();
            ?>
                <input class="note-title-field" type="text" value="<?php echo esc_attr(get_the_title()); ?>">
                <textarea class="note-body-field" type="text" value="<?php echo esc_attr(get_the_title()); ?>"></textarea>
            <?php
            }
            ?>
        </ul>
    </div>

<?php }

get_footer();

?>