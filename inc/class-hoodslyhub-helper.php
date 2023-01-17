<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * The helper functionality of the Theme.
 *
 */

/**
 * Class HoodslyHubHelper
 *
 */
class HoodslyHubHelper {

	/**
	 * Returns all Color
	 *
	 * @return array
	 */
	public static function getAllColor(): array {
		return array(
			'empty'              => esc_html__( 'Select Color', 'hoodslyhub' ),
			'american-walnut'    => esc_html__( 'American Walnut', 'hoodslyhub' ),
			'antique-white'      => esc_html__( 'Antique White', 'hoodslyhub' ),
			'black'              => esc_html__( 'Black', 'hoodslyhub' ),
			'blue'               => esc_html__( 'Blue', 'hoodslyhub' ),
			'brass-sample'       => esc_html__( 'Brass', 'hoodslyhub' ),
			'york-chocolate'     => esc_html__( 'Chocolate', 'hoodslyhub' ),
			'shaker-espresso'    => esc_html__( 'Espresso', 'hoodslyhub' ),
			'gray'               => esc_html__( 'Gray', 'hoodslyhub' ),
			'light-gray'         => esc_html__( 'Light Gray', 'hoodslyhub' ),
			'primed-paint-ready' => esc_html__( 'Primed/Paint Ready', 'hoodslyhub' ),
			'raw'                => esc_html__( 'Raw', 'hoodslyhub' ),
			'charleston-saddle'  => esc_html__( 'Saddle', 'hoodslyhub' ),
			'stained-gray'       => esc_html__( 'Stained Gray', 'hoodslyhub' ),
			'white'              => esc_html__( 'White', 'hoodslyhub' ),
			'custom-color-match' => esc_html__( 'Custom Color Match', 'hoodslyhub' ),
		);
	}//end getAllColor


	/**
	 * Returns all Sizes
	 *
	 * @return array
	 */
	public static function getAllSize(): array {
		return array(
			'empty'     => esc_html__( 'Select Size', 'hoodslyhub' ),
			'30_w_30_h' => esc_html__( '30" Width x 30" Height (SKU : 3030)', 'hoodslyhub' ),
			'30_w_36_h' => esc_html__( '30" Width x 36" Height (SKU : 3036)', 'hoodslyhub' ),
			'30_w_48_h' => esc_html__( '30" Width x 48" Height (SKU : 3048)', 'hoodslyhub' ),
			'36_w_30_h' => esc_html__( '36" Width x 30" Height (SKU : 3630)', 'hoodslyhub' ),
			'36_w_36_h' => esc_html__( '36" Width x 36" Height (SKU : 3636)', 'hoodslyhub' ),
			'36_w_48_h' => esc_html__( '36" Width x 48" Height (SKU : 3638)', 'hoodslyhub' ),
			'42_w_30_h' => esc_html__( '42" Width x 30" Height (SKU : 4230)', 'hoodslyhub' ),
			'42_w_36_h' => esc_html__( '42" Width x 36" Height (SKU : 4236)', 'hoodslyhub' ),
			'42_w_48_h' => esc_html__( '42" Width x 48" Height (SKU : 4248)', 'hoodslyhub' ),
			'48_w_30_h' => esc_html__( '48" Width x 30" Height (SKU : 4830)', 'hoodslyhub' ),
			'48_w_36_h' => esc_html__( '48" Width x 36" Height (SKU : 4836)', 'hoodslyhub' ),
			'48_w_48_h' => esc_html__( '48" Width x 48" Height (SKU : 4848)', 'hoodslyhub' ),
			'54_w_30_h' => esc_html__( '54" Width x 30" Height (SKU : 5430)', 'hoodslyhub' ),
			'54_w_36_h' => esc_html__( '54" Width x 36" Height (SKU : 5436)', 'hoodslyhub' ),
			'54_w_48_h' => esc_html__( '54" Width x 48" Height (SKU : 5448)', 'hoodslyhub' ),
			'60_w_30_h' => esc_html__( '60" Width x 30" Height (SKU : 6030)', 'hoodslyhub' ),
			'60_w_36_h' => esc_html__( '60" Width x 36" Height (SKU : 6036)', 'hoodslyhub' ),
			'60_w_48_h' => esc_html__( '60" Width x 48" Height (SKU : 6048)', 'hoodslyhub' ),
		);
	}//end getAllSize

