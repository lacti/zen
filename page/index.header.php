<?php
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="format-detection" content="telephone=no" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="keywords" content="programming,프로그래밍,coding,code,coder,programmer,c,cpp,java,python,php,mysql,html,javascript,js" />
	<meta name="description" content="프로그래밍의 도를 닦는 길" />
	<title><?=$HEAD_TITLE?></title>
	<link rel="stylesheet" type="text/css" href="/res/default.css" /> 
	<link rel="stylesheet" type="text/css" href="/res/classy.css" /> 
<!--<link href="/syntax/styles/shThemeEclipse.css" rel="stylesheet" type="text/css" />-->
	<link href="/syntax/styles/shThemeMidnight.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
	<script src="/res/zen.js" type="text/javascript"></script>
	<script src="/syntax/scripts/shCore.js" type="text/javascript"></script>
	<script src="/syntax/scripts/shAutoloader.js" type="text/javascript"></script>
</head>
<body>
<div id="bodyWrap">
<div id="headerWrap">
	<p id="header">
		<a onclick="location.href='<?php echo HOME_URL; ?>'" style="cursor: pointer; position: absolute; width: 200px; height: 55px;"></a>
		&nbsp;
	</p>
	<div id="user">
<?php		require "page/user.login.php"; ?>
	</div>
</div>
<div id="toolWrap">
	<div id="menuWrap">
	</div>
	<div id="subMenuWrap">
	</div>
</div>

<div id="contentWrap">
<?php		require "page/search.input.php"; ?>
<div class="content">
