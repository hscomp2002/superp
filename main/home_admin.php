<?php   include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
	if(!$se->can_view)
                die(lang_fa_class::access_deny);
        ticket_class::clearTickets();
	$GLOBALS['parvaz_id'] = -1;
	$GLOBALS['obj'] = array();
	function loadCities($smabda_id = -1)
	{
		$smabda_id = (int)$smabda_id;
		$out ="<option value=\"-1\">\nهمه\n</option>\n";
		$mysql = new mysql_class;
		$mysql->ex_sql("select * from `shahr` order by `name`",$q);
		foreach($q as $r)
		{
			$out .= "<option value=\"".(int)$r["id"]."\" ".(((int)$r["id"]==$smabda_id)?"selected=\"selected\"":"")." >\n";
			$out .= $r["name"]."\n";
			$out .= '</option>\n';
		}
		return($out);
	}
	function loadCityMab($inp)
	{
		$inp = (int)$inp;
		$mysql = new mysql_class;
		$mysql->ex_sql("select `mabda_id`,`maghsad_id`,`shomare`,`havapiema_id` from `parvaz` where `id`='$inp' ",$p);
		$GLOBALS['parvaz_id'] = $inp;
		$GLOBALS['obj'] = $p;
		$inp =-1;
		if(isset($p[0]))
			$inp =(int) $p[0]['mabda_id'];
		$out = '';
		$q = array();
		$mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
		return($out);
	}
	function loadCityMagh($inp)
	{
		$inp = (int)$inp;
		$out = '';
		if($GLOBALS['parvaz_id']==$inp && isset($GLOBALS['obj'][0]))
			$inp = $GLOBALS['obj'][0]['maghsad_id'];
		else
		{
			$parvaz = new parvaz_class($inp);
			$inp = $parvaz->maghsad_id;
		}
		$mysql = new mysql_class;
		$mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
		return($out);
	}
	function loadShomare($inp)
	{
		$inp = (int)$inp;
		if($GLOBALS['parvaz_id']==$inp && isset($GLOBALS['obj'][0]))
			$inp = $GLOBALS['obj'][0]['shomare'];
		else
		{
			$parvaz = new parvaz_class($inp);
			$inp = $parvaz->shomare;
		}
		return($inp);
	}
	function loadPlane($inp)
	{
		$inp = (int)$inp;
		if($GLOBALS['parvaz_id']==$inp && isset($GLOBALS['obj'][0]) )
			$inp = $GLOBALS['obj'][0]['havapiema_id'];
		else
		{
			$parvaz = new parvaz_class($inp);
			$inp = $parvaz->havapiema_id;
		}
		$mysql = new mysql_class;
		$out = '';
		$mysql->ex_sql("select `name` from `havapeima` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
		return($out);
	}
	function hamed_pdate($str)
        {
                $out=jdate('d / m ',strtotime($str));
		$out .= "<br/>".date('M d',strtotime($str));
                return ($out);
        }
	function hamed_pdate_day($str)
	{
		$out=jdate('y/m/d',strtotime($str));
                return ($out);
	}
	function saat($inp)
	{
		$inp = enToPerNums(substr($inp,0,-3));
		return ($inp);
	}
	function loadCheckBox($id)
	{
		$my = new mysql_class;
		$my->ex_sql("select `typ`,`tarikh`,`j_id`,`saat` from `parvaz_det` where `id`='$id' ",$q);
		$name='';
		$jIcon = '';
		if(isset($q[0]) && (int)$q[0]['j_id']==1)
		{
			$my->ex_sql("select `jid` from `parvaz_jid` where `parvaz_det_id`='$id' ",$p);
			$jid = array();
			foreach($p as $t)
				$jid[] =$t['jid'];
			$name =(count($jid)>0)?implode('-',$jid):0;
			$jIcon = '<br/><img src="../img/twoway.png" >';
		}
		$out = "<input type='checkbox' class='sel' name='$name' id='ch_$id'  >";
		if(isset($q[0]) && strtotime($q[0]['tarikh'].' '.$q[0]['saat'])<strtotime(date("Y-m-d H:i:s")))
			$out='';
		if(isset($q[0]) && (int)$q[0]['typ']==2)
			$out .= '<br/><img src="../img/tel.png" >';
		$out.=$jIcon;
		return $out;
	}
	function zarfiat($inp)
	{
		$inp = (int)$inp;
		if($inp>9)
			$inp = 9;
		$out = '<span style=\'cursor:pointer;\' >'.$inp.'</span>';
		if($inp==0)
			$out = '<span class=\'notice\' style=\'cursor:pointer;\' >CLOSED</span>';
		return ($out);
	}
	function loadType()
	{
		$out[0] = 'عمومی';
		$out[2] = 'تلفنی';
		return $out;
	}
	function loadDet($parvaz_det_id)
	{
		return '<div class=\'msg\' style="cursor:pointer;" onclick=\'loadDet('.$parvaz_det_id.');\'>ویرایش</div>';
	}
	function parvaz_detail($inp)
	{
		$inp = (int)$inp;
		$movaghat = 0;
		$mysql = new mysql_class;
		$mysql->ex_sql("select SUM(`tedad`) as `jam` from `reserve_tmp` where `parvaz_det_id`=$inp ",$qq);
		if(isset($qq[0]))
			$movaghat = (int) $qq[0]['jam'];
		$mysql->ex_sql("select `id` from `ticket` where `en`='1' and `adult`<>'2' and parvaz_det_id=$inp",$q);
		$out ="<div  class='detail_div' onclick=\"loadParvazDet($inp);\" ><span class='msg' >".count($q)."</span><span class='notice' >$movaghat</span> </div>";
		return $out;
	}
	function loadGhimat($inp)
	{
		return perToEnNums(monize($inp));
	}
	function umonizeGh($inp)
	{
		return perToEnNums(umonize($inp));
	}
	function colorFunc($inp)
	{
		function loadClassCss($parvaz_id,&$parvazTunck)
		{
			$my = new mysql_class;
			$rang = '';
			$my->ex_sql("select `rang` from `parvaz` where `id`='$parvaz_id' ",$q);
			if(isset($q[0]))
				$rang =  $q[0]['rang'];
			$parvazTunck[$parvaz_id] = $rang;
			return $rang;
		}
		$parvazTunck = array();
		$rows = $inp['rows'];
		$tmpRow =array();
		foreach($rows as $id=>$row)
		{
			$parvaz_id = $row['cell'][13]['value'];
			$row['css'] = (isset($parvazTunck[$parvaz_id]))?$parvazTunck[$parvaz_id]:loadClassCss($parvaz_id,$parvazTunck);
			$tmpRow[$id] = $row;
		}
		$inp['rows'] = $tmpRow;
		return $inp;
	}
	function delete_item($table,$id,$gname)
	{
		$my = new mysql_class;
		$my->ex_sqlx("update $table set en=0 where id in ($id)");
		return(TRUE);
	}
	function sh_type($inp)
	{
		$out='';
		if($inp==0)
			$out='عمومی ';
		else
			$out='خصوصی';
		return $out;
	}
	function loadEN($inp)
	{
		$name ='';
		$out ='';
		if($inp==1)
			$name = 'ok.png';
		else if($inp==2)
			$name = 'nok.png';
		else if($inp==3)
			$name = 'remove.png';
		if($name!='')
			$out = '<img src="../img/'.$name.'" >';
		return($out);
	}
	if(isset($_POST['change_parvaz']))
	{
		$parvaz_ids='';
		$mod = trim($_POST['mod']);
		foreach($_POST['parvaz_ids'] as $in=>$p_id)
			$parvaz_ids.=($parvaz_ids==''?'':',').$in;
		$my = new mysql_class;
		if($parvaz_ids!='')
		{
			if($mod=='del_parvaz')
				$sql = "update parvaz_det set en=0 where id in ($parvaz_ids)";
			else if($mod=='show_parvaz')
				$sql = "update parvaz_det set en=1 where id in ($parvaz_ids)";
			else if($mod=='hide_parvaz')
				$sql = "update parvaz_det set en=2 where id in ($parvaz_ids)";
			die($my->ex_sqlx($sql));
		}
	}
	$aztarikh = date("Y-m-d");
        $tatarikh = strtotime(date("Y-m-d H:i:s").' + 1 month');
        $tatarikh = date("Y-m-d",$tatarikh);
        $saat = date("H:i:s");
        $tshart = '';
        $shart = '1=1';
        if(isset($_REQUEST['smabda_id']))
        {
                $aztarikh = audit_class::hamed_pdateBack(trim($_REQUEST['saztarikh']),FALSE);
                $tatarikh =audit_class::hamed_pdateBack(trim($_REQUEST['statarikh']),FALSE);
                $mabda_id = (int)$_REQUEST['smabda_id'];
                $maghsad_id = (int)$_REQUEST['smaghsad_id'];
                $tshart = ($mabda_id==-1?'':" and parvaz.mabda_id=$mabda_id");
                $tshart .=($maghsad_id==-1?'':" and parvaz.maghsad_id=$maghsad_id");
        }
        $shart ="(tarikh>='$aztarikh' and tarikh<='$tatarikh') $tshart";//
	$domasire = TRUE;
	$pageRows = 10;
	$now = date("Y-m-d H:i:s");
	$my = new mysql_class;
	$my->ex_sql("select count(`id`) as `co` from `parvaz_det`",$l);
	if(isset($l[0]))
		$pageRows =(int) $l[0]['co'];
	$gname = 'grid_parvaz_det';
	$input =array($gname=>array('table'=>'parvaz_det','div'=>'parvaz_det_div','query'=>"select parvaz_det.id,ghimat,zarfiat,shahr.name mabda,shahr1.name maghsad,parvaz.shomare,
havapeima.name havapeima, parvaz_det.tarikh,parvaz_det.saat,parvaz_det.poor_def,
(parvaz_det.zakhire-(if((select sum(customer_parvaz.zakhire) from customer_parvaz where customer_parvaz.parvaz_det_id=parvaz_det.id ) is null,0,(select sum(customer_parvaz.zakhire) from customer_parvaz where customer_parvaz.parvaz_det_id=parvaz_det.id)))) zakhire,parvaz_det.typ,parvaz.id parvaz_id,sites.name site_name,parvaz_det.en
 from parvaz_det left join parvaz on (parvaz_det.parvaz_id=parvaz.id) 
left join shahr on (parvaz.mabda_id=shahr.id) 
left join shahr shahr1 on (parvaz.maghsad_id=shahr1.id) 
left join  havapeima on (parvaz.havapiema_id=havapeima.id) 
left join parvaz_det_sites on (parvaz_det.id=parvaz_det_sites.parvaz_det_id)
left join sites on (parvaz_det_sites.sites_id=sites.id)
where parvaz_det.en>=1 and $shart  order by parvaz_det.tarikh,parvaz_det.saat"));
	
	$xgrid = new xgrid($input);
	if(isset($_REQUEST['saztarikh']))
                $xgrid->eRequest[$gname]=array('saztarikh'=>$_REQUEST['saztarikh'],'statarikh'=>$_REQUEST['statarikh'],'smabda_id'=>$_REQUEST['smabda_id'],'smaghsad_id'=>$_REQUEST['smaghsad_id']);	
	$xgrid->disableRowColor[$gname] = TRUE;
	$xgrid->afterCreateFunction[$gname] = 'colorFunc';
	$id = $xgrid->column[$gname][0];
	$xgrid->column[$gname][0]['name'] = 'انتخاب';
	$xgrid->column[$gname][0]['cfunction']=array('loadCheckBox');
	$xgrid->column[$gname][1]['name'] = 'قیمت';
	$xgrid->column[$gname][2]['name'] = 'ظرفیت';
	$xgrid->column[$gname][3]['name'] = 'مبدأ'; 
	$xgrid->column[$gname][4]['name'] = 'مقصد';
	$xgrid->column[$gname][5]['name'] = 'شماره';
	$xgrid->column[$gname][6]['name'] = 'هواپیما';
	$xgrid->column[$gname][7]['name'] = 'تاریخ';
	$xgrid->column[$gname][7]['cfunction'] =array('hamed_pdate_day');
	$xgrid->column[$gname][8]['name'] = 'ساعت';
	$xgrid->column[$gname][8]['cfunction'] =array('saat');
	$xgrid->column[$gname][9]['name'] = 'کمیسیون';
	$xgrid->column[$gname][10]['name'] = 'ذخیره';
	$xgrid->column[$gname][11]['name'] = 'نوع';
	$xgrid->column[$gname][11]['cfunction'] = array('sh_type');
	$xgrid->column[$gname][12]['name']='';
	$xgrid->column[$gname][13]['name']='سایت';	
	$xgrid->column[$gname][14]['name']='نمایش';
	$xgrid->column[$gname][14]['cfunction'] = array('loadEN');
	$xgrid->column[$gname][15] = $id;
	$xgrid->column[$gname][16] = $id;
	$xgrid->column[$gname][15]['name'] = 'ویرایش';
	$xgrid->column[$gname][15]['cfunction'] = array('loadDet');
	$xgrid->column[$gname][16]['name'] = 'جزئیات فروش';
	$xgrid->column[$gname][16]['cfunction'] = array('parvaz_detail'); 
	/*
	//$xgrid->whereClause[$gname] = " `tarikh`>='$now'";
	$xgrid->whereClause[$gname] = " en>=1 order by `tarikh`,`saat`";
	$xgrid->canEdit[$gname] = TRUE;
	$xgrid->canDelete[$gname] = TRUE;
	$xgrid->deleteFunction[$gname] = 'delete_item';
	$xgrid->pageRows[$gname]= $pageRows;
	//$xgrid->alert = TRUE;
	//$xgrid->echoQuery = TRUE;
	$id = $xgrid->column[$gname][0];
	$parvaz_id = $xgrid->column[$gname][1];
	$tarikh = $xgrid->column[$gname][2];
	$saat = $xgrid->column[$gname][3];
	$zarfiat = $xgrid->column[$gname][4];
	$ghimat = $xgrid->column[$gname][5];
	$typ = $xgrid->column[$gname][6];
	$zakhire = $xgrid->column[$gname][7];
	$j_id = $xgrid->column[$gname][8];
	$poor_def =  $xgrid->column[$gname][9];
	$mablagh_kharid = $xgrid->column[$gname][10];
	$saat_kh = $xgrid->column[$gname][11];
	$can_esterdad = $xgrid->column[$gname][12];
	$en = $xgrid->column[$gname][13];
	$customer_id = $xgrid->column[$gname][14];
	$tour_mablagh =  $xgrid->column[$gname][15];
	$toz = $xgrid->column[$gname][16];

	$xgrid->column[$gname][0]['name'] = '';

	$xgrid->column[$gname][1] = $id;
	$xgrid->column[$gname][1]['name'] = 'انتخاب';
	$xgrid->column[$gname][1]['cfunction'] = array();
	$xgrid->column[$gname][1]['access']  = 'a';

	$xgrid->column[$gname][2] = $ghimat;
	$xgrid->column[$gname][2]['name'] = 'قیمت';
	$xgrid->column[$gname][2]['cfunction'] = array('loadGhimat','umonizeGh');

	$xgrid->column[$gname][3] =$id;
	$xgrid->column[$gname][3]['name'] = 'ظرفیت';
	$xgrid->column[$gname][3]['cfunction'] = array('loadZarfiat');
	$xgrid->column[$gname][3]['access']  = 'a';

	$xgrid->column[$gname][4] = array('name'=>'مبدأ','fieldname'=>'parvaz_id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadCityMab'));
	$xgrid->column[$gname][5] = array('name'=>'مقصد','fieldname'=>'parvaz_id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadCityMagh'));
	$xgrid->column[$gname][6] = $parvaz_id;
	$xgrid->column[$gname][6]['name'] = 'شماره';
	$xgrid->column[$gname][6]['cfunction'] = array('loadShomare');
	$xgrid->column[$gname][6]['access']  = 'a';

	$xgrid->column[$gname][7] = array('name'=>'هواپیما','fieldname'=>'parvaz_id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadPlane'));
	$xgrid->column[$gname][8] = $tarikh;
	$xgrid->column[$gname][8]['name'] = 'تاریخ';
	$xgrid->column[$gname][8]['cfunction'] = array('hamed_pdate');
	$xgrid->column[$gname][8]['access']  = 'a';

	$xgrid->column[$gname][9] = $saat;
	$xgrid->column[$gname][9]['name'] = 'خروج';
	$xgrid->column[$gname][9]['cfunction'] = array('saat');

	$xgrid->column[$gname][10] = $saat_kh;
	$xgrid->column[$gname][10]['name'] = '';
	//$xgrid->column[$gname][10]['cfunction'] = array('saat');

	$xgrid->column[$gname][11] = $id;
	$xgrid->column[$gname][11]['name'] = 'کمیسیون';
	$xgrid->column[$gname][11]['cfunction'] = array('poorsant');

	$xgrid->column[$gname][12] = $id;
	$xgrid->column[$gname][12]['name'] = 'ذخیره';
	$xgrid->column[$gname][12]['cfunction'] = array('loadCustomerZakhire');
	$xgrid->column[$gname][12]['access']  = 'a';

	$xgrid->column[$gname][13] = $typ;
	$xgrid->column[$gname][13]['name']='نوع';
	//$xgrid->column[$gname][12]['access']  = 'a';
	$xgrid->column[$gname][13]['clist']  = loadType();

	$xgrid->column[$gname][14] = $id;
	$xgrid->column[$gname][14]['name']='موارد دیگر';
	$xgrid->column[$gname][14]['access']  = 'a';
	$xgrid->column[$gname][14]['cfunction'] = array('loadDet');

	$xgrid->column[$gname][15] = $id;
	$xgrid->column[$gname][15]['name']='جزئیات فروش';
	$xgrid->column[$gname][15]['access']  = 'a';
	$xgrid->column[$gname][15]['cfunction'] = array('parvaz_detail');	
	$xgrid->column[$gname][16] =$parvaz_id;
	$xgrid->column[$gname][16]['name'] = '';
	*/
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out);
?>
<script>
	$(document).ready(function(){
		var args=<?php echo $xgrid->arg; ?>;
		args['<?php echo $gname; ?>']['afterLoad'] = afterLoadGrid;
                $.each($(".dateValue"),function(id,field){
	                if(field.id)
        		        Calendar.setup({
		                inputField     :    field.id,
		                button:    field.id,
		                ifFormat       :    "%Y/%m/%d",
		                dateType           :    "jalali",
		                weekNumbers    : false
                		});
		});
		intialGrid(args);
	});
	function afterLoadGrid()
	{
		initReserve();
		var del_btn="<button onclick=\"changeParvaz('del_parvaz');\" >حذف</button>";
		del_btn+="<button onclick=\"changeParvaz('show_parvaz');\" >نمایش</button>";
		del_btn+="<button onclick=\"changeParvaz('hide_parvaz');\" >عدم نمایش</button>";
		del_btn+="<span id='del_khoon' ></span>";
		$(".ajaxgrid_bottomTable tr:first td:first ").html(del_btn);
		$(".ajaxgrid_bottomTable tr:first td:nth-child(2)").remove();
	}
	function changeParvaz(mod)
	{
		if($.isEmptyObject(parvaz_id))
		{
			alert('هیچ پروازی انتخاب نشده است');
			return(false);
		}
		else
		{
			if(confirm("آیا تغییرات اعمال شود؟"))
			{
				var tmpArr = {
					'change_parvaz':1,
					'mod':mod,
					'parvaz_ids':parvaz_id	
				};
				$("#del_khoon").html("<img src='../img/status_fb.gif' >");	
				$.post("home_admin.php",tmpArr,function(result){
					$("#del_khoon").html('');
					searchFlight();
					alert('تغییرات با موفقیت اعمال گردید');	
				});
			}
		}
	}
	function searchFlight()
	{
		var werc ='';
		var ser ='ser';
		$.each($('.'+ser),function(id,field)
		{
			var fi = $("#"+field.id).val();
			if(field.id=='saztarikh' && fi!='' )
			{
				tmpTr = jToM(fi);
				werc +=((werc=='')?' where ':' and ')+" (date(`tarikh`) >= '"+tmpTr+"') ";
			}
			else if(field.id=='statarikh' && fi!='')
			{
				tmpTr = jToM(fi);
				werc +=((werc=='')?' where ':' and ')+" (date(`tarikh`) <= '"+tmpTr+"') ";
			}
			else if(field.id=='smabda_id' && parseInt(fi,10)!=-1)
				werc +=((werc=='')?' where ':' and ')+" ( `parvaz_id` in(select `id` from `parvaz` WHERE `mabda_id`='"+fi+"' ))  ";
			else if(field.id=='smaghsad_id' && parseInt(fi,10)!=-1)
				werc +=((werc=='')?' where ':' and ')+" ( `parvaz_id` in(select `id` from `parvaz` WHERE `maghsad_id`='"+fi+"' ))  ";
		});
		var ggname ='<?php echo $gname; ?>';
		whereClause[ggname] = encodeURIComponent(werc);
		grid[ggname].init(gArgs[ggname]);
	}
	function submitForm()
        {
                loadPage('home_admin.php?'+$("#frm_2").serialize());
        }
	function loadDet(inp)
	{
		openDialog("parvaz_detail.php?parvaz_det_id="+inp+"&","جزئیات بیشتر پرواز",{'minWidth':750,'minHeight':400},false);
	}
	function loadParvazDet(id)
	{
		openDialog("parvaz_forookhte.php?parvaz_det_id="+id+"&","جزئیات فروش پرواز",{'minWidth':750,'minHeight':550},false);
	}
</script>
<form id="frm_2" >
<table style="width:22cm;border-width:1px;border-style:dashed;border-collapse:collapse;border-color:#BCBCBC;">
	<tr  style="background-color:#EEEEEE;">
		<th>
			مبدأ :
		</th>
		
		<th>
			مقصد :
		</th>
									
		<th>
			از تاریخ :
		</th>
		
		<th>
			تا تاریخ :
		</th>
		
		<td>
			&nbsp;
		</td>
	</tr>
	<tr >
		<td>
			<select id="smabda_id" name="smabda_id" class="ser" >
			<?php
				echo loadCities(isset($_REQUEST['smabda_id'])?(int)$_REQUEST['smabda_id']:-1);
			?>
			</select>
		</td>
		<td>
			<select id="smaghsad_id" name="smaghsad_id" class="ser" >
			<?php
				echo loadCities(isset($_REQUEST['smaghsad_id'])?(int)$_REQUEST['smaghsad_id']:-1);
			?>
			</select>
		</td>	
		<td>
			<input onblur="correctDate(this);" autocomplete="off" class="ser dateValue" style="direction:ltr;" type="text" id="saztarikh" name="saztarikh" value="<?php echo isset($_REQUEST['saztarikh'])?trim($_REQUEST['saztarikh']):(jdate('Y/m/d',strtotime(date("Y-m-d")))); ?>" />
		</td>
		<td>
			<input onblur="correctDate(this);" autocomplete="off" class="ser dateValue" style="direction:ltr;" type="text" id="statarikh" name="statarikh" value="<?php echo isset($_REQUEST['statarikh'])?trim($_REQUEST['statarikh']):jdate('Y/m/d',strtotime(date("Y-m-d")." +15 day ")); ?>"  />
		</td>
		<td>
			<input type="button" value="نمایش و بروز رسانی" onclick="submitForm();" id="searchButton">
		</td>
	</tr>
</table>
</form>
<div id="parvaz_det_div" style="overflow:auto;border:1px dotted #bbb;paddin:5px;" >
</div>
<div id="reserve_div" class="show_div" >
	<table width="99%">
		<tr >
		
			<td align="center"  >بزرگسال</td>
			<td align="center"  > کودک </td>
			<td align="center"  >نوزاد</td>
		</tr>
		<tr>
			<td align="center"><input style="width:50px;"  id="reserve_adl" name="reserve_adl" class="textbox"/>
			</td>
			<td align="center"><input style="width:50px;"  id="reserve_chd" name="reserve_chd" class="textbox"/>
			</td>
			<td align="center"><input style="width:50px;"  id="reserve_inf" name="reserve_inf" class="textbox"/>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="6" >
				بلیط الکترونیکی-Eticket
				<input style="display:none"  type="checkbox" id="ticket_checkbox"  name="ticket_checkbox" checked="checked" onclick="checkEticket(this,document.getElementById('ticket_type'));" >
				<select class="inp"  id="ticket_type" name="ticket_type" style="display:none;" >
					<option value="0" selected="selected">بلیط الکترونیکی<br>Eticket</option>
					<option value="1">بلیط چاپ شده</option>
				</select> 
			</td>
		</tr>
		<tr>
			<td colspan="6" align="center"  >
				<!-- <input  value="رزرو پرواز" class="dokme" id="reserve" type="button" style="width:auto;font-weight:bold;"> -->
			<img src="../img/reserve.png" id="reserve" style="cursor:pointer;" >
			<input type="hidden" id="selected_parvaz" name="selected_parvaz" value="" />
			</td>
		</tr>
	</table>
</div>