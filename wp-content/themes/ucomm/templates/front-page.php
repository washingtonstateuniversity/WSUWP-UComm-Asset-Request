<?php

/* Template Name: UComm Front Page */
// Provides simply an unmodified <main> container

?>

<?php get_header(); ?>

<main class="spine-blank-template">

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<div id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
			<section class="row single">
				<div class="unbound recto verso sky">
					<div class="rebound">
						<div class="column one">
							<article>
								<header>
									<div class="article-header">
										<h1>University Communications helping you unleash the power of the <span class="addcrimson">WSU</span> brand</h1>
									</div>
								</header>	
							</article>
						</div>	
					</div>
				</div>		
			</section>
		<?php the_content(); ?>
		</div><!-- #post -->

	<?php endwhile; endif; ?>

</main>
<script>
   $(document).ready(function(){
	   $(window).bind('scroll', function() {
	   var callHeight = $( window ).height() - 70;
			 if ($(window).scrollTop() > callHeight) {
				 $('.call').addClass('fixed');
			 }
			 else {
				 $('.call').removeClass('fixed');
			 }
		});
	});
</script>
<?php get_footer(); ?>