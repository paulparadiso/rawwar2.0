<?php get_header(); ?>
<?php get_sidebar(); ?>

	<div id="content" class="narrowcolumn">

		<?php if (have_posts()) : ?>

 	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="pagetitle">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle">Author Archive</h2>
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle">Blog Archives</h2>
 	  <?php } ?>


		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

		<?php while (have_posts()) : the_post(); ?>
			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<?php
					global $post;
					if ($post->video && $post->video->thumbnail_url) {
						//thumbnail
						?><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>" class="thumbnail"><img src="<?= $post->video->thumbnail_url ?>" class="thumbnail" alt="<?php the_title_attribute() ?>"/></a><?
					}
				?>
				<div class="entry">
					<?php the_excerpt(); ?>
					<div>Posted on <?php the_time('F jS, Y') ?> by <?php the_author_posts_link() ?> <?php
						if ($post->video
							&& function_exists('secondsToTimeString')) {
							if (isset($post->video->start_offset)
								&& $post->video->end_offset
								&& $post->video->end_offset > $post->video->start_offset
								) {
								$duration = secondsToTimeString($post->video->end_offset - $post->video->start_offset);
							} else if ($post->video->duration) {
								$duration = secondsToTimeString($post->video->duration);
							}
							echo " | Duration: $duration";
							
							if ($post->video->video_id != $post->video->post_id) {
								echo '<br/>Clip from <a href="' . get_permalink($post->video->video_id) . '">' . get_the_title($post->video->video_id) . '</a>';
							}
						}
					?></div>
				</div>

				<p class="postmetadata"><?php the_category(', ') ?> | <?php the_tags('Tags: ', ', ', '&nbsp;| '); ?> <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

	</div>


<?php get_footer(); ?>
