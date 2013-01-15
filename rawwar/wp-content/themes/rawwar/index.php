<?php get_header(); ?>
<?php get_sidebar(); ?>

	<div id="content" class="narrowcolumn">

	<?php
		if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post" id="post-<?php the_ID(); ?>">
				<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				<?php
					global $post;
					if ($post->video && $post->video->thumbnail_url) {
						//thumbnail
						?><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>" class="thumbnail"><img src="<?= $post->video->thumbnail_url ?>" class="thumbnail" alt="<?php the_title_attribute() ?>"/></a>
				<?php
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
		<p class="center">Sorry, but you are looking for something that isn't here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>


<?php get_footer(); ?>