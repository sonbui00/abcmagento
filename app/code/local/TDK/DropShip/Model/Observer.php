<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/4/18
 * Time: 11:39 AM
 */

class TDK_DropShip_Model_Observer {

	public function salesOrderPlaceBefore( $event ) {
		/** @var Mage_Sales_Model_Order $order */
		$order = $event->getOrder();
		if ( ! Mage::helper( 'tdk_dropship' )->allowDropShipForOrder( $order ) ) {
			return;
		}
		$order->setData( 'can_ship_partially_item', 0 );
	}

	public function salesOrderPlaceAfter( $event ) {
		/** @var Mage_Sales_Model_Order $order */
		$order = $event->getOrder();
		if ( ! Mage::helper( 'tdk_dropship' )->allowDropShipForOrder( $order ) ) {
			return;
		}
		$supplierId    = array();
		$supplierItems = array();
		/** @var $item Mage_Sales_Model_Order_Item */
		foreach ( $order->getAllItems() as $item ) {
			$_id = (int) $item->getSupplierId();
			if ( $_id && ! in_array( $_id, $supplierId ) ) {
				$supplierId[]          = $_id;
				$supplierItems[ $_id ] = array();
				Mage::getModel( 'tdk_dropship/supplierOrder' )
				    ->setOrderId( $order->getId() )
				    ->setSupplierId( $_id )
				    ->save();

				Mage::helper( 'tdk_dropship/email' )->newOrderSupplier( $order, $_id );
			}

			$supplierItems[ $_id ][] = $item;
		}

		/* create shipment and label */
		/*
		$savedQtys
		Array
		(
			[3132] => 1
			[3133] => 0
			[3134] => 1
		)

		[packages] => Array
		(
			[1] => Array
				(
					[params] => Array
						(
							[container] =>
							[weight] => 17
							[customs_value] => 469.98
							[length] =>
							[width] =>
							[height] =>
							[weight_units] => POUND
							[dimension_units] => INCH
							[content_type] =>
							[content_type_other] =>
							[delivery_confirmation] => True
						)

					[items] => Array
						(
							[3132] => Array
								(
									[qty] => 1
									[customs_value] => 69.99
									[price] => 69.9900
									[name] => POP 2 (60's+70's+80'sRock) - English MagicSing Chip
									[weight] => 1.0000
									[product_id] => 126
									[order_item_id] => 3132
								)

							[3134] => Array
								(
									[qty] => 1
									[customs_value] => 399.99
									[price] => 399.9900
									[name] => EnterTech OnStage ET28KH Microphone Karaoke Player - English Edition
									[weight] => 16.0000
									[product_id] => 151
									[order_item_id] => 3134
								)

						)

				)

		)

		*/

		foreach ( $supplierItems as $supplierId => $items ) {
			Mage::unregister('current_supplier_id');
			Mage::register('current_supplier_id', $supplierId);
			$savedQtys = array();
			$package   = array(
				1 => array(
					'params' => array(
						'container'             => '',
						'weight'                => 17,
						'customs_value'         => 469.98,
						'length'                => '',
						'width'                 => '',
						'height'                => '',
						'weight_units'          => 'POUND',
						'dimension_units'       => 'INCH',
						'content_type'          => '',
						'content_type_other'    => '',
						'delivery_confirmation' => true,
					),
					'items'  => array()
				)
			);
			foreach ( $items as $item ) {
				$savedQtys[ $item->getId() ] = $item->getQtyOrdered();
				$packageItem                 = array(
					'qty'           => $item->getQtyOrdered(),
					'customs_value' => $item->getPrice(),
					'price'         => $item->getPrice(),
					'name'          => $item->getName(),
					'weight'        => $item->getWeight(),
					'product_id'    => $item->getProductId(),
					'order_item_id' => $item->getId(),
				);
				$package[1]['items'][$item->getId()] = $packageItem;
			}
			$shipment = Mage::getModel( 'sales/service_order', $order )->prepareShipment( $savedQtys );

			$shipment->register();

			$shipment->setEmailSent( true );

			try {
				$this->_createShippingLabel( $shipment, $package );
			} catch ( Exception $e ) {
				$message = 'An error occurred while creating shipping label. Error: ' . $e->getMessage();
				$shipment->addComment($message);
			}

			$shipment->getOrder()->setIsInProcess( true );

			Mage::getModel( 'core/resource_transaction' )
			    ->addObject( $shipment )
			    ->addObject( $shipment->getOrder() )
			    ->save();

		}
	}

