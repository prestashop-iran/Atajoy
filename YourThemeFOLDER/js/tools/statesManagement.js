$(document).ready(function(){
	$('input[name*="city"]').after('<select name="city" id="city"><option value="0">لطفاً استان را انتخاب کنید</option></select>');
	$('input[name*="city"]').remove();
	$('select#id_country').change(function(){
		updateState();
		updateNeedIDNumber();
		updateZipCode();
	});
	updateState();
	updateNeedIDNumber();
	updateZipCode();
	
	if ($('select#id_country_invoice').length != 0)
	{
		$('select#id_country_invoice').change(function(){
			updateState('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode();
		});
		if ($('select#id_country_invoice:visible').length != 0)
		{
			updateState('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode('invoice');
		}
	}
    
    $('#id_country option[value=112]').attr('selected', 'selected');
    
    if($('#id_country').val() == 112){
            
                        $('#id_state').change(function() {
                             getCity($(this).find(':selected')[0].id);
                
                            //alert($('#id_state').val());
                        });
                     }// end if
                     
    $('#city').parents().map(function() { 
        if(this.tagName == 'FORM'){
            /*this.submit(function(){
                alert('Handler for .submit() called.');
                return false;
                }
                ); 
            */
            return this;
            }
        }).submit(function(){
            if($("select#city option:selected").val()){
                var nval = $("select#city option:selected").text()+'-'+$("select#city option:selected").val();
                $("select#city option:selected").val(nval);
            }
            
                });
                     
});

function updateState(suffix)
{
	$('select#id_state'+(suffix !== undefined ? '_'+suffix : '')+' option:not(:first-child)').remove();
	var states = countries[$('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val()];
	if(typeof(states) != 'undefined')
	{
		$(states).each(function (key, item){
			$('select#id_state'+(suffix !== undefined ? '_'+suffix : '')).append('<option id="'+item.iso+'" value="'+item.id+'"'+ (idSelectedCountry == item.id ? ' selected="selected"' : '') + '>'+item.name+'</option>');
		});
		
		$('p.id_state'+(suffix !== undefined ? '_'+suffix : '')+':hidden').slideDown('slow');
	}
	else
		$('p.id_state'+(suffix !== undefined ? '_'+suffix : '')).slideUp('fast');
}

function updateNeedIDNumber(suffix)
{
	var idCountry = parseInt($('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val());

	if ($.inArray(idCountry, countriesNeedIDNumber) >= 0)
		$('.dni'+(suffix !== undefined ? '_'+suffix : '')).slideDown('slow');
	else
		$('.dni'+(suffix !== undefined ? '_'+suffix : '')).slideUp('fast');
}

function updateZipCode(suffix)
{
	var idCountry = parseInt($('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val());
	
	if (countriesNeedZipCode[idCountry] != 0)
		$('.postcode'+(suffix !== undefined ? '_'+suffix : '')).slideDown('slow');
	else
		$('.postcode'+(suffix !== undefined ? '_'+suffix : '')).slideUp('fast');
}

/**
*
**/
//document.write('<div><span><select id="ostan" name="ostan" onchange="getCity(this.value)"><option value="">استان خود را انتخاب کنید</option><option value="21">تهران</option><option value="31">اصفهان</option><option value="71">فارس</option><option value="61">خوزستان</option><option value="15">مازندران</option><option value="13">گيلان</option><option value="51">خراسان رضوی</option><option value="41">آذربايجان شرقي</option><option value="44">آذربايجان غربي</option><option value="76">هرمزگان</option><option value="34">كرمان</option><option value="77">بوشهر</option><option value="35">يزد</option><option value="26">البرز</option><option value="17">گلستان</option><option value="25">قم</option><option value="54">سيستان و بلوچستان</option><option value="86">مركزي</option><option value="83">كرمانشاه</option><option value="81">همدان</option><option value="66">لرستان</option><option value="28">قزوين</option><option value="23">سمنان</option><option value="87">كردستان</option><option value="45">اردبيل</option><option value="24">زنجان</option><option value="38">چهارمحال و بختياري</option><option value="74">كهكيلويه وبويراحمد</option><option value="56">خراسان جنوبي</option><option value="58">خراسان شمالي</option><option value="84">ايلام</option></select></span>&nbsp; &nbsp<span id="shahrLayer"> <select name="shahr" id="shahr" onchange="if(this.value == 21 || this.value == 26)  showThirdBox(this.value);  else  hideThirdBox();"> <option> استان را انتخاب کنید </option></select>  </span>&nbsp;&nbsp;<span style="visibility:hidden" id="thirdBoxLayer"> <select name="thirdbox" id="thirdbox"></select></span></div>');

/*
function showThirdBox(value) {
    document.getElementById("thirdBoxLayer").style.visibility='visible'; 
    getThirdBox(value);
}

function hideThirdBox() {
    document.getElementById("thirdBoxLayer").style.visibility='hidden';
}
*/
/*function check(){
    if (document.getElementById('ostan').selectedIndex==0) {
        alert(' استان را انتخاب کنيد');
        document.getElementById('ostan').focus();
        return (false);
    }
    
    if (document.getElementById('shahr').selectedIndex==0) {
        alert(' شهر را انتخاب کنيد');
        document.getElementById('shahr').focus();
        return (false);
    }
    
    ostan = document.getElementById('ostan').options[document.getElementById('ostan').selectedIndex].value;
    if (ostan == 21) {
        if (document.getElementById('thirdbox').selectedIndex == 0) {
            alert('ناحیه دقیق محل اقامت خود در تهران یا کرج را انتخاب کنید');
            document.getElementById('thirdbox').focus();
            return false;
        }
    }
    
    return true;
}*/


function getCity(value) {
    var shahr = document.getElementById('city');
    //hideThirdBox();
    shahr.options.length = 0;

    //alert(value);
            if (value == 'TRN') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("آبعلی","210014");
                            
                            shahr.options[2]=new Option("باقر شهر","210015");
                            
                            shahr.options[3]=new Option("بومهن","210016");
                            
                            shahr.options[4]=new Option("پاکدشت","210017");
                            
                            shahr.options[5]=new Option("پردیس","210018");
                            
                            shahr.options[6]=new Option("پیشوا","210019");
                            
                            shahr.options[7]=new Option("تجریش","210020");
                            
                            shahr.options[8]=new Option("تهران","210021");
                                    shahr.options[8].style.color='red';
                            
                            shahr.options[9]=new Option("چهاردانگه","210022");
                            
                            shahr.options[10]=new Option("حسن آباد","210023");
                            
                            shahr.options[11]=new Option("دماوند","210024");
                            
                            shahr.options[12]=new Option("اسلامشهر","210010");
                            
                            shahr.options[13]=new Option("رودهن","210025");
                            
                            shahr.options[14]=new Option("آبسرد","210033");
                            
                            shahr.options[15]=new Option("ری","210026");
                            
                            shahr.options[16]=new Option("فشم","210027");
                            
                            shahr.options[17]=new Option("فیروزکوه","210028");
                            
                            shahr.options[18]=new Option("قرچک","210029");
                            
                            shahr.options[19]=new Option("کهریزک","210030");
                            
                            shahr.options[20]=new Option("لواسان","210031");
                            
                            shahr.options[21]=new Option("ورامین","210032");
                            
                    }
            if (value == 'IFH') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("شهرضا","310012");
                            
                            shahr.options[2]=new Option("نجف آباد","310029");
                            
                            shahr.options[3]=new Option("نطنز","310030");
                            
                            shahr.options[4]=new Option("زرین شهر","310031");
                            
                            shahr.options[5]=new Option("نائین","310028");
                            
                            shahr.options[6]=new Option("زیباشهر","310027");
                            
                            shahr.options[7]=new Option("دهاقان","310013");
                            
                            shahr.options[8]=new Option("داران","310014");
                            
                            shahr.options[9]=new Option("فریدونشهر","310015");
                            
                            shahr.options[10]=new Option("فلاورجان","310016");
                            
                            shahr.options[11]=new Option("قهدریجان","310017");
                            
                            shahr.options[12]=new Option("کاشان","310018");
                            
                            shahr.options[13]=new Option("قمصر","310019");
                            
                            shahr.options[14]=new Option("جوشقان و کامو","310020");
                            
                            shahr.options[15]=new Option("گلپایگان","310021");
                            
                            shahr.options[16]=new Option("زاینده رود","310022");
                            
                            shahr.options[17]=new Option("سده لنجان","310023");
                            
                            shahr.options[18]=new Option("فولادشهر","310024");
                            
                            shahr.options[19]=new Option("باغ بهارداران","310025");
                            
                            shahr.options[20]=new Option("مبارکه","310026");
                            
                            shahr.options[21]=new Option("آران و بیدگل","310001");
                            
                            shahr.options[22]=new Option("اردستان","310002");
                            
                            shahr.options[23]=new Option("اصفهان","310003");
                                    shahr.options[23].style.color='red';
                            
                            shahr.options[24]=new Option("شاهین شهر","310004");
                            
                            shahr.options[25]=new Option("میمه","310005");
                            
                            shahr.options[26]=new Option("تیران","310006");
                            
                            shahr.options[27]=new Option("شهرک رضوان","310007");
                            
                            shahr.options[28]=new Option("چادگان","310008");
                            
                            shahr.options[29]=new Option("خمینی شهر","310009");
                            
                            shahr.options[30]=new Option("خوانسار","310010");
                            
                            shahr.options[31]=new Option("سمیرم","310011");
                            
                    }
            if (value == 'FAS') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("شیراز","710032");
                                    shahr.options[1].style.color='red';
                            
                            shahr.options[2]=new Option("اردکان","710001");
                            
                            shahr.options[3]=new Option("ارسنجان","710002");
                            
                            shahr.options[4]=new Option("استهبان","710003");
                            
                            shahr.options[5]=new Option("اقلید","710004");
                            
                            shahr.options[6]=new Option("اوز","710005");
                            
                            shahr.options[7]=new Option("آباده","710006");
                            
                            shahr.options[8]=new Option("بوانات","710007");
                            
                            shahr.options[9]=new Option("جهرم","710008");
                            
                            shahr.options[10]=new Option("حاجی آباد","710009");
                            
                            shahr.options[11]=new Option("خنج","710010");
                            
                            shahr.options[12]=new Option("خور","710011");
                            
                            shahr.options[13]=new Option("داراب","710012");
                            
                            shahr.options[14]=new Option("داریان","710013");
                            
                            shahr.options[15]=new Option("نی ریز","710030");
                            
                            shahr.options[16]=new Option("مهر","710031");
                            
                            shahr.options[17]=new Option("نور آباد","710029");
                            
                            shahr.options[18]=new Option("مرودشت","710028");
                            
                            shahr.options[19]=new Option("سروستان","710014");
                            
                            shahr.options[20]=new Option("سعادت شهر","710015");
                            
                            shahr.options[21]=new Option("سیدان","710016");
                            
                            shahr.options[22]=new Option("صفا شهر","710017");
                            
                            shahr.options[23]=new Option("فراشبند","710018");
                            
                            shahr.options[24]=new Option("فسا","710019");
                            
                            shahr.options[25]=new Option("فیروز آباد","710020");
                            
                            shahr.options[26]=new Option("قیر","710021");
                            
                            shahr.options[27]=new Option("کارزین","710022");
                            
                            shahr.options[28]=new Option("کازرون","710023");
                            
                            shahr.options[29]=new Option("گراش","710024");
                            
                            shahr.options[30]=new Option("گله دار","710025");
                            
                            shahr.options[31]=new Option("لار","710026");
                            
                            shahr.options[32]=new Option("لامرد","710027");
                            
                    }
            if (value == 'KZT') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("آبادان","610001");
                            
                            shahr.options[2]=new Option("امیدیه","610002");
                            
                            shahr.options[3]=new Option("اندیمشک","610003");
                            
                            shahr.options[4]=new Option("اهواز","610004");
                                    shahr.options[4].style.color='red';
                            
                            shahr.options[5]=new Option("حمیدیه","610005");
                            
                            shahr.options[6]=new Option("ایذه","610006");
                            
                            shahr.options[7]=new Option("باغ ملک","610007");
                            
                            shahr.options[8]=new Option("بهبهان","610008");
                            
                            shahr.options[9]=new Option("آغاجاری","610009");
                            
                            shahr.options[10]=new Option("گتوند","610026");
                            
                            shahr.options[11]=new Option("مسجد سلیمان","610027");
                            
                            shahr.options[12]=new Option("قلعه خواجه","610028");
                            
                            shahr.options[13]=new Option("لالی","610029");
                            
                            shahr.options[14]=new Option("هندیجان","610030");
                            
                            shahr.options[15]=new Option("شوشتر","610025");
                            
                            shahr.options[16]=new Option("شوش","610024");
                            
                            shahr.options[17]=new Option("بندر ماهشهر","610010");
                            
                            shahr.options[18]=new Option("چمران","610011");
                            
                            shahr.options[19]=new Option("بندر امام خمینی","610012");
                            
                            shahr.options[20]=new Option("خرمشهر","610013");
                            
                            shahr.options[21]=new Option("مینوشهر","610014");
                            
                            shahr.options[22]=new Option("دزفول","610015");
                            
                            shahr.options[23]=new Option("سوسنگرد","610016");
                            
                            shahr.options[24]=new Option("بستان","610017");
                            
                            shahr.options[25]=new Option("هویزه","610018");
                            
                            shahr.options[26]=new Option("رامهرمز","610019");
                            
                            shahr.options[27]=new Option("هفتگل","610020");
                            
                            shahr.options[28]=new Option("رامشیر","610021");
                            
                            shahr.options[29]=new Option("شادگان","610022");
                            
                            shahr.options[30]=new Option("دارخوین","610023");
                            
                    }
            if (value == 'MZD') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("ساری","150015");
                                    shahr.options[1].style.color='red';
                            
                            shahr.options[2]=new Option("سلمانشهر","150016");
                            
                            shahr.options[3]=new Option("عباس آباد","150017");
                            
                            shahr.options[4]=new Option("فریدونکنار","150018");
                            
                            shahr.options[5]=new Option("قایم شهر","150019");
                            
                            shahr.options[6]=new Option("کلار آباد","150020");
                            
                            shahr.options[7]=new Option("کلاردشت","150021");
                            
                            shahr.options[8]=new Option("کیاسر","150022");
                            
                            shahr.options[9]=new Option("گلوگاه","150023");
                            
                            shahr.options[10]=new Option("محمود آباد","150024");
                            
                            shahr.options[11]=new Option("مرزن آباد","150025");
                            
                            shahr.options[12]=new Option("نشتارود","150026");
                            
                            shahr.options[13]=new Option("نکا","150027");
                            
                            shahr.options[14]=new Option("نور","150028");
                            
                            shahr.options[15]=new Option("رویان","150014");
                            
                            shahr.options[16]=new Option("رامسر","150013");
                            
                            shahr.options[17]=new Option("آلاشت","150001");
                            
                            shahr.options[18]=new Option("آمل","150002");
                            
                            shahr.options[19]=new Option("ایزد شهر","150003");
                            
                            shahr.options[20]=new Option("بابل","150004");
                            
                            shahr.options[21]=new Option("بابلسر","150005");
                            
                            shahr.options[22]=new Option("بلده","150006");
                            
                            shahr.options[23]=new Option("بهشهر","150007");
                            
                            shahr.options[24]=new Option("تنکابن","150008");
                            
                            shahr.options[25]=new Option("جویبار","150009");
                            
                            shahr.options[26]=new Option("چالوس","150010");
                            
                            shahr.options[27]=new Option("چمستان","150011");
                            
                            shahr.options[28]=new Option("خرم آباد","150012");
                            
                            shahr.options[29]=new Option("نوشهر","150029");
                            
                    }
            if (value == 'GLN') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("آستارا","130001");
                            
                            shahr.options[2]=new Option("آستانه اشرفیه","130002");
                            
                            shahr.options[3]=new Option("بازار اسالم","130003");
                            
                            shahr.options[4]=new Option("املش","130004");
                            
                            shahr.options[5]=new Option("بره سر","130005");
                            
                            shahr.options[6]=new Option("بندر انزلی","130006");
                            
                            shahr.options[7]=new Option("توتکابن","130007");
                            
                            shahr.options[8]=new Option("چابکسر","130008");
                            
                            shahr.options[9]=new Option("خشکبیجار","130009");
                            
                            shahr.options[10]=new Option("خمام","130010");
                            
                            shahr.options[11]=new Option("دیلمان","130011");
                            
                            shahr.options[12]=new Option("رحیم آباد","130012");
                            
                            shahr.options[13]=new Option("رشت","130013");
                                    shahr.options[13].style.color='red';
                            
                            shahr.options[14]=new Option("رضوانشهر","130014");
                            
                            shahr.options[15]=new Option("رودبار","130015");
                            
                            shahr.options[16]=new Option("رودینه","130016");
                            
                            shahr.options[17]=new Option("هشتپر","130033");
                            
                            shahr.options[18]=new Option("منجیل","130032");
                            
                            shahr.options[19]=new Option("ماسوله","130031");
                            
                            shahr.options[20]=new Option("رودسر","130017");
                            
                            shahr.options[21]=new Option("سنگر","130018");
                            
                            shahr.options[22]=new Option("سیاهکل","130019");
                            
                            shahr.options[23]=new Option("شفت","130020");
                            
                            shahr.options[24]=new Option("صومعه سرا","130021");
                            
                            shahr.options[25]=new Option("فومن","130022");
                            
                            shahr.options[26]=new Option("کلاچای","130023");
                            
                            shahr.options[27]=new Option("کوچصفهان","130024");
                            
                            shahr.options[28]=new Option("کیاشهر","130025");
                            
                            shahr.options[29]=new Option("لاهیجان","130026");
                            
                            shahr.options[30]=new Option("لشت نشاء","130027");
                            
                            shahr.options[31]=new Option("لنگرود","130028");
                            
                            shahr.options[32]=new Option("لوشان","130029");
                            
                            shahr.options[33]=new Option("ماسال","130030");
                            
                            shahr.options[34]=new Option("تالش","130034");
                            
                    }
            if (value == 'KHR') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("بردسکن","510001");
                            
                            shahr.options[2]=new Option("تایباد","510002");
                            
                            shahr.options[3]=new Option("تربت جام","510003");
                            
                            shahr.options[4]=new Option("صالح آباد","510004");
                            
                            shahr.options[5]=new Option("نصرآباد","510005");
                            
                            shahr.options[6]=new Option("تربت حیدریه","510006");
                            
                            shahr.options[7]=new Option("فیض آباد","510007");
                            
                            shahr.options[8]=new Option("چناران","510008");
                            
                            shahr.options[9]=new Option("خواف","510009");
                            
                            shahr.options[10]=new Option("درگز","510010");
                            
                            shahr.options[11]=new Option("رشتخوار","510011");
                            
                            shahr.options[12]=new Option("فیروزه","510027");
                            
                            shahr.options[13]=new Option("نیشابور","510026");
                            
                            shahr.options[14]=new Option("سبزوار","510012");
                            
                            shahr.options[15]=new Option("سرخس","510013");
                            
                            shahr.options[16]=new Option("فریمان","510014");
                            
                            shahr.options[17]=new Option("قوچان","510015");
                            
                            shahr.options[18]=new Option("کاشمر","510016");
                            
                            shahr.options[19]=new Option("خلیل آباد","510017");
                            
                            shahr.options[20]=new Option("گناباد","510018");
                            
                            shahr.options[21]=new Option("بجستان","510019");
                            
                            shahr.options[22]=new Option("مشهد","510020");
                                    shahr.options[22].style.color='red';
                            
                            shahr.options[23]=new Option("ملک آباد","510021");
                            
                            shahr.options[24]=new Option("رضویه","510022");
                            
                            shahr.options[25]=new Option("طرقبه","510023");
                            
                            shahr.options[26]=new Option("شاندیز","510024");
                            
                            shahr.options[27]=new Option("کلات","510025");
                            
                    }
            if (value == 'AzS') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("اسکو","410003");
                            
                            shahr.options[2]=new Option("مرند","410018");
                            
                            shahr.options[3]=new Option("ملکان","410019");
                            
                            shahr.options[4]=new Option("میانه","410020");
                            
                            shahr.options[5]=new Option("ترکمانچای","410021");
                            
                            shahr.options[6]=new Option("ورزقان","410022");
                            
                            shahr.options[7]=new Option("هریس","410023");
                            
                            shahr.options[8]=new Option("هشترود","410024");
                            
                            shahr.options[9]=new Option("نظرکهریزی","410025");
                            
                            shahr.options[10]=new Option("مراغه","410017");
                            
                            shahr.options[11]=new Option("کلیبر","410016");
                            
                            shahr.options[12]=new Option("آذرشهر","410002");
                            
                            shahr.options[13]=new Option("ممقان","410001");
                            
                            shahr.options[14]=new Option("اهر","410004");
                            
                            shahr.options[15]=new Option("هوراند","410005");
                            
                            shahr.options[16]=new Option("بستان آباد","410006");
                            
                            shahr.options[17]=new Option("بناب","410007");
                            
                            shahr.options[18]=new Option("تبریز","410008");
                                    shahr.options[18].style.color='red';
                            
                            shahr.options[19]=new Option("جلفا","410009");
                            
                            shahr.options[20]=new Option("سراب","410010");
                            
                            shahr.options[21]=new Option("خامنه","410011");
                            
                            shahr.options[22]=new Option("شبستر","410012");
                            
                            shahr.options[23]=new Option("تسوج","410013");
                            
                            shahr.options[24]=new Option("صوفیان","410014");
                            
                            shahr.options[25]=new Option("عجب شیر","410015");
                            
                    }
            if (value == 'AzQ') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("چالدران","440018");
                            
                            shahr.options[2]=new Option("ارومیه","440001");
                                    shahr.options[2].style.color='red';
                            
                            shahr.options[3]=new Option("قوشچی","440002");
                            
                            shahr.options[4]=new Option("اشنویه","440003");
                            
                            shahr.options[5]=new Option("بوکان","440004");
                            
                            shahr.options[6]=new Option("پیرانشهر","440005");
                            
                            shahr.options[7]=new Option("تکاب","440006");
                            
                            shahr.options[8]=new Option("خوی","440007");
                            
                            shahr.options[9]=new Option("قره ضیاالدین","440008");
                            
                            shahr.options[10]=new Option("سردشت","440009");
                            
                            shahr.options[11]=new Option("سلماس","440010");
                            
                            shahr.options[12]=new Option("شاهین دژ","440011");
                            
                            shahr.options[13]=new Option("ماکو","440012");
                            
                            shahr.options[14]=new Option("بازرگان","440013");
                            
                            shahr.options[15]=new Option("پلدشت","440014");
                            
                            shahr.options[16]=new Option("مهاباد","440015");
                            
                            shahr.options[17]=new Option("میاندوآب","440016");
                            
                            shahr.options[18]=new Option("نقده","440017");
                            
                    }
            if (value == 'HMG') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("کیش","760013");
                            
                            shahr.options[2]=new Option("میناب","760014");
                            
                            shahr.options[3]=new Option("هرمز","760015");
                            
                            shahr.options[4]=new Option("ابوموسی","760001");
                            
                            shahr.options[5]=new Option("بستک","760002");
                            
                            shahr.options[6]=new Option("بندر جاسک","760003");
                            
                            shahr.options[7]=new Option("بندر عباس","760004");
                                    shahr.options[7].style.color='red';
                            
                            shahr.options[8]=new Option("بندر لنگه","760005");
                            
                            shahr.options[9]=new Option("چارک","760006");
                            
                            shahr.options[10]=new Option("حاجی آباد","760007");
                            
                            shahr.options[11]=new Option("خمیر","760008");
                            
                            shahr.options[12]=new Option("درگهان","760009");
                            
                            shahr.options[13]=new Option("دهبارز","760010");
                            
                            shahr.options[14]=new Option("سیریک","760011");
                            
                            shahr.options[15]=new Option("قشم","760012");
                            
                    }
            if (value == 'KRM') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("بافت","340003");
                            
                            shahr.options[2]=new Option("بردسیر","340004");
                            
                            shahr.options[3]=new Option("بم","340005");
                            
                            shahr.options[4]=new Option("بهرمان","340006");
                            
                            shahr.options[5]=new Option("جیرفت","340007");
                            
                            shahr.options[6]=new Option("راور","340008");
                            
                            shahr.options[7]=new Option("رفسنجان","340009");
                            
                            shahr.options[8]=new Option("رودبار","340010");
                            
                            shahr.options[9]=new Option("زرند","340011");
                            
                            shahr.options[10]=new Option("سیرجان","340012");
                            
                            shahr.options[11]=new Option("شهر بابک","340013");
                            
                            shahr.options[12]=new Option("عنبر آباد","340014");
                            
                            shahr.options[13]=new Option("قلعه گنج","340015");
                            
                            shahr.options[14]=new Option("کوهبنان","340016");
                            
                            shahr.options[15]=new Option("انار","340002");
                            
                            shahr.options[16]=new Option("اختیار آباد","340001");
                            
                            shahr.options[17]=new Option("کهنوج","340017");
                            
                            shahr.options[18]=new Option("کیانشهر","340018");
                            
                            shahr.options[19]=new Option("محمد آباد","340019");
                            
                            shahr.options[20]=new Option("کرمان","340026");
                                    shahr.options[20].style.color='red';
                            
                            shahr.options[21]=new Option("سرچشمه","340020");
                            
                            shahr.options[22]=new Option("ملوجان","340021");
                            
                            shahr.options[23]=new Option("نجف آباد","340022");
                            
                            shahr.options[24]=new Option("نودژ","340023");
                            
                            shahr.options[25]=new Option("هجدک","340024");
                            
                            shahr.options[26]=new Option("یزدان شهر","340025");
                            
                    }
            if (value == 'BSR') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("بوشهر","770001");
                                    shahr.options[1].style.color='red';
                            
                            shahr.options[2]=new Option("خارک","770002");
                            
                            shahr.options[3]=new Option("دلوار","770003");
                            
                            shahr.options[4]=new Option("جم","770004");
                            
                            shahr.options[5]=new Option("برازجان","770005");
                            
                            shahr.options[6]=new Option("کلمه","770006");
                            
                            shahr.options[7]=new Option("خورموج","770007");
                            
                            shahr.options[8]=new Option("بندردیر","770008");
                            
                            shahr.options[9]=new Option("بندر دیلم","770009");
                            
                            shahr.options[10]=new Option("بندر کنگان","770010");
                            
                            shahr.options[11]=new Option("عسلویه","770011");
                            
                            shahr.options[12]=new Option("بندر گناوه","770012");
                            
                            shahr.options[13]=new Option("بندر ریگ","770013");
                            
                    }
            if (value == 'YZD') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("یزد","350013");
                                    shahr.options[1].style.color='red';
                            
                            shahr.options[2]=new Option("اردکان","350001");
                            
                            shahr.options[3]=new Option("اشکذر","350002");
                            
                            shahr.options[4]=new Option("بافق","350003");
                            
                            shahr.options[5]=new Option("تفت","350004");
                            
                            shahr.options[6]=new Option("زارچ","350005");
                            
                            shahr.options[7]=new Option("طبس","350006");
                            
                            shahr.options[8]=new Option("مروست","350007");
                            
                            shahr.options[9]=new Option("مهریز","350008");
                            
                            shahr.options[10]=new Option("میبد","350009");
                            
                            shahr.options[11]=new Option("ندوشن","350010");
                            
                            shahr.options[12]=new Option("نیر","350011");
                            
                            shahr.options[13]=new Option("هرات","350012");
                            
                            shahr.options[14]=new Option("ابرکوه","350014");
                            
                    }
            if (value == 'ALB') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("اندیشه","260002");
                            
                            shahr.options[2]=new Option("اشتهارد","260001");
                            
                            shahr.options[3]=new Option("نظرآباد","260012");
                            
                            shahr.options[4]=new Option("هشتگرد","260013");
                            
                            shahr.options[5]=new Option("فردیس","260014");
                            
                            shahr.options[6]=new Option("مهرشهر","260015");
                            
                            shahr.options[7]=new Option("شهریار","260016");
                            
                            shahr.options[8]=new Option("ملارد","260011");
                            
                            shahr.options[9]=new Option("گلستان","260010");
                            
                            shahr.options[10]=new Option("صباشهر","260003");
                            
                            shahr.options[11]=new Option("رباط کریم","260004");
                            
                            shahr.options[12]=new Option("شاهد شهر","260005");
                            
                            shahr.options[13]=new Option("صفادشت","260006");
                            
                            shahr.options[14]=new Option("طالقان","260007");
                            
                            shahr.options[15]=new Option("قدس","260008");
                            
                            shahr.options[16]=new Option("کرج","260009");
                                    shahr.options[16].style.color='red';
                            
                    }
            if (value == 'GLT') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("اینچه برون","170001");
                            
                            shahr.options[2]=new Option("مینو دشت","170018");
                            
                            shahr.options[3]=new Option("مراوه تپه","170017");
                            
                            shahr.options[4]=new Option("گنبد کاووس","170016");
                            
                            shahr.options[5]=new Option("آزادشهر","170002");
                            
                            shahr.options[6]=new Option("آق قلا","170003");
                            
                            shahr.options[7]=new Option("بندر ترکمن","170004");
                            
                            shahr.options[8]=new Option("بندر گز","170005");
                            
                            shahr.options[9]=new Option("خان ببین","170006");
                            
                            shahr.options[10]=new Option("دلند","170007");
                            
                            shahr.options[11]=new Option("رامیان","170008");
                            
                            shahr.options[12]=new Option("سیمین شهر","170009");
                            
                            shahr.options[13]=new Option("علی آباد","170010");
                            
                            shahr.options[14]=new Option("کرد کوی","170011");
                            
                            shahr.options[15]=new Option("کلاله","170012");
                            
                            shahr.options[16]=new Option("گالیکش","170013");
                            
                            shahr.options[17]=new Option("گرگان","170014");
                                    shahr.options[17].style.color='red';
                            
                            shahr.options[18]=new Option("گمیش تپه","170015");
                            
                    }
            if (value == 'QOM') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("قنوات","250005");
                            
                            shahr.options[2]=new Option("کهک","250006");
                            
                            shahr.options[3]=new Option("جعفریه","250001");
                            
                            shahr.options[4]=new Option("دستجرد","250002");
                            
                            shahr.options[5]=new Option("سلفچگان","250003");
                            
                            shahr.options[6]=new Option("قم","250004");
                                    shahr.options[6].style.color='red';
                            
                    }
            if (value == 'SVB') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("سراوان","540010");
                            
                            shahr.options[2]=new Option("سوران","540011");
                            
                            shahr.options[3]=new Option("راسک","540012");
                            
                            shahr.options[4]=new Option("نیک شهر","540013");
                            
                            shahr.options[5]=new Option("نصرت آباد","540009");
                            
                            shahr.options[6]=new Option("میرجاوه","540008");
                            
                            shahr.options[7]=new Option("ایرانشهر","540001");
                            
                            shahr.options[8]=new Option("چابهار","540002");
                            
                            shahr.options[9]=new Option("کنارک","540003");
                            
                            shahr.options[10]=new Option("خاش","540004");
                            
                            shahr.options[11]=new Option("زابل","540005");
                            
                            shahr.options[12]=new Option("زهک","540006");
                            
                            shahr.options[13]=new Option("زاهدان","540007");
                                    shahr.options[13].style.color='red';
                            
                    }
            if (value == 'MKZ') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("آستانه","860001");
                            
                            shahr.options[2]=new Option("اراک","860002");
                                    shahr.options[2].style.color='red';
                            
                            shahr.options[3]=new Option("آشتیان","860003");
                            
                            shahr.options[4]=new Option("پرندک","860004");
                            
                            shahr.options[5]=new Option("تفرش","860005");
                            
                            shahr.options[6]=new Option("خمین","860006");
                            
                            shahr.options[7]=new Option("خنداب","860007");
                            
                            shahr.options[8]=new Option("دلیجان","860008");
                            
                            shahr.options[9]=new Option("ساوه","860009");
                            
                            shahr.options[10]=new Option("شازند","860010");
                            
                            shahr.options[11]=new Option("کمیجان","860011");
                            
                            shahr.options[12]=new Option("محلات","860012");
                            
                    }
            if (value == 'KMS') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("کرمانشاه","830011");
                                    shahr.options[1].style.color='red';
                            
                            shahr.options[2]=new Option("کرند غرب","830012");
                            
                            shahr.options[3]=new Option("کنگاور","830013");
                            
                            shahr.options[4]=new Option("گیلانغرب","830014");
                            
                            shahr.options[5]=new Option("هرسین","830015");
                            
                            shahr.options[6]=new Option("قصر شیرین","830010");
                            
                            shahr.options[7]=new Option("صحنه","830009");
                            
                            shahr.options[8]=new Option("اسلام آباد غرب","830001");
                            
                            shahr.options[9]=new Option("پاوه","830002");
                            
                            shahr.options[10]=new Option("تازه آباد","830003");
                            
                            shahr.options[11]=new Option("جوانرود","830004");
                            
                            shahr.options[12]=new Option("روانسر","830005");
                            
                            shahr.options[13]=new Option("سر پل ذهاب","830006");
                            
                            shahr.options[14]=new Option("سنقر","830007");
                            
                            shahr.options[15]=new Option("سومار","830008");
                            
                    }
            if (value == 'HMD') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("همدان","810010");
                                    shahr.options[1].style.color='red';
                            
                            shahr.options[2]=new Option("کبودرآهنگ","810007");
                            
                            shahr.options[3]=new Option("ملایر","810008");
                            
                            shahr.options[4]=new Option("نهاوند","810009");
                            
                            shahr.options[5]=new Option("قهاوند","810006");
                            
                            shahr.options[6]=new Option("رزن","810005");
                            
                            shahr.options[7]=new Option("اسد آباد","810001");
                            
                            shahr.options[8]=new Option("بهار","810002");
                            
                            shahr.options[9]=new Option("تویسرکان","810003");
                            
                            shahr.options[10]=new Option("جورقان","810004");
                            
                    }
            if (value == 'LRT') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("ازنا","660001");
                            
                            shahr.options[2]=new Option("اشترینان","660002");
                            
                            shahr.options[3]=new Option("الشتر","660003");
                            
                            shahr.options[4]=new Option("الیگودرز","660004");
                            
                            shahr.options[5]=new Option("بروجرد","660005");
                            
                            shahr.options[6]=new Option("پلدختر","660006");
                            
                            shahr.options[7]=new Option("چالانچولان","660007");
                            
                            shahr.options[8]=new Option("چغلوندی","660008");
                            
                            shahr.options[9]=new Option("خرم آباد","660009");
                                    shahr.options[9].style.color='red';
                            
                            shahr.options[10]=new Option("دورود","660010");
                            
                            shahr.options[11]=new Option("زاغه","660011");
                            
                            shahr.options[12]=new Option("سپید دشت","660012");
                            
                            shahr.options[13]=new Option("سراب دوره","660013");
                            
                            shahr.options[14]=new Option("فیروز آباد","660014");
                            
                            shahr.options[15]=new Option("کوهدشت","660015");
                            
                            shahr.options[16]=new Option("شول آباد","660019");
                            
                            shahr.options[17]=new Option("معمولان","660016");
                            
                            shahr.options[18]=new Option("نور آباد","660017");
                            
                            shahr.options[19]=new Option("ویسیان","660018");
                            
                    }
            if (value == 'QZV') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("قزوین","280010");
                                    shahr.options[1].style.color='red';
                            
                            shahr.options[2]=new Option("اقبالیه","280001");
                            
                            shahr.options[3]=new Option("الوند","280002");
                            
                            shahr.options[4]=new Option("آبگرم","280003");
                            
                            shahr.options[5]=new Option("آبیک","280004");
                            
                            shahr.options[6]=new Option("آوج","280005");
                            
                            shahr.options[7]=new Option("بوئین زهرا","280006");
                            
                            shahr.options[8]=new Option("تاکستان","280007");
                            
                            shahr.options[9]=new Option("خرمدشت","280008");
                            
                            shahr.options[10]=new Option("ضیا آباد","280009");
                            
                    }
            if (value == 'SMN') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("مهدی شهر","230004");
                            
                            shahr.options[2]=new Option("شهمیرزاد","230005");
                            
                            shahr.options[3]=new Option("شاهرود","230006");
                            
                            shahr.options[4]=new Option("بسطام","230007");
                            
                            shahr.options[5]=new Option("میامی","230008");
                            
                            shahr.options[6]=new Option("گرمسار","230009");
                            
                            shahr.options[7]=new Option("آرادان","230010");
                            
                            shahr.options[8]=new Option("ایوانکی","230011");
                            
                            shahr.options[9]=new Option("دامغان","230001");
                            
                            shahr.options[10]=new Option("سمنان","230002");
                                    shahr.options[10].style.color='red';
                            
                            shahr.options[11]=new Option("سرخه","230003");
                            
                    }
            if (value == 'KDT') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("بانه","870001");
                            
                            shahr.options[2]=new Option("بیجار","870002");
                            
                            shahr.options[3]=new Option("دزج","870003");
                            
                            shahr.options[4]=new Option("دلبران","870004");
                            
                            shahr.options[5]=new Option("دهگلان","870005");
                            
                            shahr.options[6]=new Option("دیواندره","870006");
                            
                            shahr.options[7]=new Option("سرو آباد","870007");
                            
                            shahr.options[8]=new Option("سقز","870008");
                            
                            shahr.options[9]=new Option("سنندج","870009");
                                    shahr.options[9].style.color='red';
                            
                            shahr.options[10]=new Option("صاحب","870010");
                            
                            shahr.options[11]=new Option("قروه","870011");
                            
                            shahr.options[12]=new Option("کامیاران","870012");
                            
                            shahr.options[13]=new Option("مریوان","870013");
                            
                    }
            if (value == 'ADB') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("مشکین شهر","450009");
                            
                            shahr.options[2]=new Option("رضی","450010");
                            
                            shahr.options[3]=new Option("نمین","450011");
                            
                            shahr.options[4]=new Option("نیر","450012");
                            
                            shahr.options[5]=new Option("گرمی","450008");
                            
                            shahr.options[6]=new Option("گیوی","450007");
                            
                            shahr.options[7]=new Option("اردبیل","450001");
                                    shahr.options[7].style.color='red';
                            
                            shahr.options[8]=new Option("سرعین","450002");
                            
                            shahr.options[9]=new Option("بیله سوار","450003");
                            
                            shahr.options[10]=new Option("پارس آباد","450004");
                            
                            shahr.options[11]=new Option("اصلان دوز","450005");
                            
                            shahr.options[12]=new Option("خلخال","450006");
                            
                    }
            if (value == 'ZNJ') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("طارم","240008");
                            
                            shahr.options[2]=new Option("ایجرود","240009");
                            
                            shahr.options[3]=new Option("ابهر","240001");
                            
                            shahr.options[4]=new Option("زرین آباد","240002");
                            
                            shahr.options[5]=new Option("قیدار","240003");
                            
                            shahr.options[6]=new Option("سهرورد","240004");
                            
                            shahr.options[7]=new Option("خرمدره","240005");
                            
                            shahr.options[8]=new Option("زنجان","240006");
                                    shahr.options[8].style.color='red';
                            
                            shahr.options[9]=new Option("ماه نشان","240007");
                            
                    }
            if (value == 'CVB') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("اردل","380001");
                            
                            shahr.options[2]=new Option("بروجن","380002");
                            
                            shahr.options[3]=new Option("شهرکرد","380003");
                                    shahr.options[3].style.color='red';
                            
                            shahr.options[4]=new Option("فارسان","380004");
                            
                            shahr.options[5]=new Option("چلگرد","380005");
                            
                            shahr.options[6]=new Option("لردگان","380006");
                            
                    }
            if (value == 'KVB') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("باشت","740001");
                            
                            shahr.options[2]=new Option("تلگرد چرام","740002");
                            
                            shahr.options[3]=new Option("دوگنبدان","740003");
                            
                            shahr.options[4]=new Option("دهدشت","740004");
                            
                            shahr.options[5]=new Option("دیشموک","740005");
                            
                            shahr.options[6]=new Option("سی سخت","740006");
                            
                            shahr.options[7]=new Option("قلعه رئیسی","740007");
                            
                            shahr.options[8]=new Option("لیکک","740008");
                            
                            shahr.options[9]=new Option("یاسوج","740009");
                                    shahr.options[9].style.color='red';
                            
                            shahr.options[10]=new Option("گچساران","740010");
                            
                    }
            if (value == 'KHJ') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("اسفدن","560005");
                            
                            shahr.options[2]=new Option("حاجی آباد","560006");
                            
                            shahr.options[3]=new Option("سرایان","560007");
                            
                            shahr.options[4]=new Option("اسلامیه","560008");
                            
                            shahr.options[5]=new Option("فردوس","560009");
                            
                            shahr.options[6]=new Option("بشرویه","560010");
                            
                            shahr.options[7]=new Option("قائن","560004");
                            
                            shahr.options[8]=new Option("نهبندان","560003");
                            
                            shahr.options[9]=new Option("بیرجند","560001");
                                    shahr.options[9].style.color='red';
                            
                            shahr.options[10]=new Option("سربیشه","560002");
                            
                    }
            if (value == 'KHS') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("جاجرم","580009");
                            
                            shahr.options[2]=new Option("راز","580010");
                            
                            shahr.options[3]=new Option("بجنورد","580001");
                                    shahr.options[3].style.color='red';
                            
                            shahr.options[4]=new Option("گرمه","580002");
                            
                            shahr.options[5]=new Option("شیروان","580003");
                            
                            shahr.options[6]=new Option("اسفراین","580004");
                            
                            shahr.options[7]=new Option("آشخانه","580005");
                            
                            shahr.options[8]=new Option("قاضی","580006");
                            
                            shahr.options[9]=new Option("پیش قلعه","580007");
                            
                            shahr.options[10]=new Option("فاروج","580008");
                            
                    }
            if (value == 'ILM') {
            shahr.options[0]=new Option("شهر خود را انتخاب کنید","");
                            shahr.options[1]=new Option("آبدانان","840001");
                            
                            shahr.options[2]=new Option("ایلام","840002");
                                    shahr.options[2].style.color='red';
                            
                            shahr.options[3]=new Option("ایوان","840003");
                            
                            shahr.options[4]=new Option("دره شهر","840004");
                            
                            shahr.options[5]=new Option("دهلران","840005");
                            
                            shahr.options[6]=new Option("سرابله","840006");
                            
                            shahr.options[7]=new Option("مهران","840007");
                            
                    }
        
}