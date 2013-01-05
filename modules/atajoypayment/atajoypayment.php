<?php


if (!defined('_PS_VERSION_'))
	exit;

class AtajoyPayment extends PaymentModule
{	
	public function __construct()
	{
		$this->name = 'atajoypayment';
		$this->tab = 'payments_gateways';
		$this->version = '1.0';
		$this->author = 'Domanjiri';
		$this->need_instance = 0;
		
		$this->currencies = false;

		parent::__construct();

		$this->displayName = $this->l('Cash on delivery by Atajoy (COD)');
		$this->description = $this->l('Accept cash on delivery payments');

		
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn'))
			return false;
            
        if (!Configuration::get('ATAJOY_FAILED'))
		{
			$orderState = new OrderState();
			$orderState->name = array();
			foreach (Language::getLanguages() AS $language)
			{
				if (strtolower($language['iso_code']) == 'ir')
					$orderState->name[$language['id_lang']] = 'سفارش در اتاجوی ثبت نشد';
				else
					$orderState->name[$language['id_lang']] = 'سفارش در اتاجوی ثبت نشد';
			}
			$orderState->send_email = false;
			$orderState->color = '#E54C10';
			$orderState->hidden = false;
			$orderState->delivery = false;
			$orderState->logable = true;
			$orderState->invoice = true;
			if ($orderState->add())
				copy(dirname(__FILE__).'/status-f.gif', dirname(__FILE__).'/../../img/os/'.(int)$orderState->id.'.gif');
			Configuration::updateValue('ATAJOY_FAILED', (int)$orderState->id);
		}
        
        if (!Configuration::get('ATAJOY_SENT'))
		{
			$orderState = new OrderState();
			$orderState->name = array();
			foreach (Language::getLanguages() AS $language)
			{
				if (strtolower($language['iso_code']) == 'ir')
					$orderState->name[$language['id_lang']] = 'در اتاجوی ثبت گردید';
				else
					$orderState->name[$language['id_lang']] = 'در اتاجوی ثبت گردید';
			}
			$orderState->send_email = false;
			$orderState->color = '#E5D840';
			$orderState->hidden = false;
			$orderState->delivery = false;
			$orderState->logable = true;
			$orderState->invoice = true;
			if ($orderState->add())
				copy(dirname(__FILE__).'/status-s.gif', dirname(__FILE__).'/../../img/os/'.(int)$orderState->id.'.gif');
			Configuration::updateValue('ATAJOY_SENT', (int)$orderState->id);
		}
        
		return true;
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return ;

		global $smarty;

		// Check if cart has product download
		foreach ($params['cart']->getProducts() AS $product)
		{
			$pd = ProductDownload::getIdFromIdProduct((int)($product['id_product']));
			if ($pd AND Validate::isUnsignedInt($pd))
				return false;
		}

		$smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}
	
	public function hookPaymentReturn($params)
	{
	   global $smarty;
		if (!$this->active)
			return ;
       
		$row = Db::getInstance()->getValue('
		SELECT invoice FROM '._DB_PREFIX_.'atajoy_order
		WHERE id_order = '.(int)($params['objOrder']->id));
        
        $smarty->assign('invoice', $row);
        
		return $this->display(__FILE__, 'confirmation.tpl');
	}
}
