{*
** Compatibility code for Prestashop older than 1.4.2 using a recent theme
** Ignore list isn't require here
** $address exist in every PrestaShop version
*}

{* Will be deleted for 1.5 version and more *}
{* If ordered_adr_fields doesn't exist, it's a PrestaShop older than 1.4.2 *}
{if !isset($dlv_all_fields)}
		{$dlv_all_fields.0 = 'company'}
		{$dlv_all_fields.1 = 'firstname'}
		{$dlv_all_fields.2 = 'lastname'}
		{$dlv_all_fields.3 = 'address1'}
		{$dlv_all_fields.4 = 'address2'}
		{$dlv_all_fields.5 = 'postcode'}
		{$dlv_all_fields.6 = 'city'}
		{$dlv_all_fields.7 = 'country'}
		{$dlv_all_fields.8 = 'state'}
{/if}

<div id="opc_new_account" class="opc-main-block">
	<div id="opc_new_account-overlay" class="opc-overlay" style="display: none;"></div>
	<h2>1. {l s='Account'}</h2>
	<form action="{$link->getPageLink('authentication.php', true)}?back=order-opc.php" method="post" id="login_form" class="std">
		<fieldset>
			<h3>{l s='Already registered?'} <a href="#" id="openLoginFormBlock">{l s='Click here'}</a></h3>
			<div id="login_form_content" style="display:none;">
				<!-- Error return block -->
				<div id="opc_login_errors" class="error" style="display:none;"></div>
				<!-- END Error return block -->
				<div style="margin-left:40px;margin-bottom:5px;float:left;width:40%;">
					<label for="login_email">{l s='E-mail address'}</label>
					<span><input type="text" id="login_email" name="email" /></span>
				</div>
				<div style="margin-left:40px;margin-bottom:5px;float:left;width:40%;">
					<label for="passwd">{l s='Password'}</label>
					<span><input type="password" id="login_passwd" name="passwd" /></span>
				</div>
				<p class="submit">
					{if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
					<input type="submit" id="SubmitLogin" name="SubmitLogin" class="button" value="{l s='Log in'}" />
				</p>
				<p class="lost_password"><a href="{$link->getPageLink('password.php', true)}">{l s='Forgot your password?'}</a></p>
			</div>
		</fieldset>
	</form>
	<form action="#" method="post" id="new_account_form" class="std">
		<fieldset>
			<h3 id="new_account_title">{l s='New Customer'}</h3>
			<div id="opc_account_choice">
				<div class="opc_float">
					<h4>{l s='Instant Checkout'}</h4>
					<p>
						<input type="button" class="exclusive_large" id="opc_guestCheckout" value="{l s='Guest checkout'}" />
					</p>
				</div>

				<div class="opc_float">
					<h4>{l s='Create your account today and enjoy:'}</h4>
					<ul class="bullet">
						<li>{l s='Personalized and secure access'}</li>
						<li>{l s='Fast and easy check out'}</li>
						<li>{l s='Separate billing and shipping addresses'}</li>
					</ul>
					<p>
						<input type="button" class="button_large" id="opc_createAccount" value="{l s='Create an account'}" />
					</p>
				</div>
				<div class="clear"></div>
			</div>
			<div id="opc_account_form">
				{$HOOK_CREATE_ACCOUNT_TOP}
				<script type="text/javascript">
				// <![CDATA[
				idSelectedCountry = {if isset($guestInformations) && $guestInformations.id_state}{$guestInformations.id_state|intval}{else}false{/if};
				{if isset($countries)}
					{foreach from=$countries item='country'}
						{if isset($country.states) && $country.contains_states}
							countries[{$country.id_country|intval}] = new Array();
							{foreach from=$country.states item='state' name='states'}
								countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state}', 'iso' : '{$state.iso_code}', 'name' : '{$state.name|escape:'htmlall':'UTF-8'}'{rdelim});
							{/foreach}
						{/if}
						{if $country.need_identification_number}
							countriesNeedIDNumber.push({$country.id_country|intval});
						{/if}
						{if isset($country.need_zip_code)}
							countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code};
						{/if}
					{/foreach}
				{/if}
				//]]>
				{if $vat_management}
					{literal}
					function vat_number()
					{
						if ($('#company').val() != '')
							$('#vat_number_block').show();
						else
							$('#vat_number_block').hide();
					}
					function vat_number_invoice()
					{
						if ($('#company_invoice').val() != '')
							$('#vat_number_block_invoice').show();
						else
							$('#vat_number_block_invoice').hide();
					}

					$(document).ready(function() {
						$('#company').blur(function(){
							vat_number();
						});
						$('#company_invoice').blur(function(){
							vat_number_invoice();
						});
						vat_number();
						vat_number_invoice();
					});
					{/literal}
				{/if}
				</script>
				<!-- Error return block -->
				<div id="opc_account_errors" class="error" style="display:none;"></div>
				<!-- END Error return block -->
				<!-- Account -->
				<input type="hidden" id="is_new_customer" name="is_new_customer" value="0" />
				<input type="hidden" id="opc_id_customer" name="opc_id_customer" value="{if isset($guestInformations) && $guestInformations.id_customer}{$guestInformations.id_customer}{else}0{/if}" />
				<input type="hidden" id="opc_id_address_delivery" name="opc_id_address_delivery" value="{if isset($guestInformations) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
				<input type="hidden" id="opc_id_address_invoice" name="opc_id_address_invoice" value="{if isset($guestInformations) && $guestInformations.id_address_delivery}{$guestInformations.id_address_delivery}{else}0{/if}" />
				<p class="required text">
					<label for="email">{l s='E-mail'}</label>
					<input type="text" class="text" id="email" name="email" value="{if isset($guestInformations) && $guestInformations.email}{$guestInformations.email}{/if}" />
					<sup>*</sup>
				</p>
				<p class="required password is_customer_param">
					<label for="passwd">{l s='Password'}</label>
					<input type="password" class="text" name="passwd" id="passwd" />
					<sup>*</sup>
					<span class="form_info">{l s='(5 characters min.)'}</span>
				</p>
				<p class="radio required">
					<span>{l s='Title'}</span>
					<input type="radio" name="id_gender" id="id_gender1" value="1" {if isset($guestInformations) && $guestInformations.id_gender == 1}checked="checked"{/if} />
					<label for="id_gender1" class="top">{l s='Mr.'}</label>
					<input type="radio" name="id_gender" id="id_gender2" value="2" {if isset($guestInformations) && $guestInformations.id_gender == 2}checked="checked"{/if} />
					<label for="id_gender2" class="top">{l s='Ms.'}</label>
				</p>
				<p class="required text">
					<label for="firstname">{l s='First name'}</label>
					<input type="text" class="text" id="customer_firstname" name="customer_firstname" onblur="$('#firstname').val($(this).val());" value="{if isset($guestInformations) && $guestInformations.customer_firstname}{$guestInformations.customer_firstname}{/if}" />
					<sup>*</sup>
				</p>
				<p class="required text">
					<label for="lastname">{l s='Last name'}</label>
					<input type="text" class="text" id="customer_lastname" name="customer_lastname" onblur="$('#lastname').val($(this).val());" value="{if isset($guestInformations) && $guestInformations.customer_lastname}{$guestInformations.customer_lastname}{/if}" />
					<sup>*</sup>
				</p>
				<p class="select">
					<span>{l s='Date of Birth'}</span>
					<select id="days" name="days">
						<option value="">-</option>
						{foreach from=$days item=day}
							<option value="{$day|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_day == $day)} selected="selected"{/if}>{$day|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
						{/foreach}
					</select>
					{*
						{l s='January'}
						{l s='February'}
						{l s='March'}
						{l s='April'}
						{l s='May'}
						{l s='June'}
						{l s='July'}
						{l s='August'}
						{l s='September'}
						{l s='October'}
						{l s='November'}
						{l s='December'}
					*}
					<select id="months" name="months">
						<option value="">-</option>
						{foreach from=$months key=k item=month}
							<option value="{$k|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_month == $k)} selected="selected"{/if}>{l s="$month"}&nbsp;</option>
						{/foreach}
					</select>
					<select id="years" name="years">
						<option value="">-</option>
						{foreach from=$years item=year}
							<option value="{$year|escape:'htmlall':'UTF-8'}" {if isset($guestInformations) && ($guestInformations.sl_year == $year)} selected="selected"{/if}>{$year|escape:'htmlall':'UTF-8'}&nbsp;&nbsp;</option>
						{/foreach}
					</select>
				</p>
				{if isset($newsletter) && $newsletter}
				<p class="checkbox">
					<input type="checkbox" name="newsletter" id="newsletter" value="1" {if isset($guestInformations) && $guestInformations.newsletter}checked="checked"{/if} />
					<label for="newsletter">{l s='Sign up for our newsletter'}</label>
				</p>
				<p class="checkbox" >
					<input type="checkbox"name="optin" id="optin" value="1" {if isset($guestInformations) && $guestInformations.optin}checked="checked"{/if} />
					<label for="optin">{l s='Receive special offers from our partners'}</label>
				</p>
				{/if}
				<h3>{l s='Delivery address'}</h3>
				{$stateExist = false}
				{foreach from=$dlv_all_fields item=field_name}
				{if $field_name eq "company"}
				<p class="text">
					<label for="company">{l s='Company'}</label>
					<input type="text" class="text" id="company" name="company" value="{if isset($guestInformations) && $guestInformations.company}{$guestInformations.company}{/if}" />
				</p>
				{elseif $field_name eq "firstname"}
				<p class="required text">
					<label for="firstname">{l s='First name'}</label>
					<input type="text" class="text" id="firstname" name="firstname" value="{if isset($guestInformations) && $guestInformations.firstname}{$guestInformations.firstname}{/if}" />
					<sup>*</sup>
				</p>
				{elseif $field_name eq "lastname"}
				<p class="required text">
					<label for="lastname">{l s='Last name'}</label>
					<input type="text" class="text" id="lastname" name="lastname" value="{if isset($guestInformations) && $guestInformations.lastname}{$guestInformations.lastname}{/if}" />
					<sup>*</sup>
				</p>
				{elseif $field_name eq "address1"}
				<p class="required text">
					<label for="address1">{l s='Address'}</label>
					<input type="text" class="text" name="address1" id="address1" value="{if isset($guestInformations) && $guestInformations.address1}{$guestInformations.address1}{/if}" />
					<sup>*</sup>
				</p>
				{elseif $field_name eq "address2"}
				<p class="text">
					<label for="address2">{l s='Address (Line 2)'}</label>
					<input type="text" class="text" name="address2" id="address2" value="" />
				</p>
				{elseif $field_name eq "postcode"}
				<p class="required postcode text">
					<label for="postcode">{l s='Zip / Postal code'}</label>
					<input type="text" class="text" name="postcode" id="postcode" value="{if isset($guestInformations) && $guestInformations.postcode}{$guestInformations.postcode}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
					<sup>*</sup>
				</p>
				{elseif $field_name eq "city"}
				<p class="required text">
					<label for="city">{l s='City'}</label>
					<input type="text" class="text" name="city" id="city" value="{if isset($guestInformations) && $guestInformations.city}{$guestInformations.city}{/if}" />
					<sup>*</sup>
				</p>
				{elseif $field_name eq "country" || $field_name eq "Country:name"}
				<p class="required select">
					<label for="id_country">{l s='Country'}</label>
					<select name="id_country" id="id_country">
						<option value="">-</option>
						{foreach from=$countries item=v}
						<option value="{$v.id_country}" {if (isset($guestInformations) AND $guestInformations.id_country == $v.id_country) OR (!isset($guestInformations) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
					<sup>*</sup>
				</p>
				{elseif $field_name eq "vat_number"}
				<div id="vat_number_block" style="display:none;">
					<p class="text">
						<label for="vat_number">{l s='VAT number'}</label>
						<input type="text" class="text" name="vat_number" id="vat_number" value="{if isset($guestInformations) && $guestInformations.vat_number}{$guestInformations.vat_number}{/if}" />
					</p>
				</div>
				{elseif $field_name eq "state" || $field_name eq 'State:name'}
				{$stateExist = true}
				<p class="required id_state select" style="display:none;">
					<label for="id_state">{l s='State'}</label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
					<sup>*</sup>
				</p>
				{/if}
				{/foreach}
				<p class="required text dni">
					<label for="dni">{l s='Identification number'}</label>
					<input type="text" class="text" name="dni" id="dni" value="{if isset($guestInformations) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
					<span class="form_info">{l s='DNI / NIF / NIE'}</span>
					<sup>*</sup>
				</p>
				{if !$stateExist}
				<p class="required id_state select">
					<label for="id_state">{l s='State'}</label>
					<select name="id_state" id="id_state">
						<option value="">-</option>
					</select>
					<sup>*</sup>
				</p>
				{/if}
				<p class="textarea is_customer_param">
					<label for="other">{l s='Additional information'}</label>
					<textarea name="other" id="other" cols="26" rows="3"></textarea>
				</p>
				<p class="text">
					<label for="phone">{l s='Home phone'}</label>
					<input type="text" class="text" name="phone" id="phone" value="{if isset($guestInformations) && $guestInformations.phone}{$guestInformations.phone}{/if}" /> <sup>*</sup>
				</p>
				<p class="text is_customer_param">
					<label for="phone_mobile">{l s='Mobile phone'}</label>
					<input type="text" class="text" name="phone_mobile" id="phone_mobile" value="" />
				</p>
				<input type="hidden" name="alias" id="alias" value="{l s='My address'}" />

				<p class="checkbox is_customer_param">
					<input type="checkbox" name="invoice_address" id="invoice_address" />
					<label for="invoice_address"><b>{l s='Use another address for invoice'}</b></label>
				</p>
			
				<div id="opc_invoice_address" class="is_customer_param">
					{assign var=stateExist value=false}
					<h3>{l s='Invoice address'}</h3>
					{foreach from=$inv_all_fields item=field_name}
					{if $field_name eq "company"}
					<p class="text is_customer_param">
						<label for="company_invoice">{l s='Company'}</label>
						<input type="text" class="text" id="company_invoice" name="company_invoice" value="" />
					</p>
					{elseif $field_name eq "vat_number"}
					<div id="vat_number_block_invoice" class="is_customer_param" style="display: none;">
						<p class="text">
							<label for="vat_number_invoice">{l s='VAT number'}</label>
							<input type="text" class="text" id="vat_number_invoice" name="vat_number_invoice" value="" />
						</p>
					</div>
					<p class="required text dni_invoice">
						<label for="dni">{l s='Identification number'}</label>
						<input type="text" class="text" name="dni_invoice" id="dni_invoice" value="{if isset($guestInformations) && $guestInformations.dni}{$guestInformations.dni}{/if}" />
						<span class="form_info">{l s='DNI / NIF / NIE'}</span>
						<sup>*</sup>
					</p>
					{elseif $field_name eq "firstname"}
					<p class="required text">
						<label for="firstname_invoice">{l s='First name'}</label>
						<input type="text" class="text" id="firstname_invoice" name="firstname_invoice" value="" />
						<sup>*</sup>
					</p>
					{elseif $field_name eq "lastname"}
					<p class="required text">
						<label for="lastname_invoice">{l s='Last name'}</label>
						<input type="text" class="text" id="lastname_invoice" name="lastname_invoice" value="" />
						<sup>*</sup>
					</p>
					{elseif $field_name eq "address1"}
					<p class="required text">
						<label for="address1_invoice">{l s='Address'}</label>
						<input type="text" class="text" name="address1_invoice" id="address1_invoice" value="" />
						<sup>*</sup>
					</p>
					{elseif $field_name eq "address2"}
					<p class="text is_customer_param">
						<label for="address2_invoice">{l s='Address (Line 2)'}</label>
						<input type="text" class="text" name="address2_invoice" id="address2_invoice" value="" />
					</p>
					{elseif $field_name eq "postcode"}
					<p class="required postcode text">
						<label for="postcode_invoice">{l s='Zip / Postal Code'}</label>
						<input type="text" class="text" name="postcode_invoice" id="postcode_invoice" value="" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
						<sup>*</sup>
					</p>
					{elseif $field_name eq "city"}
					<p class="required text">
						<label for="city_invoice">{l s='City'}</label>
						<input type="text" class="text" name="city_invoice" id="city_invoice" value="" />
						<sup>*</sup>
					</p>
					{elseif $field_name eq "country" || $field_name eq "Country:name"}
					<p class="required select">
						<label for="id_country_invoice">{l s='Country'}</label>
						<select name="id_country_invoice" id="id_country_invoice">
							<option value="">-</option>
							{foreach from=$countries item=v}
							<option value="{$v.id_country}" {if ($sl_country == $v.id_country)} selected="selected"{/if}>{$v.name|escape:'htmlall':'UTF-8'}</option>
							{/foreach}
						</select>
						<sup>*</sup>
					</p>
					{elseif $field_name eq "state" || $field_name eq 'State:name'}
					{$stateExist = true}
					<p class="required id_state_invoice select" style="display:none;">
						<label for="id_state_invoice">{l s='State'}</label>
						<select name="id_state_invoice" id="id_state_invoice">
							<option value="">-</option>
						</select>
						<sup>*</sup>
					</p>
					{/if}
					{/foreach}
					{if !$stateExist}
					<p class="required id_state_invoice select" style="display:none;">
						<label for="id_state_invoice">{l s='State'}</label>
						<select name="id_state_invoice" id="id_state_invoice">
							<option value="">-</option>
						</select>
						<sup>*</sup>
					</p>
					{/if}
					<p class="textarea is_customer_param">
						<label for="other_invoice">{l s='Additional information'}</label>
						<textarea name="other_invoice" id="other_invoice" cols="26" rows="3"></textarea>
					</p>
					<p class="text">
						<label for="phone_invoice">{l s='Home phone'}</label>
						<input type="text" class="text" name="phone_invoice" id="phone_invoice" value="" /> <sup>*</sup>
					</p>
					<p class="text is_customer_param">
						<label for="phone_mobile_invoice">{l s='Mobile phone'}</label>
						<input type="text" class="text" name="phone_mobile_invoice" id="phone_mobile_invoice" value="" />
					</p>
					<input type="hidden" name="alias_invoice" id="alias_invoice" value="{l s='My Invoice address'}" />
				</div>
				{$HOOK_CREATE_ACCOUNT_FORM}
				<p style="float: right;">
					<input type="submit" class="exclusive button" name="submitAccount" id="submitAccount" value="{l s='Save'}" />
				</p>
				<p style="float: right;color: green;display: none;" id="opc_account_saved">
					{l s='Account information saved successfully'}
				</p>
				<p style="clear: both;">
					<sup>*</sup>{l s='Required field'}
				</p>
				<!-- END Account -->
			</div>
		</fieldset>
	</form>
	<div class="clear"></div>
</div>