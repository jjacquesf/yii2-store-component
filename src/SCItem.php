<?php 

namespace jjacquesf\yii2storecomponent;

use Yii;
use yii\base\Object;
use common\models\Product;

class SCItem extends Object {
	
	private $_id;
	private $_product_id;
	private $_product_config_id;
	private $_qty;
	private $_code;

	public function __construct($qty, $product_id, $product_config_id, $code = '') {

		$this->_product_id = $product_id;
		$product = $this->getProduct();
		if($product) {
			$this->_id = $this->generateId($product->id, $product_config_id);
			// $this->_qty = $qty;
				
			$this->_product_config_id = $product_config_id;
			$this->_code = $code;

			$this->setQty($qty);
		}
	}

	public function getId()
	{
		return $this->_id;
	}

	public function getQty()
	{
		return $this->_qty;
	}

	public function getProductId()
	{
		return $this->_product_id;
	}

	public function getProductConfigId()
	{
		return $this->_product_config_id;
	}

	public function getCode()
	{
		return $this->_code;
	}

	public function getPrice()
	{
		$product = $this->getProduct();
		if($product) {
			return $product->getOriginalPrice();
		}

		return 0;
	}

	public function getFinalPrice()
	{
		$product = $this->getProduct();
		if($product) {
			return $product->getFinalPrice($this->getCode());
		}

		return 0;
	}

	public function getSubtotal()
	{
		$product = $this->getProduct();
		if($product) {
			return $this->_qty * $this->getPrice();
		}

		return 0;
	}

	public function getDiscount()
	{
		$product = $this->getProduct();
		if($product) {
			return $this->_qty * ($this->getPrice() - $this->getFinalPrice());
		}

		return 0;
	}

	public function getTotal()
	{
		return $this->getSubtotal() - $this->getDiscount();
	}

	public function getProduct()
	{
		if(!is_null($this->_product_id)) {
			return Product::loadModel($this->_product_id);
		}

		return false;
	}

	public function getDescription()
	{
		$product = $this->getProduct();
		if($product) {

			;
			if(($config = $this->getConfig())) {
				return $product->getFormatted('title').'. '.$config->getFormatted('description');
			}

			return $product->getFormatted('title');
		}

		return false;
	}

	public function getConfig()
	{
		$product = $this->getProduct();
		if($product && !empty($this->_product_config_id)) {
			return $product->getConfig($this->_product_config_id);
		}

		return false;
	}

	public function setQty($qty)
	{	
		$product = $this->getProduct();
		$config = $this->getConfig();

		$qty = $product->getStock($config, $qty);

		if($qty > 0 && $this->_qty != $qty) {
			$this->_qty = $qty;
			return true;
		}

		return false;
	}

	private function generateId($product_id, $config)
	{
		return md5($product_id.$config);
	}
}