<?php
class Module extends ModuleCore
{
	/**
     * ship2pay function, get only active for current shiping payment modules 
     * */
	public static function hookExecPaymentFront($carrier)
	{
		global $cart, $cookie;
		$sql='SELECT * FROM `'._DB_PREFIX_.'shiptopay`';
		$result = Db::getInstance()->ExecuteS($sql);
        
		if(count($result)==0){
            Module::hookExecPayment();
		}else{
    		$hookArgs = array('cookie' => $cookie, 'cart' => $cart);
    		$billing = new Address(intval($cart->id_address_invoice));
    		$output = '';
    		$sql='
    		SELECT distinct(stp.id_carrier),h.`id_hook`, m.`name`, hm.`position`
    		FROM `'._DB_PREFIX_.'module_country` mc
    		LEFT JOIN `'._DB_PREFIX_.'module` m ON m.`id_module` = mc.`id_module`
    		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
    		LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
    		LEFT JOIN `'._DB_PREFIX_.'shiptopay` stp ON hm.`id_module` = stp.`id_payment`
    		WHERE h.`name` = \'payment\'
    		AND stp.id_carrier='.intval($carrier).'
    		AND mc.id_country = '.intval($billing->id_country).'
    		AND m.`active` = 1
    		ORDER BY hm.`position`, m.`name` DESC';
    		$result = Db::getInstance()->ExecuteS($sql);
    		if ($result)
    			foreach ($result AS $k => $module)
    				if (($moduleInstance = Module::getInstanceByName($module['name'])) AND is_callable(array($moduleInstance, 'hookpayment')))
    					if (!$moduleInstance->currencies OR ($moduleInstance->currencies AND sizeof(Currency::checkPaymentCurrencies($moduleInstance->id))))
    						$output .= call_user_func(array($moduleInstance, 'hookpayment'), $hookArgs);
    		return $output;
		}
	}
}