<?php

if (!defined('_PS_VERSION_'))
	exit;


class Atajoy extends CarrierModule
{
	public  $id_carrier;

	private $_html = '';
	private $_postErrors = array();
	private $_moduleName = 'atajoy';
    private $weightUnit = 'gr';



	public function __construct()
	{
		$this->name = 'atajoy';
		$this->tab = 'shipping_logistics';
		$this->version = '1.1';
		$this->author = 'Presta-Shop.ir';
		$this->limited_countries = array('ir');   // iran

		parent::__construct ();

		$this->displayName = $this->l('Atajoy post service');
		$this->description = $this->l('Atajoy\'s shipping module');
        // Uninstall message
        $this->confirmUninstall = $this->l('plaese read the document before uninstall this module');   

		if (self::isInstalled($this->name))
		{
            // Its Ok!
            if (!Configuration::get('ATAJOY_USERNAME') OR 
                !Configuration::get('ATAJOY_PASSWORD'))
		        $this->warning = $this->l('ATAJOY must be configured correctly').' ';
				
        }
	}


	/**
	* Install 
	**/
	 public function install()
	{
	   // Shipping Carrier
		$carrierConfig = array(
			0 => array('name' => $this->l('atajoy'),
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => 0,
				'shipping_handling' => false,
				'range_behavior' => 0,
				'delay' =>  array('en' => 'Description 1', Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')) => $this->l('Shipping by Atajoy')),
				'id_zone' => 1,
				'is_module' => true,
				'shipping_external' => true,
				'external_module_name' => $this->_moduleName,
				'need_range' => true
			));

		$id_atajoy   = $this->installExternalCarrier($carrierConfig[0]);
        
        $curency = new Currency();
        // save id
		Configuration::updateValue('ATAJOY_CARRIER_ID', (int)$id_atajoy);
        
        if(!Configuration::updateValue('PS_WEIGHT_UNIT', 'گرم'))
            return false;
		
        if (!parent::install() OR 
         !$this->registerHook('updateCarrier') OR 
         !$this->registerHook('cart') OR 
         !$this->registerHook('invoice') OR
         !$this->registerHook('rightColumn') OR
		 !$this->registerHook('leftColumn') OR
         !Configuration::updateValue('PS_CURRENCY_DEFAULT', $curency->getIdByIsoCode('IRT')))
		      return false;
        
        $sql = array();
        $sql[] = 'UPDATE  '._DB_PREFIX_.'country SET  contains_states = "1" WHERE  iso_code ="IR" LIMIT 1;' ;
        $sql[] = 'CREATE TABLE  '._DB_PREFIX_.'atajoy_cache (
            id_atajoy_cache INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            id_cart INT( 10 ) NOT NULL ,
            hash VARCHAR( 300 ) NOT NULL ,
            price VARCHAR( 50 ) NOT NULL ,
            UNIQUE (id_cart)) ENGINE = INNODB;';
        $sql[] = 'CREATE TABLE  '._DB_PREFIX_.'atajoy_order (
                id_order INT( 10 ) NOT NULL ,
                invoice VARCHAR( 50 ) NOT NULL
                ) ENGINE = INNODB;';
         
        foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;
        
        
		return true;
	}
	
    /**
     *  Uninstall
     * */
	public function uninstall()
	{
		// Uninstall
		if (!parent::uninstall() or !$this->unregisterHook('updateCarrier') or !$this->unregisterHook('cart') or !$this->unregisterHook('invoice') or !$this->unregisterHook('rightColumn') or !$this->unregisterHook('leftColumn'))
			return false;
		
		// Delete External Carrier
		$atajoy = new Carrier((int)(Configuration::get('ATAJOY_CARRIER_ID')));
        
		// If external carrier is default set other one as default
		if (Configuration::get('PS_CARRIER_DEFAULT') == (int)($atajoy->id))
		{
			global $cookie;
			$carriersD = Carrier::getCarriers($cookie->id_lang, true, false, false, NULL, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
			foreach($carriersD as $carrierD)
				if ($carrierD['active'] AND !$carrierD['deleted'] AND ($carrierD['name'] != $this->_config['name']))
					Configuration::updateValue('PS_CARRIER_DEFAULT', $carrierD['id_carrier']);
		} 

		// delete Carrier
		$atajoy->deleted = 1;
		
        if (!$atajoy->update())
			return false;
   
        $sql = array();
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'atajoy_cache`;';
        $sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'atajoy_order`;';
        foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;

		return true;
	}
    
    /**
     *  Installing
     * */
	 public static function installExternalCarrier($config)
	{
		$carrier = new Carrier();
		$carrier->name = $config['name'];
		$carrier->id_tax_rules_group = $config['id_tax_rules_group'];
		$carrier->id_zone = $config['id_zone'];
		$carrier->active = $config['active'];
		$carrier->deleted = $config['deleted'];
		$carrier->delay = $config['delay'];
		$carrier->shipping_handling = $config['shipping_handling'];
		$carrier->range_behavior = $config['range_behavior'];
		$carrier->is_module = $config['is_module'];
		$carrier->shipping_external = $config['shipping_external'];
		$carrier->external_module_name = $config['external_module_name'];
		$carrier->need_range = $config['need_range'];

		$languages = Language::getLanguages(true);
		foreach ($languages as $language)
		{
			if ($language['iso_code'] == 'en')
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
			if ($language['iso_code'] == Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')))
				$carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
		}

		if ($carrier->add())
		{
			$groups = Group::getGroups(true);
			foreach ($groups as $group)
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_group', array('id_carrier' => (int)($carrier->id), 'id_group' => (int)($group['id_group'])), 'INSERT');
            // price range
			$rangePrice = new RangePrice();
			$rangePrice->id_carrier = $carrier->id;
			$rangePrice->delimiter1 = '0';           // lower bound
			$rangePrice->delimiter2 = '10000000';    // upper bound
			$rangePrice->add();
            // weight range
			$rangeWeight = new RangeWeight();
			$rangeWeight->id_carrier = $carrier->id;
			$rangeWeight->delimiter1 = '0';          // lower bound
			$rangeWeight->delimiter2 = '100000';     // ...
			$rangeWeight->add();

			$zones = Zone::getZones(true);
			foreach ($zones as $zone)
			{
				Db::getInstance()->autoExecute(_DB_PREFIX_.'carrier_zone', array('id_carrier' => (int)($carrier->id), 'id_zone' => (int)($zone['id_zone'])), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => (int)($rangePrice->id), 'id_range_weight' => NULL, 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
				Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'delivery', array('id_carrier' => (int)($carrier->id), 'id_range_price' => NULL, 'id_range_weight' => (int)($rangeWeight->id), 'id_zone' => (int)($zone['id_zone']), 'price' => '0'), 'INSERT');
			}

			// Copy Logo
			if (!copy(dirname(__FILE__).'/atajoy.jpg', _PS_SHIP_IMG_DIR_.'/'.(int)$carrier->id.'.jpg'))
				return false;

			// Return ID Carrier
			return (int)($carrier->id);
		}

		return false;
	}
    
    /**
     * Module Configuration Form
     * 
     * */
     public function getContent()
	{
		$this->_html .= '<h2>' . $this->l('Atajoy').'</h2>';
		
        // On submit
        if (!empty($_POST) AND Tools::isSubmit('submitSave'))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error"><img src="'._PS_IMG_.'admin/forbbiden.gif" alt="nok" />&nbsp;'.$err.'</div>';
		} // end if
        
		$this->_displayForm();
		return $this->_html;
	}
    
    /**
     * Config Form Validation
     * 
     * */
     private function _postValidation()
	{
		// Check configuration values
		if (!Tools::getValue('username') OR !Validate::isCleanHtml(Tools::getValue('username')))
			$this->_postErrors[] = $this->l('username not valid');
            
		if (!Tools::getValue('password') OR !Validate::isCleanHtml(Tools::getValue('password')))
			$this->_postErrors[] = $this->l('password not valid');
		
	}
    
     /**
     * Store configuration
     * 
     * */
     private function _postProcess()
	{
		// Saving new configurations
		if (Configuration::updateValue('ATAJOY_USERNAME', Tools::getValue('username')) AND
			Configuration::updateValue('ATAJOY_PASSWORD', Tools::getValue('password')))
			  $this->_html .= $this->displayConfirmation($this->l('Settings updated')); // It's Ok
		else
			  $this->_html .= $this->displayErrors($this->l('Settings failed in updating')); // an Error occured
            
       // Clear shipping cost Cache
       Db::getInstance()->ExecuteS('TRUNCATE TABLE  '._DB_PREFIX_.'atajoy_cache');
            
	}

	/**
	** Hook Update carrier
	**/
	 public function hookupdateCarrier($params)
	{
	   global $cookie;
       
            
		if ((int)($params['id_carrier']) == (int)(Configuration::get('ATAJOY_CARRIER_ID')))
			Configuration::updateValue('ATAJOY_CARRIER_ID', (int)($params['carrier']->id));
            
	}
    
    /**
     * 
     * */
    public function hookInvoice($params)
     {
        
        $result = Db::getInstance()->ExecuteS('SELECT invoice FROM '. _DB_PREFIX_.'atajoy_order WHERE id_order="'.$params['id_order'].'" LIMIT 1');
        
        //if($result)
        echo '<fieldset style="width: 400px; float: right; margin: 0 0 20px 30px;">
	       <legend>اتاجوی</legend>
 
	       <div id="info" border:="" solid="" red="" 1px;"="">
	       <table>
	           <tbody>
                    <tr><td>کد رهگیری :</td> <td><b>'.$result[0]['invoice'].'</b></td></tr>
	           </tbody></table>
	       </div>
 
            </fieldset>';
        
     }
     
     /**
      * PDFInvoice
      * */
      public function hookPDFInvoice($pdf, $id)
     {
        /*$rahgiri = Db::getInstance()->getValue('
		SELECT invoice FROM '._DB_PREFIX_.'atajoy_order
		WHERE id_order = '.(int)($id).' LIMIT 1');

        
        $pdf->Ln(5);
	    $pdf->SetFont('Arial', 'B', 8);
	    $width = 165;
        $pdf->Cell($width, 0, ' کد رهگیری : ', 0, 0, 'R');
        $pdf->Cell(0, 0, $rahgiri, 0, 0, 'R');
        $pdf->Ln(4);*/
     }

    /**
     * Hook Cart
     * if Cart get empty clear shipping cost Cache
     * */
     public function hookCart($params)
	{
	   global $cart;
	   
	   if (!is_object($cart))
		return;
       
       if($cart->nbProducts() == 0) {
            Db::getInstance()->ExecuteS('DELETE FROM  '._DB_PREFIX_.'atajoy_cache 
                                        WHERE id_cart="'.$cart->id.'"');
       }
	   
    }
    
    /**
     * clear shipping cost Cache
     * */
    public function hooknewOrder($cart, $order, $customer, $currency, $orderStatus)
    {
       
            
        
       /* Db::getInstance()->ExecuteS('DELETE FROM  '._DB_PREFIX_.'atajoy_cache 
                                        WHERE id_cart="'.$cart->id.'"');*/
        
    }
    
    function hookRightColumn($params)
    {
		global $smarty, $link, $cookie;

        
        $temp  = __PS_BASE_URI__.'modules/'. $this->name.'/status.php';
        
        
        //echo $temp;
        $smarty->assign(array('form_url' => $temp));
		return $this->display(__FILE__, 'blockatajoy.tpl');
		
	}
	function hookLeftColumn($params)
	{
		return $this->hookRightColumn;
	}
	/**
	* Generate Hash string
    **/
	public function getOrderShippingCostHash($wsParams)
	{
		$paramHash = '';
		$productHash = '';
		foreach ($wsParams['products'] as $product)
		{
			if (!empty($productHash))
				$productHash .= '|';
			$productHash .= $product['id_product'].':'.$product['id_product_attribute'].':'.$product['cart_quantity'];
		}
		foreach ($wsParams as $k => $v)
			if ($k != 'products')
			$paramHash .= '/'.$v;
		return md5($productHash.$paramHash);
	}
    
    /**
     * Get cached cost from db
     * */
    public function getOrderShippingCostCache($id, $hash)
	{
		// Get Cache
		$row = Db::getInstance()->getValue('
		SELECT price FROM '._DB_PREFIX_.'atajoy_cache
		WHERE id_cart = '.(int)($id).'
		AND hash = "'.pSQL($hash).'"');
        
        return $row;
	}
    
    /**
     * Store shipping cost in db
     * */
    public function saveOrderShippingCostCache($id, $hash, $price)
	{
		
		Db::getInstance()->ExecuteS('INSERT INTO  '._DB_PREFIX_.'atajoy_cache (id_atajoy_cache, id_cart, hash, price)
                                     VALUES (NULL, "'.$id.'", "'.$hash.'","'.$price.'") 
                                     ON DUPLICATE KEY UPDATE price="'.$price.'",hash="'.$hash.'"' );
                                     
            
	}
    
    /**
     * Get shipping cost
     * */
	public function getOrderShippingCost($params, $shipping_cost)
	{
	   global $cart;
       if (!extension_loaded('soap'))
        return false;
       
        // Init var
		$address = new Address($params->id_address_delivery);
		if (!Validate::isLoadedObject($address))
		{
			// If address is not loaded, we take data from shipping estimator module (if installed)
			global $cookie;
			$address->id_state = $cookie->id_state;
			$address->postcode = $cookie->postcode;
		}
        
        $carrier = ($this->id_carrier != 0 )? $this->id_carrier : Configuration::get('PS_CARRIER_DEFAULT');
        
        
        // Webservices Params
		$wsParams = array(
			'id_cart' => $params->id,
			'id_address_delivery' => $params->id_address_delivery,
			'recipient_city' => $address->city,
            'shpping_type' => $carrier,
            'currency' => $params->id_currency,
			'products' => $params->getProducts()
		);
        
        // Get Hash
		$myHash = $this->getOrderShippingCostHash($wsParams);
  

       // Check cache
		$cache = $this->getOrderShippingCostCache($cart->id, $myHash);
            
        if((int)$cache > 0 OR (string)$cache == '0')
            return $cache;
        else{       //cache not found
            
            $newCity    = explode('-', $address->city);
            $cityId   =  (int)$newCity[1];
            
            if(!isset($cityId )){
                return 0;
            }
            
            ini_set("soap.wsdl_cache_enabled", "0");
            $soapClient = new SoapClient('http://www.atajoyvendor.ir/webservice/index/wsdl/');
            @$login = $soapClient->__call('login', array('username' => Configuration::get('ATAJOY_USERNAME'), 'password' => Configuration::get('ATAJOY_PASSWORD')));
            if(!$login)
                return false;
                
            if ($soapClient->pathExists($cityId )){
                
                $TotalWeight =  $cart->getTotalWeight();
                
                $totalPrice  =  $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
                $Currrency     = new Currency();
                $rial = $Currrency->getIdByIsoCode('IRR');
                $currentCurrency  = $Currrency->getCurrency($params->id_currency);
                $TotalPrice =  $totalPrice / $this->getCartCurrencyRate($rial, $cart->id);//Tools::convertPrice($totalPrice, $currentCurrency, $rial);
                
                 if( $TotalPrice == 0 OR $TotalWeight == 0 )
                     return false; 
                     
                $Res = $soapClient->getPostPrice($TotalWeight, $cityId , $TotalPrice );
                
                 if((int)$Res > 0){
                    $price = $this->getCartCurrencyRate($rial, $cart->id) * $Res;//Tools::convertPrice((float)$Res, $rial, $currentCurrency);
                    
                    // Store cost as cache
                    $this->saveOrderShippingCostCache($cart->id, $myHash, $price);
                    return $price;
                 }else{ //If an error occured return 0
		  	       $this->saveOrderShippingCostCache($cart->id, $myHash, 0);
			         return false;
		          }
                
            }// end path exist cond
            
            return false;
        }
        
		return false;
	}
	
    /**
     * */
	 public function getOrderShippingCostExternal($params)
	{
	   $this->getOrderShippingCost($params);
	}
    
    public function getCartCurrencyRate($id_currency_origin, $id_cart)
	{
	   
       
		$conversionRate = 1;
		$cart = new Cart($id_cart);
		if ($cart->id_currency != $id_currency_origin)
		{
			$currencyOrigin = new Currency((int)$id_currency_origin);
			$conversionRate /= $currencyOrigin->conversion_rate;
			$currencySelect = new Currency((int)$cart->id_currency);
			$conversionRate *= $currencySelect->conversion_rate;
		}
		return $conversionRate;
	}
    
    public function getStateId($cityId)
	{
	   return (int)$cityId/10000;
	}
    
    /**
     * Config form generator
     * */
     private function _displayForm()
	{
	   $this->_html .= '<style>
            input.fro_textbox {float: right; border-radius: 3px 3px 3px 3px; border: 1px solid rgb(187, 187, 187); font: 11px tahoma; padding: 2px 3px; text-align: right; color: rgb(85, 85, 85);}
            input.fro_textbox:focus {border: 1px solid #AA6537;}
            fieldset.fro_fset {width: 250px; border-radius: 5px 5px 5px 5px; font: 11px tahoma; border: 1px solid #839CA9; padding: 10px; direction:rtl;background: #FEFEFE;margin: 0 20px; box-shadow:0 -40px 35px #F8F8F8 inset}
            legend.fro_legend {font: 11px tahoma; color: #4A98B5; border: 1px solid;background: #F2F2F8;}
            label.fro_label {margin-right: 50px;float: none;}
            select.fro_select {border: 1px solid #BBBBBB;border-radius: 3px 3px 3px 3px;float: right;width: 125px;}
            fieldset#opt div { margin-right: 30px; }
            fieldset.fro_fset span { color:#CC6600;}
            form#frotel > div { margin-top: 30px; }
            </style>';
        
        // main legend
        $this->_html .= '<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" /> '.$this->l('Atajoy configuration').'</legend>';
        // 
        $this->_html .= '<div style="width: 630px; margin:0 20px;">
        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post" id="frotel">
            <div style="float: right;margin-bottom: 15px;"><fieldset class="fro_fset"><legend class="fro_legend">'.$this->l('Your account data') . '</legend>
	         <p>
                <label class="fro_label" for="username">' . $this->l('username') .'<span>*</span></label>
                <input class="fro_textbox" name="username" id="" value="' . Tools::safeOutput(Tools::getValue('username',Configuration::get('ATAJOY_USERNAME'))) . '" type="text">
             </p>
	         <p>
                <label class="fro_label" for="password">' . $this->l('password') .' <span>*</span></label>
	            <input class="fro_textbox" name="password" id="" value="' . Tools::safeOutput(Tools::getValue('password',Configuration::get('ATAJOY_PASSWORD'))) . '" type="text">
             </p>
            </fieldset></div>';
        //
        $this->_html .= '<input  class="button" name="submitSave" type="submit" value="'.$this->l('submit configures').'" style="margin: 150px 120px 20px 0; border-radius: 3px; border: 1px solid #cbcb99; padding: 4px 7px; box-shadow: 0 0 5px #eee;"></form></div>';
        //
        $this->_html .= '</fieldset><div class="clear">&nbsp;</div>';
        
        
    }
	
}


