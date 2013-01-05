<?php
 
include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');



// handle requests

 if(Tools::getValue('rahgiri')){ // process data
    
    include(_PS_ROOT_DIR_.'/header.php');
    
    $rahgiri = Tools::stripslashes(Tools::getValue('rahgiri'));
    
    ini_set("soap.wsdl_cache_enabled", "0");
    $soapClient = new SoapClient('http://www.atajoyvendor.ir/webservice/index/wsdl/');
    @$login = $soapClient->__call('login', array('username' => Configuration::get('ATAJOY_USERNAME'), 'password' => Configuration::get('ATAJOY_PASSWORD')));
    
    @$myStat = $soapClient->getState($rahgiri);
    
    switch ($myStat['state']){ 
	case 'moallagh':
        $stat = 'معلق';
	break;

	case 'enserafi':
        $stat = 'انصرافی';
	break;

	case 'amadeBeErsal':
        $stat = 'آماده ارسال';
	break;
    
    case 'ersalShode':
        $stat = 'ارسال شده';
	break;
    
    case 'toziShode':
        $stat = 'توزیع شده';
	break;
    
    case 'vosolShode':
        $stat = 'وصول شده';
	break;
    
    case 'bargashti':
        $stat = 'برگشتی';
	break;
    
    default : $stat = 'خطایی رخ داده است، لطفن دوباره تلاش کنید';

}
    echo '<h1>وضعیت سفارش شما در سامانه ی اتاجوی</h1>';
    echo '<p>
    <br /> <span>کد رهگیری سفارش شما:</span><span style="margin:20px 40px 0 0;color:#e54c10; font: bold 16px ARIAL;display:block" class="bold">'.$rahgiri.'</span>
	<br/><br /> <span>وضعیت سفارش:</span><span style="margin:20px 40px 0 0;color:#e54c10; font: bold 16px ARIAL;display:block" class="bold">'.$stat.'</span>
</p>';
    
    
    include(_PS_ROOT_DIR_.'/footer.php');
	die ;
 }
 else
    { header('location:../../'); exit;}
    
