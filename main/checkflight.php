<?php   
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
	//include_once ("../class/nusoap.php");
	$user_id = $_SESSION[$conf->app.'_user_id'];
	function flightZarfiat($parvaz)
	{
		$conf = new conf;
		$out = 0;
		$se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
		$isAdmin = $se->detailAuth('all');
		if(!$isAdmin && $parvaz->getZarfiat($_SESSION[$conf->app.'_customer_id'])>=9)
			$out = 9;
		else if(!$isAdmin)
			$out = $parvaz->getZarfiat($_SESSION[$conf->app.'_customer_id']);
		else if($isAdmin)
			$out = $parvaz->getZarfiat();
		return($out);
	}
	function bargashtHast($selectedParvaz,$parvaz)
	{
                $out = TRUE;
                $jids = $parvaz->loadJid();
                if($parvaz->j_id >0 && $jids==null)
                {
                        $out = FALSE;
                        foreach($selectedParvaz as $tmp)
                                if($tmp->mabda_id == $parvaz->maghsad_id && $parvaz->mabda_id = $tmp->maghsad_id)
                                        $out = TRUE;
                }
                else if($parvaz->j_id >0 && $jids!=null)
                {
                        $out = FALSE;
                        foreach($selectedParvaz as $tmp)
                                for($i = 0;$i < count($jids);$i++)
                                        if($jids[$i] == $tmp->getId())
                                                $out = TRUE;
                }
                return($out);
	}
	function loadCity($inp)
        {
		$mysql = new mysql_class;
                $inp = (int)$inp;
                $out = "";
                $mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
                if(isset($q[0]))
                {
                        $out = $q[0]["name"];
                }
                return($out);
        }
	function loadSherkatName($inp)
        {
		$mysql = new mysql_class;
                $inp = (int)$inp;
                $out = "";
                $mysql->ex_sql("select `name` from `sherkat` where `id` = '$inp'",$q);
                if(isset($q[0]))
                {
                        $out = $q[0]["name"];
                }
                return($out);
        }
	function loadShomare($inp)
        {
                $inp = (int)$inp;
                $out = "";
                $par = new parvaz_det_class($inp);
                return(enToPerNums($par->shomare));
        }
	function hamed_pdate($str)
        {
                $out=jdate('l Y/n/j',strtotime($str));
                return enToPerNums($out);
        }
	function saat($inp)
        {
                $inp = substr($inp,0,-3);
                return enToPerNums($inp);
        }
	function poorsant($inp)
        {
		$conf = new conf;
                $par = new parvaz_det_class((int)$inp);
                $customer_id = $_SESSION[$conf->app."_customer_id"];
                $cust = new customer_class($customer_id);
                $out = ($cust->getPoorsant($inp)* ($par->ghimat) /100 );
                return enToPerNums(monize($out));
        }
	function loadMabda($inp)
        {
                $inp = (int)$inp;
                $out = "";
                $par = new parvaz_det_class($inp);
                return(loadCity($par->mabda_id));
        }
        function loadMaghsad($inp)
        {
                $inp = (int)$inp;
                $out = "";
                $par = new parvaz_det_class($inp);
                return(loadCity($par->maghsad_id));
        }

	function loadSherkat($inp)
        {
                $inp = (int)$inp;
                $out = "";
                $par = new parvaz_det_class($inp);
                return(loadSherkatName($par->sherkat_id));
        }
	$msg = "";
	$out = "";
	$info_ticket = array();
	$redirect = '';
	//$grid = new jshowGrid_new("parvaz_det","grid1");
	if(!isset($_SESSION[$conf->app."_user_id"]) || !isset($_REQUEST["adl"]))
		die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">window.location = 'index.php';</script></body></html>");
	if((int)$_REQUEST["adl"] <= 0)
		die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('حداقل یک بزرگسال باید انتخاب شود');window.location='index.php';</script></body></html>");
        $adl = abs((int)$_REQUEST["adl"]);
        $chd = abs((int)$_REQUEST["chd"]);
        $inf = abs((int)$_REQUEST["inf"]);
        $ticket_type = (int)$_REQUEST["ticket_type"];
	$kharid_typ = ((isset($_REQUEST['kharid_typ']))?$_REQUEST['kharid_typ']:'');
	$selected_parvaz = $_REQUEST["selected_parvaz"];
        $tmp = explode(",",$selected_parvaz);
	foreach($tmp as $parvaz_id)
        {
        	$tmp_parvaz = new parvaz_det_class((int)$parvaz_id);
                $selectedParvaz[] = $tmp_parvaz;
        }
	$customer = new customer_class((int)$_SESSION[$conf->app."_customer_id"]);
	$isAdmin = $se->detailAuth('all');
	$tedad = $adl + $chd;
	$jam_ghimat = 0;
	$tedad_ok = TRUE;
	$sites_id=-1;
        foreach($tmp as $parvaz_id)
        {
	        $tmp_parvaz = new parvaz_det_class((int)$parvaz_id);
		if($sites_id<0)
			$sites_id = sites_class::loadByParvaz_det_id($tmp_parvaz->id);
                if(flightZarfiat($tmp_parvaz) < $tedad)
        	        $tedad_ok = FALSE;

/*		if($tmp_parvaz->getZarfiat($customer->getId())<$tedad)
			$tedad_ok = FALSE;
*/
                $jam_ghimat += ($tedad * $tmp_parvaz->ghimat);
                $jam_ghimat += ($inf * $tmp_parvaz->ghimat)/10;
        }
	$paravaz_tedad  = ((count($tmp)>0)?TRUE:FALSE);
	$domasire_ok = TRUE;
	if(count($tmp) == 1 && (int)$tmp[0] <=0)
		$paravaz_tedad = FALSE;
	foreach($selectedParvaz as $tmp)
	{
		$bar = bargashtHast($selectedParvaz,$tmp);
		$domasire_ok = $bar && $domasire_ok;
	}
	$customer_etebar_ok = TRUE;
	$customer_tedad_ok = TRUE;
	$customer_shomare_ok = TRUE;
	if(!$isAdmin && $customer->max_amount < $jam_ghimat )
		$customer_etebar_ok = FALSE;
	if($customer->max_ticket+1-$customer->min_ticket < $tedad)
		$customer_tedad_ok = FALSE;
