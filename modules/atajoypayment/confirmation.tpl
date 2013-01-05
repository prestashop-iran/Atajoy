

<p>{l s='Your order on' mod='cashondelivery'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='cashondelivery'}
	<br /><br />
	جهت هماهنگی برای دریافت کالا با شما تماس گرفته خواهد شد!
	<br /><br /><span class="bold">{l s='Your order will be sent very soon.' mod='cashondelivery'}</span>
    <br/><br /> <span>کد رهگیری سفارش شما:</span><span style="color:#e54c10; font: bold 16px ARIAL;display:block" class="bold">{$invoice}</span>
	<br />
    <br/>یک نسخه از اطلاعات این سفارش به ایمیل شما ارسال شده است، جهت هرگونه سؤال یا اطلاعات بیشتر، لطفاً با  <a href="{$link->getPageLink('contact-form.php', true)}">{l s='customer support' mod='cashondelivery'}</a>  تماس بگیرید .
</p>
