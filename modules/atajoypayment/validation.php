<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7734 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/atajoypayment.php');

$cashOnDelivery = new AtajoyPayment();
if ($cart->id_customer == 0 OR $cart->id_address_delivery == 0 OR $cart->id_address_invoice == 0 OR !$cashOnDelivery->active)
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

// Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
$authorized = false;
foreach (Module::getPaymentModules() as $module)
	if ($module['name'] == 'atajoypayment')
	{
		$authorized = true;
		break;
	}
if (!$authorized)
	die(Tools::displayError('This payment method is not available.'));
	
$customer = new Customer((int)$cart->id_customer);

if (!Validate::isLoadedObject($customer))
	Tools::redirectLink(__PS_BASE_URI__.'order.php?step=1');

/* Validate order */
if (Tools::getValue('confirm'))
{
	$customer = new Customer((int)$cart->id_customer);
	$total = $cart->getOrderTotal(true, Cart::BOTH);
    
    ///
        @$soapClient = new SoapClient('http://www.atajoyvendor.ir/webservice/index/wsdl/');
        @$login = $soapClient->__call('login', array('username' => Configuration::get('ATAJOY_USERNAME'), 'password' => Configuration::get('ATAJOY_PASSWORD')));
        
        $address = new Address($cart->id_address_delivery);
        
        $Name       = $address->firstname;
        $Family     = $address->lastname;
        $Telephone  = $address->phone;
        $Cellphone  = $address->phone_mobile;
        $Email      = $customer->email;
        $PCode      = $address->postcode;
        $AAddress   = $address->address1.'-'.$address->address2 ;
    
        $message    = new MessageCore();
        $my_mes     = $message->getMessageByCartId($cart->id);
        $Message    = $address->other.' / '.$my_mes['message'];
        
        $newCity    = explode('-', $address->city);
        $cityId   =  (int)$newCity[1];
        
        $State      = (int)($cityId/10000);
        $City       = $cityId;
        
        $PlaceType  = 'mskoni';
        
        $Currrency          = new Currency();
        $rial               = $Currrency->getIdByIsoCode('IRR');
        $currentCurrency    = $Currrency->getCurrency($cart->id_currency);

        
        $myCart = $cart->getSummaryDetails();
        foreach ($myCart['products'] as $product) {
            
	        $temp  = $product['id_product'].'^';
            $temp .= ($product['price_wt'])/getCartCurrencyRate($rial, $cart->id) .'^';
            $temp .= $product['weight'].'^'; 
            $temp .= $product['quantity'].'^';
            $temp .= $product['name'];
            $RequestList[] = $temp;
            $temp = '';
        }
       //registerOrder('Mohsen' , 'Safari' , '456123987', '6955441', '3365214', 'safari.tafreshi@gmail.com' , '135974125', 'tehran kh emam hossein' , 'لطفا سریع ارسال کنید', 21, 210021, $orderString, 'maskoni');
				
        $OrderList      =  implode(';', $RequestList);
        

        //$orderString = '5158451^38000^48^3^test farsi;zo^99000^210^2^english book';
        if(isset($cookie->atajoy) AND (int)$cookie->atajoy == 1){
            @$lincode = $soapClient->registerOrder(
                                $Name , 
                                $Family , 
                                $Telephone, 
                                '', 
                                $Cellphone, 
                                $Email , 
                                $PCode, 
                                $AAddress , 
                                $Message, 
                                $State, 
                                $City, 
                                $OrderList, 
                                'maskoni');
            $cookie->__unset('atajoy');
        }
				
		
    
    if(!isset($lincode)){
        $stat = Configuration::get('ATAJOY_FAILED');
        $lincode = 'با فروشگاه تماس بگیرید';
    }else{
        $stat = Configuration::get('ATAJOY_SENT');
    }
    
	$cashOnDelivery->validateOrder((int)$cart->id, $stat, $total, $cashOnDelivery->displayName, NULL, array('{transaction_id}' => 'کدرهگیری سفارش شما : '.$lincode), NULL, false, $customer->secure_key);
	$order = new Order((int)$cashOnDelivery->currentOrder);
    
    
    Db::getInstance()->ExecuteS('INSERT INTO  '._DB_PREFIX_.'atajoy_order (id_order, invoice)
                                     VALUES ('.$order->id.', "'.$lincode.'") 
                                     ON DUPLICATE KEY UPDATE invoice="'.$lincode.'"' );
    
   
	Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)($cart->id).'&id_module='.(int)$cashOnDelivery->id.'&id_order='.(int)$cashOnDelivery->currentOrder);
}
else
{
	/* or ask for confirmation */ 
	$smarty->assign(array(
		'total' => $cart->getOrderTotal(true, Cart::BOTH),
		'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/atajoypayment/'
	));
    
    $cookie->__set('atajoy', '1');

	$smarty->assign('this_path', __PS_BASE_URI__.'modules/atajoypayment/');
	$template = 'validation.tpl';
	echo Module::display('atajoypayment', $template);
}
function getCartCurrencyRate($id_currency_origin, $id_cart)
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
include(dirname(__FILE__).'/../../footer.php');