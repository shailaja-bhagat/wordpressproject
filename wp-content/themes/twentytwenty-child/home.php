<?php
	get_header();
?>

<div class = "categories">
	<ul class="cat-list">
		<a class="category-filter active" href="">
			<li class="js-filter">
				All
			</li>
		</a>
		<?php 
		$cat_args = array(
			'exclude' => array(1),
			'option_all' => 'All'
		);
		$categories = get_categories($cat_args); ?>
	
		<?php foreach($categories as $cat) : ?>
		<a class="category-filter" data-id="434" data-category = "<?= $cat->term_id; ?>" href="">
			<li class="js-filter-item">
				<?= $cat->name; ?>
			</li>
		</a>
	<?php endforeach; ?>
	</ul>
</div>
<main id="site-content" role="main">

	<div class="post-filter">

		<div class = "posts">
			<?php
			$ajaxposts = new WP_Query([
				'post_type' 	=> 'post',
				'posts_per_page'=> -1,
				'orderby' 		=> 'menu_order', 
				'order' 		=> 'Asc',
			]);
			
			$response = '';

			if($ajaxposts->have_posts()) {?>
				<div class="container">
					<div class="row post-row">
						<?php				
						while($ajaxposts->have_posts()) : $ajaxposts->the_post();
							$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
						?>
						<div class="col-sm-12 col-md-6">
							<div class="container">
								<div class="row inner-post">
									<div class="col-sm-12 col-md-5">	
										<div class="post-section">
											<div class="post-img-section"> 
												<a href="<?= the_permalink(); ?>"> 
													<img src="<?= $featured_img_url ?>"> 
												</a>
											</div>
										</div>
									</div>
									<div class="col-sm-12 col-md-7">
										<div class="post-title-section"> 
											<a href="<?= the_permalink(); ?>"> 
												<h3 class="home-post-title"><?= get_the_title(); ?></h3> 
											</a>
										</div>
										<div class="post-excerpt-section"> 
											<p class="home-post-excerpt">
												<?= the_excerpt(); ?>
											</p>
										</div>
										<div class="post-date-section"> 
											<p>
												<?= get_the_date(); ?>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<!-- $response .= get_template_part( 'template-parts/content', get_post_type() ); -->
						<?php endwhile;?>
					</div>
				</div>
			<?php } else {
				$response = 'empty';
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</main><!-- #site-content -->

<?php get_footer(); ?>