	public function salesOrderShipmentSaveAfter( $event ) {
		/** @var Mage_Sales_Model_Order_Shipment $shipment */
		$shipment   = $event->getShipment();
		$supplierId = (int) Mage::app()->getRequest()->getParam( 'supplier_id', false );
		if (!$supplierId) {
			$supplierId = Mage::registry('current_supplier_id');
		}
		if ( $supplierId ) {
			/** @var TDK_DropShip_Model_SupplierOrder $supplierOrder */
			$supplierOrder = Mage::getResourceModel( 'tdk_dropship/supplierOrder_collection' )
			                     ->addFieldToFilter( 'order_id', $shipment->getOrderId() )
			                     ->addFieldToFilter( 'supplier_id', $supplierId )
			                     ->getFirstItem();
			if ( $supplierOrder->getId() ) {
				$supplierOrder->setShipmentId( $shipment->getId() );
				$supplierOrder->setShipmentIncrementId( $shipment->getIncrementId() );
				$supplierOrder->save();
			}
		}
	}

	protected function _createShippingLabel( Mage_Sales_Model_Order_Shipment $shipment, $package ) {
		if ( ! $shipment ) {
			return false;
		}
		$carrier = $shipment->getOrder()->getShippingCarrier();
		if ( ! $carrier->isShippingLabelsAvailable() ) {
			return false;
		}
		$shipment->setPackages( $package );
		$response = Mage::getModel( 'shipping/shipping' )->requestToShipment( $shipment );
		if ( $response->hasErrors() ) {
			Mage::throwException( $response->getErrors() );
		}
		if ( ! $response->hasInfo() ) {
			return false;
		}
		$labelsContent   = array();
		$trackingNumbers = array();
		$info            = $response->getInfo();
		foreach ( $info as $inf ) {
			if ( ! empty( $inf['tracking_number'] ) && ! empty( $inf['label_content'] ) ) {
				$labelsContent[]   = $inf['label_content'];
				$trackingNumbers[] = $inf['tracking_number'];
			}
		}
		$outputPdf = $this->_combineLabelsPdf( $labelsContent );
		$shipment->setShippingLabel( $outputPdf->render() );
		$carrierCode  = $carrier->getCarrierCode();
		$carrierTitle = Mage::getStoreConfig( 'carriers/' . $carrierCode . '/title', $shipment->getStoreId() );
		if ( $trackingNumbers ) {
			foreach ( $trackingNumbers as $trackingNumber ) {
				$track = Mage::getModel( 'sales/order_shipment_track' )
				             ->setNumber( $trackingNumber )
				             ->setCarrierCode( $carrierCode )
				             ->setTitle( $carrierTitle );
				$shipment->addTrack( $track );
			}
		}

		return true;
	}


	/**
	 * Combine array of labels as instance PDF
	 *
	 * @param array $labelsContent
	 *
	 * @return Zend_Pdf
	 */
	protected function _combineLabelsPdf( array $labelsContent ) {
		$outputPdf = new Zend_Pdf();
		foreach ( $labelsContent as $content ) {
			if ( stripos( $content, '%PDF-' ) !== false ) {
				$pdfLabel = Zend_Pdf::parse( $content );
				foreach ( $pdfLabel->pages as $page ) {
					$outputPdf->pages[] = clone $page;
				}
			} else {
				$page = $this->_createPdfPageFromImageString( $content );
				if ( $page ) {
					$outputPdf->pages[] = $page;
				}
			}
		}

		return $outputPdf;
	}

	/**
	 * Create Zend_Pdf_Page instance with image from $imageString. Supports JPEG, PNG, GIF, WBMP, and GD2 formats.
	 *
	 * @param string $imageString
	 *
	 * @return Zend_Pdf_Page|bool
	 */
	protected function _createPdfPageFromImageString( $imageString ) {
		$image = imagecreatefromstring( $imageString );
		if ( ! $image ) {
			return false;
		}

		$xSize = imagesx( $image );
		$ySize = imagesy( $image );
		$page  = new Zend_Pdf_Page( $xSize, $ySize );

		imageinterlace( $image, 0 );
		$tmpFileName = sys_get_temp_dir() . DS . 'shipping_labels_'
		               . uniqid( mt_rand() ) . time() . '.png';
		imagepng( $image, $tmpFileName );
		$pdfImage = Zend_Pdf_Image::imageWithPath( $tmpFileName );
		$page->drawImage( $pdfImage, 0, 0, $xSize, $ySize );
		unlink( $tmpFileName );

		return $page;
	}
}