<?php

class ParentOrderController extends ParentOrderControllerCore
{
	protected function _assignPayment()
	{
		self::$smarty->assign(array(
		   'HOOK_TOP_PAYMENT' => Module::hookExec('paymentTop'),
			'HOOK_PAYMENT' => Module::hookExecPaymentFront(Tools::getValue('id_carrier', self::$cart->id_carrier))//Module::hookExecPayment()

		));
	}
}