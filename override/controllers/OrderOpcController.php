<?php

class OrderOpcController extends OrderOpcControllerCore
{
	protected function _getPaymentMethods()
	{
		if (!$this->isLogged)
			return '<p class="warning">'.Tools::displayError('Please sign in to see payment methods').'</p>';
		if (self::$cart->OrderExists())
			return '<p class="warning">'.Tools::displayError('Error: this order is already validated').'</p>';
		if (!self::$cart->id_customer OR !Customer::customerIdExistsStatic(self::$cart->id_customer) OR Customer::isBanned(self::$cart->id_customer))
			return '<p class="warning">'.Tools::displayError('Error: no customer').'</p>';
		$address_delivery = new Address(self::$cart->id_address_delivery);
		$address_invoice = (self::$cart->id_address_delivery == self::$cart->id_address_invoice ? $address_delivery : new Address(self::$cart->id_address_invoice));
		if (!self::$cart->id_address_delivery OR !self::$cart->id_address_invoice OR !Validate::isLoadedObject($address_delivery) OR !Validate::isLoadedObject($address_invoice) OR $address_invoice->deleted OR $address_delivery->deleted)
			return '<p class="warning">'.Tools::displayError('Error: please choose an address').'</p>';
		if (!self::$cart->id_carrier AND !self::$cart->isVirtualCart())
			return '<p class="warning">'.Tools::displayError('Error: please choose a carrier').'</p>';
		elseif (self::$cart->id_carrier != 0)
		{
			$carrier = new Carrier((int)(self::$cart->id_carrier));
			if (!Validate::isLoadedObject($carrier) OR $carrier->deleted OR !$carrier->active)
				return '<p class="warning">'.Tools::displayError('Error: the carrier is invalid').'</p>';
		}
		if (!self::$cart->id_currency)
			return '<p class="warning">'.Tools::displayError('Error: no currency has been selected').'</p>';
		if (!self::$cookie->checkedTOS AND Configuration::get('PS_CONDITIONS'))
			return '<p class="warning">'.Tools::displayError('Please accept Terms of Service').'</p>';

		/* If some products have disappear */
		if (!self::$cart->checkQuantities())
			return '<p class="warning">'.Tools::displayError('An item in your cart is no longer available, you cannot proceed with your order.').'</p>';

		/* Check minimal amount */
		$currency = Currency::getCurrency((int)self::$cart->id_currency);

		$minimalPurchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
		if (self::$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimalPurchase)
			return '<p class="warning">'.Tools::displayError('A minimum purchase total of').' '.Tools::displayPrice($minimalPurchase, $currency).
			' '.Tools::displayError('is required in order to validate your order.').'</p>';

		/* Bypass payment step if total is 0 */
		if (self::$cart->getOrderTotal() <= 0)
			return '<p class="center"><input type="button" class="exclusive_large" name="confirmOrder" id="confirmOrder" value="'.Tools::displayError('I confirm my order').'" onclick="confirmFreeOrder();" /></p>';

		$return = Module::hookExecPaymentFront(Tools::getValue('id_carrier', self::$cart->id_carrier));//Module::hookExecPayment();
		if (!$return)
			return '<p class="warning">'.Tools::displayError('No payment method is available').'</p>';
		return $return;
	}
}