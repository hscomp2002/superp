<?php	session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
               // die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                //die(lang_fa_class::access_deny);
	$grid = new jshowGrid_new("log","grid1");
	//$grid->whereClause = ' `en`=1 order by `name`';
	//$grid->columnHeaders[0] = null;
	//$grid->columnHeaders[1] = "نام";
	//$grid->columnHeaders[2] = null;
	//$grid->addFeild('id');
	//$grid->columnHeaders[3] = 'ارث بری دسترسی';
	//$grid->columnFunctions[3] = 'accessCopy';
	//$grid->columnAccesses[3] = 0;
	//$grid->deleteFunction = 'delete_item';
	//$grid->addFunction = 'add_item';
        $grid->intial();
   	$grid->executeQuery();
        $out = $grid->getGrid();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<?php	echo $out;?>
		</div>
	</body>
</html>
