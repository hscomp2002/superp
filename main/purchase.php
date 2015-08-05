<?php
        include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
	include_once ("../class/nusoap.php");
	$out = '';
//var_dump($_REQUEST);
	if(isset($_REQUEST['RefId']) && isset($_REQUEST['ResCode']) && isset($_REQUEST['SaleOrderId']) && isset($_REQUEST['SaleReferenceId']) && isset($_REQUEST['CardHolderInfo']))
	{
		$RefId  = $_REQUEST['RefId'];
		$ResCode = $_REQUEST['ResCode'];
		$SaleOrderId = $_REQUEST['SaleOrderId'];
		$SaleReferenceId = $_REQUEST['SaleReferenceId'];
		$CardHolderInfo = $_REQUEST['CardHolderInfo'];
		$bank_out = array('RefId'=>$RefId,'ResCode'=>$ResCode,'SaleOrderId'=>$SaleOrderId,'SaleReferenceId'=>$SaleReferenceId,'CardHolderInfo'=>$CardHolderInfo);
		$pay = pay_class::verify($SaleOrderId,$SaleReferenceId);
		//echo "pay:$pay<br/>";
		if(($pay == '0' || (int)$pay == 43) && (!is_array($pay)))
		{
			$pardakht = new pardakht_class($SaleOrderId);
			$pardakht->bank_out = serialize($bank_out);
			$sanad_record_id = sanad_class::getLastSanad_record_id();
			$sanad_record_id_ticket = $sanad_record_id;
			//-------------ticket ----------
			if(!($pardakht->is_tmp && !$pardakht->is_hotel))
			{
				pay_class::revers($SaleOrderId,$SaleReferenceId);
				die('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><center>در پردازش مشکلی پیش آمده است مجدد تلاش نمایید در صورت پرداخت وجه مبلغی از حساب شما کم نشده است <br/><a href="index.php" >بازگشت</a></center></body></html>');
			}
			$res_tmp =explode(',',$pardakht->sanad_record_id);
			$ghimat_kharid = 0;
			$ticket_ids = array();
			$ticket_error = FALSE;
			$shenavar = array();
			$tedad = 0;
			//var_dump($res_tmp);
			//for($i=0;$i<count($res_tmp);$i++)
			//{
			//echo "befor reserve_tmp<br/>";
				$reserve_tmp = new reserve_tmp_class($res_tmp[0]);
			//var_dump($reserve_tmp);
				if($reserve_tmp->info!='' && $reserve_tmp->info!=null)
				{
                                    $moghim_info = moghim_class::reservefl($reserve_tmp);
//echo "moghim_rsponse<br/>";
									//var_dump($moghim_info);
                                    if($moghim_info->reserveflResult)
                                    {   
                                        $etick = moghim_class::printEticket($reserve_tmp->rwaitlog);
                                        if(isset($etick->printEticketResult))
                                        {
                                            file_put_contents("../pdf/".$moghim_info->refer.str_replace('/','',$moghim_info->seldate).".pdf", fopen("http://91.98.31.190/ereports/NCRLYB940514.pdf", 'r'));
                                        }    
                                        $info = $reserve_tmp->info['info']; 
                                        $parvaz =  $reserve_tmp->info['parvaz'];
                                        if($parvaz->is_shenavar)
                                                $shenavar[] = $parvaz;
                                        foreach($info as $ticket)
                                        {
                                                $ticket->sanad_record_id = $sanad_record_id;
                                                if(!$ticket->add($res_tmp[0],$moghim_info,$reserve_tmp->rwaitlog,$ticket_id))
                                                        $ticket_error = TRUE;
                                                $ticket_ids[] = $ticket_id;
                                                if((int)$ticket->adult!=2)
                                                        $tedad++;
                                        }
                                    }
                                    else
                                    {
                                        pay_class::revers($SaleOrderId,$SaleReferenceId);
					die('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><center>در پردازش مشکلی پیش آمده است مجدد تلاش نمایید در صورت پرداخت وجه مبلغی از حساب شما کم نشده است!!! <br/><a href="index.php" >بازگشت</a></center></body></html>');
                                    }    
				}
				else
				{
					pay_class::revers($SaleOrderId,$SaleReferenceId);
					die('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><center>در پردازش مشکلی پیش آمده است مجدد تلاش نمایید در صورت پرداخت وجه مبلغی از حساب شما کم نشده است!!! <br/><a href="index.php" >بازگشت</a></center></body></html>');
				}
			//}
			if($ticket_error)
			{
				/*
				for($i=0;$i<count($ticket_ids);$i++)
					mysql_class::ex_sqlx("delet from `ticket` where `id`= ".$ticket_ids[$i]);
				*/
				ticket_class::clearTickets();
				pay_class::revers($SaleOrderId,$SaleReferenceId);
				die('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><center>در پردازش مشکلی پیش آمده است مجدد تلاش نمایید در صورت پرداخت وجه مبلغی از حساب شما کم نشده است !<br/><a href="index.php" >بازگشت</a></center></body></html>');
			}
			else
			{
				$customer = new customer_class($info[0]->customer_id);
				$customer->buyTicket($sanad_record_id,$pardakht->mablagh,FALSE);
				$pardakht->update($sanad_record_id);
				//-------------- shenavar sanad------------
				$sanad_record_id = sanad_class::getLastSanad_record_id();
				$user_id = (isset($_SESSION[$conf->app.'_user_id']))?(int)$_SESSION[$conf->app.'_user_id']:-1;
				foreach($shenavar as $par)
					parvaz_det_class::sanad_shenavar_kharid($par,$tedad,$sanad_record_id,$user_id);			
				//Sabte sanade pardakht parvaz.------------	
				$sanad_record_id = sanad_class::getLastSanad_record_id();
				$tozihat = ' بابت خرید نقدی بلیت به شماره سند '.$sanad_record_id_ticket;
				customer_class::pardakht($sanad_record_id,$info[0]->customer_id,$pardakht->mablagh,$tozihat,$user_id);
			}
			$mysql = new mysql_class;
			foreach($res_tmp as $tmpid)
				$mysql->ex_sqlx("delete from `reserve_tmp` where `id` = ".$tmpid);
			$rev = pay_class::settle($SaleOrderId,$SaleReferenceId);
			$rahgiri = pardakht_class::getBarcode($pardakht->id);
			$email ='armaniha@gmail.com';
			$text ='
<html>
	<body dir="rtl" style="font-family:tahoma;" >
		<h3>
			خرید بلیت به شماره ره‌گیری '.$rahgiri.' <br/>
		</h3>
		<span style="font-family:tahoma" >
			سامانه رزرواسیون پرواز بهار 
			<br/>
			www.gcom.ir
		</span>
	</body>
</html>
';
			$mail = new email_class($email,'ثبت بلیت به شماره ره‌گیری'.$rahgiri,$text);
			$out ='<script langauge="javascript" >window.location = "finalticket2.php?ticket_type=0&sanad_record_id='.$sanad_record_id_ticket.'&rahgiri='.$rahgiri.'&SaleReferenceId='.$SaleReferenceId.'"</script>';
		}
		else
			$out = ' پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، مبلغ از حساب شما کم نشده است
					<br/>
					<input class="inp" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';
	}
	else
		$out = 'در تراکنش مالی مشکلی پیش آمده است پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، مبلغ از حساب شما کم نشده است
			<br/>
			<input class="inp" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="css/style.css" rel="stylesheet" />	
		<script type="text/javascript" src="js/tavanir.js"></script>
		<style>
		td { text-align: center; }
		</style>
		<title>
		</title>
		<script language="javascript" >
		</script>
	</head>
	<body style="background: #B5D3FF;padding-bottom: 0px;">
		<div align="center" style="background: #B5D3FF;margin:20px;" >
			<?php
				if($out == '')				
					echo "در حال تولید بلیت‌ها لطفاً منتظر بمانید";
				else
					echo $out; 
			?>
			<br/>
			<br/>
		</div>
		</center>
	</body>
</html>
