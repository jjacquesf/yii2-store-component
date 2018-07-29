<?php

namespace jjacquesf\yii2\storecomponent;

use Yii;
use yii\base\Object;

class SC extends Object
{
	private $_id;
	private $_code;
	private $_items;
	private $_shipping_cost = 150;
	private $_free_shipping_amount = 3000;

	public function init()
	{
		$this->_id = $this->generateId();
		$this->_code = '';
		$this->_items = [];
	}

	public function getId()
	{
		return $this->_id;
	}

	public function clear()
	{
		$this->_items = [];
	}

	public function canConfirm()
	{
		return $this->getItemCount() && $this->getTotal() > 0;
	}

	public function addItem($qty, $product_id, $product_config_id, $code = '')
	{
		$item = new SCItem($qty, $product_id, $product_config_id, $code);
		if($item->qty > 0) {

			if(($exist = $this->getItem($item->id))) {
				$qty = $exist->qty += $item->qty;
				$this->setItemQty($exist->id, $qty);
				return $exist->id;
			}

			$this->_items[$item->getId()] = $item;
			return $item->getId();
		}

		return false;
	}

	public function itemExists($id)
	{
		return isset($this->_items[$id]);
	}

	public function setItemQty($id, $qty)
	{
		if($this->itemExists($id)) {

			if($qty <= 0)
				return $this->removeItem($id);

			return $this->_items[$id]->setQty($qty);
		}
		
		return false;
	}

	public function removeItem($id)
	{
		if($this->itemExists($id)) {
			unset($this->_items[$id]);
			return true;
		}
		
		return false;
	}

	public function getItems()
	{
		return $this->_items;
	}


	public function getItem($id)
	{
		if($this->itemExists($id)) {
			return $this->_items[$id];
		}
		
		return false;
	}

	public function getItemCount()
	{
		return count($this->_items);
	}

	public function getSubtotal()
	{
		$amount = 0;
		foreach($this->_items as $item)
		{
			$amount += $item->getSubtotal();
		}

		return $amount;
	}

	public function getDiscount()
	{
		$amount = 0;
		return $amount;
	}

	public function getShippingCost()
	{
		if($this->getSubtotal() > 0) {
			if($this->getSubtotal() >= $this->_free_shipping_amount) {
				return 0;
			}

			return $this->_shipping_cost;
		}

		return 0;
	}

	public function getTotal()
	{
		$amount = 0;
		foreach($this->_items as $item)
		{
			$amount += $item->getTotal();
		}

		return $amount + $this->getShippingCost() - $this->getDiscount();

	}

	private function generateId()
	{
		return uniqid('ord_', TRUE);
	}
}