//---------Flight Selctetion Problems------
	$msg = "";	
	if( !(isset($_REQUEST['mod']) && $_REQUEST['mod']=='save') && !$tedad_ok )           
		$msg = "Zarfiate parvaz kam ast";
	if(!$paravaz_tedad)
		$msg .= " parvaz entekhab nashode";
	if(!$domasire_ok && !$isAdmin)
		$msg .= " paravz domasire dorost entekhab nashode";
//-----------------------------------------
//---------Customer Problems---------------
	if(!$customer_etebar_ok && $kharid_typ=='etebari')
		$msg .= " اعتبار مشتری کافی نیست";
	if(!$customer_tedad_ok)
		$msg .= " سقف تعداد خرید مشتری کافی نیست";
	if(!$customer_shomare_ok)
		$msg .= " تعداد شماره تیکت مشتری کافی نیست";
	$out = "";
	$adults = "";
	$childs = "";
	$infants = "";
	$tmp_id = array();
	if(!isset($_REQUEST["mod"]))
	{
		if($msg == "")
		{
			$adl_ghimat = 0;
			$chd_ghimat = 0;
			$inf_ghimat = 0;
			$poorsant = 0;
			$radif = '۱';
			$k = 0;
			//var_dump($_SESSION);
			foreach($selectedParvaz as $tmp)
			{
				$timeout = 5;
				if($kharid_typ=='naghdi')
					$timeout = 15;
				
				$alaki = ticket_class::addTmp($tmp->getId(),$tedad,$timeout);
				$tmp->setZarfiat($tedad);
				$tmp_id[] = $alaki;
				$adl_ghimat += $tmp->ghimat;
				$chd_ghimat += $tmp->ghimat;
				$inf_ghimat += ($tmp->ghimat/10);
				$poorsant += $tmp->ghimat * ($customer->getPoorsant($tmp->getId())/100);
				$k++;
			}
			$res_tmp = new reserve_tmp_class($alaki);
			$time_out = strtotime($res_tmp->tarikh .' + 5 minute ') - strtotime(date("Y-m-d H:i:s"));
 			$time_out = audit_class::secondToMinute($time_out);
			$adl_ghimat = enToPerNums(monize($adl_ghimat));
			$chd_ghimat = enToPerNums(monize($chd_ghimat));
			$inf_ghimat = enToPerNums(monize($inf_ghimat));
			$poorsant = enToPerNums(monize($poorsant));
			if ($ticket_type==1)
				$e_ticket="<th colspan='1' >شماره تیکت</th>";
			else	
				$e_ticket="<th colspan='1'></th>";
			$adults = <<<adul
				<tr class="showgrid_row_odd">
                                        <th class="showgrid_row_td_reserve_reserve">ردیف</th>
					<th colspan='2' class="showgrid_row_td_reserve_reserve">بزرگسال</th>
					<th colspan='1' class="showgrid_row_td_reserve_reserve">جنسیت</th>
					$e_ticket
					<th class="showgrid_row_td_reserve_reserve">بهای فروش</th>
                                        <th class="showgrid_row_td_reserve_reserve">کمیسیون</th>
                                        <th class="showgrid_row_td_reserve_reserve">بهای خالص</th>
				</tr>
adul;
			for($i = 0;$i < $adl;$i++)
			{
				$khal = enToPerNums(monize(perToEnNums(umonize($adl_ghimat))-perToEnNums(umonize($poorsant))));
				if($ticket_type == 1)
				{
					$adults .= <<<tmp0
				<tr class="showgrid_row_even" >
					<td class="showgrid_row_td_reserve" >$radif</td>
					<td>نام‌و‌نام‌خانوادگی</td>
					<td style="width:auto;"><input type='text' name='adl_lname_$i' id='adl_lname_$i' class='inp' style="width:400px;"/></td>
					<td><select class='inp' name='adl_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
					<td class="showgrid_row_td_reserve"><input type='text' name='adl_shomare_$i' id='adl_shomare_$i' class='inp'  /></td>
					<td class="showgrid_row_td_reserve" readonly="readonly">$adl_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
				</tr>
tmp0;
				}
				else
				{
	                                $adults .= <<<tmp0
                                <tr class="showgrid_row_even" >
                                        <td class="showgrid_row_td_reserve" >$radif</td>
					<td>نام‌و‌نام‌خانوادگی</td>
                                        <td colspan="1" style="width:auto;"><input type='text' name='adl_lname_$i' id='adl_lname_$i' class='inp' style="width:400px;"/></td>
					<td><select class='inp' name='adl_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
<!--
	
                                        <td class="showgrid_row_td_reserve" >شماره تماس:</td>
                                        <td class="showgrid_row_td_reserve" ><input type='text' name='adl_tel_$i' id='adl_tel$i' class='inp'  /></td>
-->
					$e_ticket
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$adl_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
                                </tr>
tmp0;
	                                $radif = enToPerNums(perToEnNums($radif)+1);
				}
			}
			if($chd >0)
			{
				$childs = <<<chil
				<tr class="showgrid_row_odd" >
					<th colspan='8' class="showgrid_row_td_reserve" >کودک</th>
				</tr>
chil;
				for($i = 0;$i < $chd;$i++)
				{
					$khal = enToPerNums(monize(perToEnNums(umonize($chd_ghimat))-perToEnNums(umonize($poorsant))));
					if($ticket_type == 1)
					{
						$childs .= <<<tmp1
				<tr class="showgrid_row_even">
					<td class="showgrid_row_td_reserve" >$radif</td>
					<td >نام و نام‌خانوادگی:</td>
					<td  colspan='1'  class="showgrid_row_td_reserve" style='width:auto;text-align:right;' ><input type='text' name='chd_lname_$i' id='chd_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' name='chd_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
                                        <td class="showgrid_row_td_reserve" ><input type='text' name='chd_shomare_$i' id='chd_shomare_$i' class='inp'  /></td>
					<td class="showgrid_row_td_reserve" readonly="readonly">$chd_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
				</tr>
tmp1;
					}
					else
					{
                                                $childs .= <<<tmp1
                                <tr class="showgrid_row_even">
                                        <td class="showgrid_row_td_reserve" >$radif</td>
                                        <td>نام و نام‌خانوادگی:</td>
					<td  colspan='1'  class="showgrid_row_td_reserve" style='width:auto;text-align:right;' ><input type='text' name='chd_lname_$i' id='chd_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' name='chd_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
					$e_ticket
					<td class="showgrid_row_td_reserve" readonly="readonly">$chd_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
                                </tr>
tmp1;

					}
	                                $radif = enToPerNums(perToEnNums($radif)+1);
				}
			}
			if($inf > 0)
			{
				$infants = <<<infa
				<tr class="showgrid_row_odd" >
					<th colspan='8' class="showgrid_row_td_reserve_reserve" >نوزاد</th>
				</tr>
infa;
				for($i = 0;$i < $inf;$i++)
				{
					$khal = enToPerNums(monize(perToEnNums(umonize($inf_ghimat))-perToEnNums(umonize($poorsant))));
                                        if($ticket_type == 1)
                                        {
						$infants .= <<<tmp2
				<tr class="showgrid_row_even">
					<td class="showgrid_row_td_reserve" >$radif</td>
					<td >نام و نام‌خانوادگی:</td>
					<td  colspan='1'  class="showgrid_row_td_reserve" style='width:autp;text-align:right;' ><input type='text' name='inf_lname_$i' id='inf_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' name='inf_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
                                        <td class="showgrid_row_td_reserve" ><input type='text' name='inf_shomare_$i' id='inf_shomare_$i' class='inp'  /></td>
					<td class="showgrid_row_td_reserve" readonly="readonly">$inf_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
				</tr>
tmp2;
					}
					else
					{
                                                $infants .= <<<tmp2
                                <tr class="showgrid_row_even">
                                        <td class="showgrid_row_td_reserve" >$radif</td>
                                        <td>نام و نام‌خانوادگی:</td>
                                        <td  colspan='1'  class="showgrid_row_td_reserve" style='width:auto;text-align:right;' ><input type='text' name='inf_lname_$i' id='inf_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' name='inf_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
					$e_ticket
					<td class="showgrid_row_td_reserve" readonly="readonly">$inf_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
                                </tr>
tmp2;

					}
	                                $radif = enToPerNums(perToEnNums($radif)+1);
				}

			}
			$out = <<<OOUT
	<span style="color:firebrick;font-size:15px;" >
		دقت فرمایید رزرو موقت شما تنها ۵ دقیقه معتبر است
	</span>
		<input id="tim" style="color:#000000;width:70px;font-size:25px;" readonly="readonly" value="$time_out" />
		<script>
			var t = setTimeout("dec();",1000);
			function dec()
			{
				var tim = document.getElementById('tim');
				var noe = tim.value;
				var tmp = noe.split(':');
				var m = parseInt(tmp[0],10);
				var s = parseInt(tmp[1],10);
				if(s > 0)
				{
					s--;
					t = setTimeout("dec();",1000);
				}
				else if(m > 0)
				{
					s = 59;
					m--;
					if(m == 1)
						tim.style.color = "firebrick";
					t = setTimeout("dec();",1000);
				}
				else if(m==0 && s==0)
				{
					alert('پایان مهلت رزرو بلیت');
					closeDialog();
				}
				tim.value = m+":"+s;
			}
		</script>
	        <table style="border-style:solid;border-width:1px;border-color:Black;width:80%" border='1'>
$adults
$childs
$infants
			<tr class="showgrid_row_even">
				<td class="showgrid_row_td_reserve" colspan="2" >شماره تماس :</td>
                                <td class="showgrid_row_td_reserve" colspan="6" ><input type='text' name='adl_tel_0' id='adl_tel0' class='inp'  style="width:99%;" /></td>
			</tr>
			<tr class="showgrid_row_odd">
                                <td class="showgrid_row_td_reserve" colspan="2" >نشانی ایمیل:</td>
                                <td class="showgrid_row_td_reserve" colspan="6" ><input type='text' name='email_addr' id='email_addr' class='inp'  style="width:99%;" /></td>
                        </tr>
			<tr class="showgrid_row_even">
                                <td class="showgrid_row_td_reserve" colspan="8" ><a href="http://www.superparvaz.com/rules" target="_blank" >شرایط و ضوابط را قبول دارم</a>
				<input type='checkbox' name='zavabet' id='zavabet'  /></td>
                        </tr>
			<tr class="showgrid_row_odd">
				<td colspan = "8" class="showgrid_row_td_reserve" >
				<br/>
				<button onclick="sendTickets();">ثبت جهت رزرو موقت</button>
	                        <button onclick="rejectTickets();">انصراف</button>

			</tr>		
		</table>

OOUT;
		}
	}
	else if($_REQUEST["mod"] == "reject")
	{
		$tmp_id = explode(",",$_REQUEST["tmp_id"]);
               	$alaki = ticket_class::removeTmp($tmp_id);
		foreach($selectedParvaz as $tmp)
	                $tmp->resetZarfiat($tedad);
		die("<html><body><script language=\"javascript\"> window.location='index.php'; </script></body></html>");
	
	}
	
