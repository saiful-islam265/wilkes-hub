<?php

require_once __DIR__ . '/vendor/autoload.php';

class UPSShippingLabel {

	const VERSION               = '1.0.0';
	const ACCESSKEY             = '9DA11D2B5E981955';
	const USERID                = 'Holliejfox';
	const PASSWORD              = 'Hoodsly2020!';
	public $shipment            = '';
	public $package             = '';
	public $shipper             = '';
	public $shipperAddress      = '';
	public $address             = '';
	public $shipTo              = '';
	public $shipFrom            = '';
	public $soldTo              = '';
	public $service             = '';
	public $unit                = '';
	public $rateInformation     = '';
	public $dimensions          = '';
	public $api                 = '';
	private $shipping_label_dir = '';
	/**
	 * init function for single tone approach
	 *
	 * @return void
	 */
	public static function init() {
		static $instance = false;
		if ( ! $instance ) {
			$instance = new self();
		}
		return $instance;
	}

	public function create_shipping_label_dir() {
		$upload_dir   = wp_upload_dir();
		$shipping_dir = $upload_dir['basedir'] . '/shipping_label';
		if ( ! file_exists( $shipping_dir ) ) {
			wp_mkdir_p( $shipping_dir );
		}
		$this->shipping_label_dir = $shipping_dir;
	}

