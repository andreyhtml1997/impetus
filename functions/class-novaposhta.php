<?php

if (!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

class NOVAPOSHTA
{

	public function __construct()
	{

		$this->load_phpnovaposhta();

		$this->api_key = '8938b785f9283c683fff3d7d801858ec'; // = get_field( 'twilio_api_sid', 'option' );


		NovaPoshta\Config::setApiKey($this->api_key);
		NovaPoshta\Config::setFormat(NovaPoshta\Config::FORMAT_JSONRPC2);
		NovaPoshta\Config::setLanguage(NovaPoshta\Config::LANGUAGE_UA);
	}

	public function load_phpnovaposhta()
	{

		require_once get_template_directory() . '/functions/NovaPoshta/bootstrap.php';
		require_once get_template_directory() . '/functions/NovaPoshta/Config.php';
	}

	/* public function get_cities_novaposhta_api( $page = 1, $limit = false ) {

		$data = new NovaPoshta\MethodParameters\Address_getCities();

		$data->setPage( $page );
		$data->setLimit( $limit );

		return NovaPoshta\ApiModels\Address::getCities( $data );
	} */

	private function get_response($response)
	{

		if (!$response->success)
			return false;

		return $response->data;
	}

	public function get_novaposhta_settlements($args = array())
	{

		$obj = new NovaPoshta\MethodParameters\AddressGeneral_getSettlements();

		if (isset($args['page']))
			$obj->setPage($args['page']);

		if (isset($args['limit']))
			$obj->setLimit($args['limit']);

		//localities where there are Nova Poshta branches
		if (isset($args['is_warehouse']))
			$obj->setWarehouse($args['is_warehouse']);

		//search for a place by name
		if (isset($args['find_by']))
			$obj->setFindByString($args['find_by']);

		$response = NovaPoshta\ApiModels\AddressGeneral::getSettlements($obj);

		return $this->get_response($response);
	}

	public function get_novaposhta_warehouse_types()
	{
		$response = NovaPoshta\ApiModels\Address::getWarehouseTypes();
		return $this->get_response($response);
	}

	public function get_novaposhta_warehouses($args = array())
	{

		$obj = new NovaPoshta\MethodParameters\Address_getWarehouses();

		if (isset($args['page']))
			$obj->setPage($args['page']);

		if (isset($args['limit']))
			$obj->setLimit($args['limit']);

		if (isset($args['settlement_ref']))
			$obj->setSettlementRef($args['settlement_ref']);

		if (isset($args['find_by']))
			$obj->setFindByString($args['find_by']);

		if (isset($args['type_ref'])) {

			// в разных версиях либы имя метода может отличаться
			if (method_exists($obj, 'setTypeOfWarehouseRef')) {
				$obj->setTypeOfWarehouseRef($args['type_ref']);
			}

			if (method_exists($obj, 'setTypeOfWarehouseRef') == false) {
				if (method_exists($obj, 'setTypeOfWarehouse')) {
					$obj->setTypeOfWarehouse($args['type_ref']);
				}
			}
		}

		$response = NovaPoshta\ApiModels\Address::getWarehouses($obj);

		return $this->get_response($response);
	}





}

