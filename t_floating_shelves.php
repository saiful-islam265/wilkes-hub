<?php
/*
Template Name: Floating Shelves
*/
get_header();
?>
	<div class="dashboard-page">
		<div class="container-fluid">
			<div class="row lr-12">
				<?php get_template_part( 'template-parts/floating_shelves/floating_shelves', 'order-list' ); ?>
			</div>
			<div class="row lr-10">
				<?php get_template_part( 'template-parts/floating_shelves/floating_shelves', 'order-completed' ); ?>
			</div>
		</div>
	</div>

<?php
get_footer();
