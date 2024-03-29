<?php
/* 
Upraveno pro Prestashop 1.4.0.15
Upravil: Ladis-s - ladis-s@centrum.cz

SHIP2PAY module by Szymon Losik -szazman@wp.pl 

*/
class Shiptopay extends Module
{
	function __construct()
	{
		$this->name = 'shiptopay';
		$this->tab = 'Blocks';
		$this->version = 2.0;

		parent::__construct(); // The parent construct is required for translations

		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('Ship to Pay');
		$this->description = $this->l('Module that matches shipment method to payment method');
	}

	function install()
	{
		if (!parent::install()){
			return false;
		}
		$sql='CREATE TABLE `'._DB_PREFIX_.'shiptopay` (
`id_carrier` INT( 11 ) NOT NULL ,
`id_payment` INT( 11 ) NOT NULL
) ENGINE = MYISAM';
		mysql_query($sql);
		return true;
	}
	public function uninstall()
	{
		
		if (!parent::uninstall()){
			return false;
				}
		$sql='DROP TABLE '._DB_PREFIX_.'_shiptopay';		
		mysql_query($sql);		
		return true;	
			
	}
	function getContent(){
		
		$output = '<h2>'.$this->displayName.'</h2>';
		
		if(Tools::isSubmit('removeShiptoPay')){
			$sql='DELETE FROM '._DB_PREFIX_.'shiptopay WHERE id_carrier="'.Tools::getValue('carr').'" AND id_payment="'.Tools::getValue('pay').'"'; 
			mysql_query($sql);
			
		}elseif(Tools::isSubmit('addShiptoPay')){
			if((Tools::getValue('carr'))AND (Tools::getValue('pay'))){
			$sql='INSERT INTO '._DB_PREFIX_.'shiptopay VALUES('.Tools::getValue('carr').','.Tools::getValue('pay').')';
			mysql_query($sql);
			}
		}
		$output.=$this->infoForm();
		return $output;
	}

	/**
	* Returns module content
	*
	* @param array $params Parameters
	* @return string Content
	*/
	function infoForm(){
		global $cookie;
	
	
	$carr=$this->getCarriers($cookie->id_lang,1, false, false);
	
	$pay=$this->getPayment();
	
	$output= '<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/shiptopay/shiptopay.js"></script>';
	$output.= '<link type="text/css" rel="stylesheet" href="'.__PS_BASE_URI__.'modules/shiptopay/shiptopay.css" />';
	
	$output.='<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="7953161">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/pl_PL/i/scr/pixel.gif" width="1" height="1">
</form><br/><fieldset><legend>'.$this->l('Settings').'</legend>';
	$output.='<h2>'.$this->l('Combinations').'</h2><table class="table width3"><tr><th>'.$this->l('Shipment method').'</th><th>'.$this->l('Payment method').'</th><th>&nbsp;</th></tr>';
	 $sql='SELECT distinct(ca.name) as carrier,m.id_module,stp.id_carrier,h.`id_hook`, m.`name`, hm.`position`
		FROM `'._DB_PREFIX_.'module_country` mc
		LEFT JOIN `'._DB_PREFIX_.'module` m ON m.`id_module` = mc.`id_module`
		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
		LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
		LEFT JOIN `'._DB_PREFIX_.'shiptopay` stp ON hm.`id_module` = stp.`id_payment`
		LEFT JOIN `'._DB_PREFIX_.'carrier` ca ON stp.`id_carrier` = ca.`id_carrier`
		WHERE h.`name` = \'payment\'';
		
		
	$shiptopay = Db::getInstance()->ExecuteS($sql);
	foreach ($shiptopay as $items){
		if($items['id_carrier']!=NULL){
	$output.='<tr><td>'.$items['carrier'].'</td><td>'.$pay[$items['name']]->displayName.'</td><td><form method="post" action=""><input type="hidden" name="carr" value="'.$items['id_carrier'].'"><input type="hidden" name="pay" value="'.$items['id_module'].'"><input type="image" src="'.__PS_BASE_URI__.'modules/shiptopay/list-remove.gif" name="removeShiptoPay" alt="remove" title="remove"></form></td></tr>';
	}
	}
	$output.='</table>';
	
	
	
	$output.= '<form method="post" action="" onsubmit="return validateFormSTP();"><div class="ship_container">';
	$output.='<h2>'.$this->l('Shipment method').'</h2>';
	$output.='<select name="carr" name="id" size="5" >';
	foreach ($carr as $ship){
			$output.= '<option value="'.$ship['id_carrier'].'" style="display:block;">'.$ship['name'].'</option>';	
	}
	
	$output.='</select></div>';
	$output.='<div class="pay_container">';
	$output.='<h2>'.$this->l('Payment method').'</h2>';
	$output.='<select name="pay" id="pay" size="5">';
	foreach($pay as $payment){
		$output.= '<option value="'.$payment->id.'" style="display:block;">'.$payment->displayName.'</option>';
	}	
	$output.='</select></div>';
	$output.= '<br style="clear:both;">';
	$output.= '<button name="addShiptoPay">'.$this->l('Save Combinations').'</button></form></fieldset>';
	return $output;
	
		}
	public static function getCarriers($id_lang, $active = false, $delete = false, $id_zone = false)
	{
	 	if (!Validate::isBool($active))
	 		die(Tools::displayError());
		$sql = '
			SELECT c.*, cl.delay
			FROM `'._DB_PREFIX_.'carrier` c
			LEFT JOIN `'._DB_PREFIX_.'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = '.$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'carrier_zone` cz  ON (cz.`id_carrier` = c.`id_carrier`)'.
			($id_zone ? 'LEFT JOIN `'._DB_PREFIX_.'zone` z  ON (z.`id_zone` = '.$id_zone.')' : '').'
			WHERE c.`deleted` '.($delete ? '= 1' : ' = 0').
			($active ? ' AND c.`active` = 1' : '').
			($id_zone ? ' AND cz.`id_zone` = '.$id_zone.'
			AND z.`active` = 1' : '').'
			GROUP BY c.`id_carrier`';
		$carriers = Db::getInstance()->ExecuteS($sql);
		foreach ($carriers as $key => $carrier)
			if ($carrier['name'] == '0')
				$carriers[$key]['name'] = Configuration::get('PS_SHOP_NAME');
		return $carriers;
	}
	public static function getPayment()
	{
	 	$paymenty= Db::getInstance()->ExecuteS('
		SELECT *,m.name as pay_name
		FROM `'._DB_PREFIX_.'module` m
		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON m.`id_module` = hm.`id_module`
		LEFT JOIN `'._DB_PREFIX_.'hook` k ON hm.`id_hook` = k.`id_hook`
		WHERE k.`position` = 1 AND k.id_hook=1');
		
		foreach ($paymenty as $paymod){
			if (@include_once _PS_MODULE_DIR_.'/'.$paymod['pay_name'].'/'.$paymod['pay_name'].'.php')
				$moduleList[$paymod['pay_name']] = new $paymod['pay_name'];
		}
		return $moduleList;
	}

}