	/**
	 * Returns all Trim Options
	 *
	 * @return array
	 */
	public static function getAllTrimOption(): array {
		return array(
			'empty'               => esc_html__( 'Select Trim Option', 'hoodslyhub' ),
			'classic_trim_option' => esc_html__( 'Classic Trim', 'hoodslyhub' ),
			'flat_trim_option'    => esc_html__( 'Flat Trim', 'hoodslyhub' ),
			'block_trim_option'   => esc_html__( 'Block Trim', 'hoodslyhub' ),
			'brass_trim_option'   => esc_html__( 'Brass Trim', 'hoodslyhub' ),
			'steel_trim_option'   => esc_html__( 'Steel Trim', 'hoodslyhub' ),
		);
	}//end getAllTrimOption

	/**
	 * Returns all Trim Options
	 *
	 * @return array
	 */
	public static function getAllRemoveTrim(): array {
		return array(
			'empty'            => esc_html__( 'Select Remove Trim Option', 'hoodslyhub' ),
			'install_trim'     => esc_html__( 'Installed', 'hoodslyhub' ),
			'remove_the_trim'  => esc_html__( 'Removed', 'hoodslyhub' ),
			'put_trim_on_side' => esc_html__( 'Remove Side Trim', 'hoodslyhub' ),
		);
	}//end getAllTrimOption

	/**
	 * Returns all Crown Molding
	 *
	 * @return array
	 */
	public static function getAllCrownMolding(): array {
		return array(
			'empty'               => esc_html__( 'Select Crown Molding', 'hoodslyhub' ),
			'no_molding2'         => esc_html__( 'No Crown Molding', 'hoodslyhub' ),
			'loose_molding2'      => esc_html__( 'Loose (Not Installed)', 'hoodslyhub' ),
			'installed_molding2'  => esc_html__( 'Installed', 'hoodslyhub' ),
			'brass_crown_molding' => esc_html__( 'Brass Crown Molding', 'hoodslyhub' ),
			'steel_crown_molding' => esc_html__( 'Steel Crown Molding', 'hoodslyhub' ),
		);
	}//end getAllCrownMolding

	/**
	 * Returns all Increase Depth
	 *
	 * @return array
	 */
	public static function getAllIncreaseDepth(): array {
		return array(
			'empty'              => esc_html__( 'Select Increase Depth', 'hoodslyhub' ),
			'standard_increase'  => esc_html__( '18" Interior Depth (Standard)', 'hoodslyhub' ),
			'increase_depth_19'  => esc_html__( 'Increase Interior Depth to 19.3125"', 'hoodslyhub' ),
			'increase_depth_205' => esc_html__( 'Increase Interior Depth to 20.5', 'hoodslyhub' ),
			'increase_depth_225' => esc_html__( 'Increase Interior Depth to 22.5"', 'hoodslyhub' ),
		);
	}//end getAllIncreaseDepth

	/**
	 * Returns all Reduce Height
	 *
	 * @return array
	 */
	public static function getAllReduceHeight(): array {
		return array(
			'empty'          => esc_html__( 'Select Reduce Height', 'hoodslyhub' ),
			'reduce_1_inch'  => esc_html__( 'Remove 1', 'hoodslyhub' ),
			'reduce_2_inch'  => esc_html__( 'Remove 2', 'hoodslyhub' ),
			'reduce_3_inch'  => esc_html__( 'Remove 3', 'hoodslyhub' ),
			'reduce_4_inch'  => esc_html__( 'Remove 4', 'hoodslyhub' ),
			'reduce_5_inch'  => esc_html__( 'Remove 5', 'hoodslyhub' ),
			'reduce_6_inch'  => esc_html__( 'Remove 6', 'hoodslyhub' ),
			'reduce_7_inch'  => esc_html__( 'Remove 7', 'hoodslyhub' ),
			'reduce_8_inch'  => esc_html__( 'Remove 8', 'hoodslyhub' ),
			'reduce_9_inch'  => esc_html__( 'Remove 9', 'hoodslyhub' ),
			'reduce_10_inch' => esc_html__( 'Remove 10', 'hoodslyhub' ),
			'reduce_11_inch' => esc_html__( 'Remove 11', 'hoodslyhub' ),
			'reduce_12_inch' => esc_html__( 'Remove 12', 'hoodslyhub' ),
			'reduce_none'    => esc_html__( 'None', 'hoodslyhub' ),
		);
	}//end getAllReduceHeight

