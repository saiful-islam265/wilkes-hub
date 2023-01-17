<?php
/*
Template Name: Damage Claim
*/
get_header();
?>
	<div class="dashboard-page">
		<div class="container-fluid">
			<div class="row lr-12">
				<?php get_template_part( 'template-parts/damage/claim', 'damage' ); ?>
				<?php //get_template_part( 'template-parts/damage/claim', 'ventilation' ); ?>
			</div>
			<div class="row lr-10">
				<?php get_template_part( 'template-parts/damage/claim', 'completed' ); ?>
				<?php //get_template_part( 'template-parts/damage/claim', 'shop-shipping' ); ?>
			</div>
		</div>
	</div>

<?php
get_footer();
