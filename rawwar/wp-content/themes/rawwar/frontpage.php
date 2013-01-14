<?php
/*
Template Name: Front Page
*/
get_header(); ?>
<?php //get_sidebar(); ?>
	<div id="content" class="narrowcolumn">
		<div style="height: 132px; border: #dfdfdf solid 0px">
			<img src="<?php bloginfo('template_directory'); ?>/images/logos.jpg" style="float: right" usemap="#rwlogos" border="0"/>
			<div style="font-size: 30px; font-weight: bold; padding-top: 15px">RAW/WAR</div>
			<br/>An open history of art
			<map name="rwlogos">
				<area shape="rect" coords="40,0,160,132" href="http://lib.stanford.edu/women-art-revolution" title="Stanford !WAR Collection">
				<area shape="rect" coords="160,0,293,132" href="http://womenartrevolution.com/" title="RAW WAR">
				<area shape="rect" coords="293,0,412,132" href="<?php echo get_option('home'); ?>/" title="!Women Art Revolution">
			</map>
		</div>
		<?php 
		if ($rw_featured_article = (int)get_option('rw_featured_article')):
			query_posts('p=' . $rw_featured_article);
			if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="frontpage-feature  frontpage-section">
				<div class="section">Featured Article</div>
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
					</div>
				</div>
			</div>
		<?php endwhile; endif;
		endif;
query_posts('');
		if (have_posts()) : while (have_posts()) : the_post(); ?>
			<div class="frontpage-about frontpage-section">
				<div class="section"><?php the_title(); ?></div>
				<article id="post-<?php the_ID(); ?>">
					<?php the_content(); ?>
					<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
				</article>
			</div>
		<?php endwhile; endif;
		
		?><div class="frontpage-browse frontpage-section">
			<div class="section">Browse Archive</div>
			<ul class="frontpage"><?php
			wp_list_categories('show_count=0&hide_empty=0&title_li=');
			?></ul>
			<?php //wp_tag_cloud(); ?>
		</div>
		<?php
		
		query_posts(array(
			'post_type' => array('video','post'),
			'showposts' => 3
		));

		if (have_posts()) : ?>
		<!--<hr style="clear: both; margin-top: 20px; height: 20px; border-style: none"/>-->
		<div class="frontpage-section">
		<div class="section">Latest Contributions</div>
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
	</div>
	<?php else : ?>

		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn&apos;t here.</p>
		<?php include (TEMPLATEPATH . "/searchform.php"); ?>

	<?php endif; ?>

	</div>


<?php get_footer(); ?>
