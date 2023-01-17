<?php
/*
Template Name: Wood Species In Stock
*/
get_header();
?>
	<div class="dashboard-page">
		<div class="container-fluid">
			<div class="row lr-12">
				<?php get_template_part( 'template-parts/hood_species_in_stock/hood_species', 'in-stock' ); ?>
			</div>
		</div>
	</div>

<?php
get_footer();