	/**
	 * Returns all Extended Chimney
	 *
	 * @return array
	 */
	public static function getAllExtendChimney(): array {
		return array(
			'empty'            => esc_html__( 'Select Extended Chimney', 'hoodslyhub' ),
			'extend_6_inches'  => esc_html__( 'Extend 6"', 'hoodslyhub' ),
			'extend_12_inches' => esc_html__( 'Extend 12"', 'hoodslyhub' ),
			'extend_24_inches' => esc_html__( 'Extend 24"', 'hoodslyhub' ),
			'extend_48_inches' => esc_html__( 'Extend 48"', 'hoodslyhub' ),
			'extend_60_inches' => esc_html__( 'Extend 60"', 'hoodslyhub' ),
			'extend_none'      => esc_html__( 'None', 'hoodslyhub' ),
		);
	}//end getAllExtendChimney

	/**
	 * Returns all Solid Bottom
	 *
	 * @return array
	 */
	public static function getAllSolidBottom(): array {
		return array(
			'empty'                => esc_html__( 'Select Solid Bottom', 'hoodslyhub' ),
			'solid_bottom_option1' => esc_html__( 'Yes (SKU : SB) (+$200.00)', 'hoodslyhub' ),
			'solid_bottom_option2' => esc_html__( 'No', 'hoodslyhub' ),
		);
	}//end getAllSolidBottom

	/**
	 * Returns all Rush Order
	 *
	 * @return array
	 */
	public static function getAllRushOrder(): array {
		return array(
			'empty'         => esc_html__( 'Select Rush Order Option', 'hoodslyhub' ),
			'rush_my_order' => esc_html__( 'Rush My Order (+$200.00)', 'hoodslyhub' ),
			'no_rush_order' => esc_html__( 'Don\'t Rush My Order', 'hoodslyhub' ),
		);
	}//end getAllRushOrder

	/**
	 * Order History helper function
	 *
	 * @param $post_id
	 * @param $summery_message
	 *
	 * @return void
	 */
	public static function add_order_history( $post_id, $summery_message ) {
		$order_summery        = get_post_meta( $post_id, 'order_summery', true );
		$order_summery        = ( isset( $order_summery ) && is_array( $order_summery ) ) ? $order_summery : array();
		$history_created_date = current_time( 'mysql' );
		$damage_claim_summery = array(
			array(
				'summery' => $summery_message,
				'date'    => $history_created_date,
			),
		);
		$order_summery_array  = array_merge( $order_summery, $damage_claim_summery );
		update_post_meta( $post_id, 'order_summery', $order_summery_array );
	}

	/**
	 *  Email_template_customize
	 *
	 * This is Email Template customizer plugin helper function
	 */
	public static function email_template_customize( $hoodslyhub_form_data, $template_id ) {

		$data = 'Something went wrong!';

		if ( file_exists( ABSPATH . 'wp-content/plugins/hoodslyhub-email-template-customizer/includes/email-render.php' ) ) {

			require_once ABSPATH . 'wp-content/plugins/hoodslyhub-email-template-customizer/includes/email-render.php';

			$email_render = VIWEC\INC\Email_Render::init();
			$data         = get_post_meta( $template_id, 'viwec_email_structure', true );
			$data         = json_decode( html_entity_decode( $data ), true );
			$data         = $email_render->render( $data, $hoodslyhub_form_data );
		}

		return $data;
	}


}//end class HoodslyHubHelper
