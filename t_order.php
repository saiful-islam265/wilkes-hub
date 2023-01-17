<?php
/*
Template Name: Orders
*/
get_header();
?>
    <div class="dashboard-page">
        <div class="container-fluid">
            <div class="row lr-10">

	            <?php get_template_part( 'template-parts/order/order', 'list' ); ?>
	            <?php get_template_part( 'template-parts/order/order', 'completed' ); ?>
                <?php get_template_part( 'template-parts/order/order', 'this-week-shipment' ); ?>
            </div>
            <div class="row lr-10">
				<?php get_template_part( 'template-parts/order/order', 'pending' ); ?>
				<?php get_template_part( 'template-parts/order/order', 'color-match' ); ?>
            </div>
        </div>
    </div>

<?php
get_footer();