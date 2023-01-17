<?php
/*
Template Name: Ventilation Orders
*/
get_header();
?>
	<div class="dashboard-page">
		<div class="container-fluid">
			<div class="row lr-12">
				<?php get_template_part( 'template-parts/vent/order', 'ventilation-list' ); ?>
			</div>
			<div class="row lr-12">
				<?php get_template_part( 'template-parts/vent/order', 'ventilation-completed' ); ?>
			</div>
		</div>
	</div>

<?php
get_footer();
