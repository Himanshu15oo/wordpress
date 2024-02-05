<?php

get_header();

while (have_posts()) {
  the_post();
  pageBanner();
?>


  <div class="container container--narrow page-section">

    <div class="metabox metabox--position-up metabox--with-home-link">
      <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('post'); ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to Blogs</a>
        <span class="metabox__main">
          Posted by <?php the_author_posts_link(); ?> on <?php the_time('j/n/y'); ?> under <?php echo get_the_category_list('|'); ?>
        </span>
      </p>
    </div>


    <?php
    $parent = wp_get_post_parent_id(get_the_ID());

    $children = get_pages(array('child_of' => get_the_ID()));

    if ($parent or $children) { ?>
      <div class="page-links">
        <h2 class="page-links__title"><a href="<?php echo get_permalink($parent) ?>"><?php echo get_the_title($parent) ?></a></h2>
        <ul class="min-list">
          <!-- <li class="current_page_item"><a href="#">Our History</a></li>
        <li><a href="#">Our Goals</a></li> -->
          <?php

          if ($parent) {
            // If we are on a child page
            // $parent will be set to non zero
            $child = $parent;
          } else {
            // Not on parent page
            $child = get_the_ID();
          }
          wp_list_pages(array(
            'title_li' => null,
            'child_of' => $child,
            'sort_column' => 'menu_order'
          ));
          ?>
        </ul>
      </div>
    <?php } ?>

    <div class="generic-content">
      <?php the_content(); ?>
    </div>

  </div>

<?php }

get_footer();

?>