?>
<script type="text/javascript" >
var adl=<?php echo $adl; ?>;
var chd=<?php echo $chd; ?>;
var inf=<?php echo $inf; ?>;
var ticket_type=<?php echo $ticket_type; ?>;
var selected_parvaz='<?php echo $selected_parvaz; ?>';
var tmp_id = '<?php echo implode(",",$tmp_id); ?>';
var kharid_typ='<?php echo $kharid_typ; ?>';
var sites_id = '<?php echo $sites_id; ?>';
var mod;
	function sendTickets()
	{
		var inputs = document.getElementsByTagName('input');
		var ok = true;
		var telfound = false;
		var tmp = Array();
		var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
		if(!$("#zavabet").prop('checked'))
		{
			alert("لطفا شرایط و ضوابط را قبول فرمایید");
			return(false);
		}		
		if(!email_regex.test($("#email_addr").val()))
		{
			alert('نشانی ایمیل درست وارد نشده است');
			$("#email_addr").val('');
			return (false);
		}
		for(var i = 0;i < inputs.length;i++)
		{
			tmp = String(inputs[i].name).split('_');
			if(tmp[1] && tmp[1] == 'lname' && inputs[i].value == '')
				ok = false;
			if(tmp[1] && tmp[1] == 'tel' && inputs[i].value != '')
				telfound = true;
		}
		if(ok && telfound)
		{
			var req = {};
			$.each($(".inp"),function(id,field){
				var item = $(field);
				req[item.prop("name")] = item.val();
			});
			var email_addr = $("#email_addr").val();
			var re = $.param(req);
			re += "&selected_parvaz="+selected_parvaz+"&ticket_type="+ticket_type+"&adl="+adl+"&chd="+chd+"&inf="+inf+"&kharid_typ="+kharid_typ+"&tmp_id="+tmp_id+"&mod=save&email_addr="+email_addr+"&sites_id="+sites_id+"&";
			clearTimeout(t);
			closeDialog();
			//alert("checkflight2.php?"+re);
			//re = encodeURIComponent(re);
			openDialog("checkflight2.php?"+re,"مشاهده بلیت‌ها",{'minWidth':300,'minHeight':200},false);
		}
		else
			alert('نام خانوادگی و حداقل یک تلفن باید وارد شود');
	}
	function rejectTickets()
	{
		closeDialog();
	}
</script>

	<div align="center" >
		<?php
			
			if ($msg != "")
				echo "<script>alert('$msg');window.location='index.php';</script>";
			else
				echo $out;
		?>
	</div>