<?php

namespace jjacquesf\yii2\storecomponent;

use Yii;
use yii\base\Component;

class Store extends Component
{
	public $cart;

	public function init()
	{
		parent::init();

		$this->cart = Yii::$app->session->get('shopping_cart', false);
		if(!$this->cart) {
			$this->cart = new SC();
			Yii::$app->session->set('shopping_cart', $this->cart);
		}
	}

	public function reset()
	{
		$this->cart = new SC();
		Yii::$app->session->set('shopping_cart', $this->cart);
	}
}