	public function create_shipping_label( $order_id ) {
		$ups_req_data = get_post_meta( $order_id, 'ups_req_data', true );
		$shipping     = get_post_meta( $order_id, 'shipping', true );
		$billing      = get_post_meta( $order_id, 'billing', true );

		// $woocommerce_dimension_unit = $ups_req_data['woocommerce_dimension_unit'];
		$woocommerce_dimension_unit = 'in';
		// $woocommerce_weight_unit    = $ups_req_data['woocommerce_weight_unit'];
		$woocommerce_weight_unit    = 'lbs';
		$weight                     = $ups_req_data['weight'];
		$length                     = $ups_req_data['length'];
		$width                      = $ups_req_data['width'];
		$height                     = $ups_req_data['height'];

		// 4. Echo image only if $cat_in_order == true
			$this->shipment = new Ups\Entity\Shipment();
			$this->shipper  = $this->shipment->getShipper();
			$this->shipper->setShipperNumber( '0A160X' );
			$this->shipper->setName( 'HypeMill/Red Industries' );
			$this->shipper->setAttentionName( 'HypeMill' );
			$this->shipperAddress = $this->shipper->getAddress();
			$this->shipperAddress->setAddressLine1( '1236 Industrial Ave #107' );
			$this->shipperAddress->setPostalCode( '28054' );
			$this->shipperAddress->setCity( 'GASTONIA' );
			$this->shipperAddress->setStateProvinceCode( 'NC' ); // required in US
			$this->shipperAddress->setCountryCode( 'US' );
			$this->shipper->setAddress( $this->shipperAddress );
			$this->shipper->setEmailAddress( 'hello@hoodsly.com' );
			$this->shipper->setPhoneNumber( '8778470405' );
			$this->shipment->setShipper( $this->shipper );

			// shipping
			$address_state = $shipping['state'] ? $shipping['state'] : $shipping['country'];
			$this->address = new \Ups\Entity\Address();
			$this->address->setAddressLine1( $shipping['address_1'] );
			$this->address->setAddressLine2( $shipping['address_2'] );
			$this->address->setPostalCode( $shipping['postcode'] );
			$this->address->setCity( $shipping['city'] );
			$this->address->setStateProvinceCode( $address_state );  // Required in US
			$this->address->setCountryCode( $shipping['country'] );
			$this->shipTo = new \Ups\Entity\ShipTo();
			$this->shipTo->setAddress( $this->address );
			$this->shipTo->setCompanyName( $shipping['first_name'] . ' ' . $shipping['last_name'] );
			$this->shipTo->setAttentionName( sprintf( '%s %s', $shipping['first_name'], $shipping['last_name'] ) );
			$this->shipTo->setEmailAddress( $billing['email'] );
			$this->shipTo->setPhoneNumber( $billing['phone'] );
			$this->shipment->setShipTo( $this->shipTo );

			// From address
			$this->address = new \Ups\Entity\Address();
			$this->address->setAddressLine1( '1236 Industrial Ave #107' );
			$this->address->setPostalCode( '28054' );
			$this->address->setCity( 'GASTONIA' );
			$this->address->setStateProvinceCode( 'NC' );
			$this->address->setCountryCode( 'US' );
			$this->shipFrom = new \Ups\Entity\ShipFrom();
			$this->shipFrom->setAddress( $this->address );
			$this->shipFrom->setName( 'HypeMill' );
			$this->shipFrom->setAttentionName( $this->shipFrom->getName() );
			$this->shipFrom->setCompanyName( 'HypeMill/Red Industries' );
			$this->shipFrom->setEmailAddress( 'hello@hoodsly.com' );
			$this->shipFrom->setPhoneNumber( '8778470405' );
			$this->shipment->setShipFrom( $this->shipFrom );

			// Sold to
			$this->address = new \Ups\Entity\Address();
			$this->address->setAddressLine1( $shipping['address_1'] );
			$this->address->setAddressLine2( $shipping['address_2'] );
			$this->address->setPostalCode( $shipping['postcode'] );
			$this->address->setCity( $shipping['city'] );
			$this->address->setCountryCode( $shipping['country'] );
			$this->address->setStateProvinceCode( $address_state );
			$this->soldTo = new \Ups\Entity\SoldTo();
			$this->soldTo->setAddress( $this->address );
			$this->soldTo->setAttentionName( sprintf( '%s %s', $shipping['first_name'], $shipping['last_name'] ) );
			$this->soldTo->setCompanyName( $this->soldTo->getAttentionName() );
			$this->soldTo->setEmailAddress( $billing['email'] );
			$this->soldTo->setPhoneNumber( $billing['phone'] );
			$this->shipment->setSoldTo( $this->soldTo );

			// Set service
			$this->service = new \Ups\Entity\Service();
			$this->service->setCode( \Ups\Entity\Service::S_GROUND );
			$this->service->setDescription( $this->service->getName() );
			$this->shipment->setService( $this->service );

			// Set description
			$this->shipment->setDescription( 'String' );

			// Add Package
			$this->package = new \Ups\Entity\Package();
			$this->package->getPackagingType()->setCode( \Ups\Entity\PackagingType::PT_PACKAGE );
			$this->package->getPackageWeight()->setWeight( $weight );
			$this->unit = new \Ups\Entity\UnitOfMeasurement();
		if ( $woocommerce_weight_unit == 'kg' ) {
			$this->unit->setCode( \Ups\Entity\UnitOfMeasurement::UOM_KGS );
		} elseif ( $woocommerce_weight_unit == 'lbs' ) {
			$this->unit->setCode( \Ups\Entity\UnitOfMeasurement::UOM_LBS );
		}
			$this->unit->setCode( \Ups\Entity\UnitOfMeasurement::UOM_LBS );
			$this->package->getPackageWeight()->setUnitOfMeasurement( $this->unit );

			// Set Package Service Options
			$packageServiceOptions = new \Ups\Entity\PackageServiceOptions();
			$packageServiceOptions->setShipperReleaseIndicator( true );
			$this->package->setPackageServiceOptions( $packageServiceOptions );

			// Set dimensions
			$this->dimensions = new \Ups\Entity\Dimensions();
			$this->dimensions->setHeight( $height );
			$this->dimensions->setWidth( $width );
			$this->dimensions->setLength( $length );
			$this->unit = new \Ups\Entity\UnitOfMeasurement();
		if ( $woocommerce_dimension_unit == 'cm' ) {
			$this->unit->setCode( \Ups\Entity\UnitOfMeasurement::UOM_CM );
		} elseif ( $woocommerce_dimension_unit == 'in' ) {
			$this->unit->setCode( \Ups\Entity\UnitOfMeasurement::UOM_IN );
		}
			$this->unit->setCode( \Ups\Entity\UnitOfMeasurement::UOM_IN );
			$this->dimensions->setUnitOfMeasurement( $this->unit );
			$this->package->setDimensions( $this->dimensions );

			// Add descriptions because it is a package
			$this->package->setDescription( 'XX' );

			// Add this package
			$this->shipment->addPackage( $this->package );
			// Set payment information
			$this->shipment->setPaymentInformation( new \Ups\Entity\PaymentInformation( 'prepaid', (object) array( 'AccountNumber' => '0A160X' ) ) );

			// Ask for negotiated rates (optional)
			$this->rateInformation = new \Ups\Entity\RateInformation();
			$this->rateInformation->setNegotiatedRatesIndicator( 1 );
			$this->shipment->setRateInformation( $this->rateInformation );
			// Get shipment info
		try {
			$this->api = new Ups\Shipping( self::ACCESSKEY, self::USERID, self::PASSWORD );
			$confirm   = $this->api->confirm( \Ups\Shipping::REQ_VALIDATE, $this->shipment );
			update_post_meta( $order_id, 'created_shipments_details_array', (array) $confirm );
			update_post_meta( $order_id, 'ShipmentIdentificationNumber', $confirm->ShipmentIdentificationNumber );

			if ( $confirm ) {
				$accept = $this->api->accept( $confirm->ShipmentDigest );
				update_post_meta( $order_id, 'confirmation_data_array', (array) $accept );
			}
		} catch ( \Exception $e ) {
			var_dump($e);
		}

			$pdf           = $this->shipping_label_dir . '/' . "$order_id.gif";
			$base64_string = $accept->PackageResults->LabelImage->GraphicImage;
			$ifp           = fopen( $pdf, 'wb' );
			fwrite( $ifp, base64_decode( $base64_string ) );
			fclose( $ifp );
			update_post_meta($order_id, 'shipping_label_url', $pdf);

			$degrees = 270;
			$source  = imagecreatefromgif( $pdf );
			// // Rotate
			$rotate     = imagerotate( $source, $degrees, 0 );
			$imgResized = imagescale( $rotate, 528, 816 );
			/////////////////////////////////////
			imagegif( $imgResized, $pdf );
	}
}

/**
 * initialise the main function
 *
 * @return void
 */
function ups_shipping_label() {
	 return UPSShippingLabel::init();
}

// let's start the plugin
ups_shipping_label();
