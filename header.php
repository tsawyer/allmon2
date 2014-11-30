<?php
// Set title
$var1 = explode('/', $_SERVER['REQUEST_URI']);
$var2 = array_pop($var1);
$uri = urldecode($var2);
#$uri = urldecode(array_pop(explode('/', $_SERVER['REQUEST_URI'])));
$pageTitle = strtoupper($_SERVER['SERVER_NAME']) . " | Allmon | "; 
if (preg_match("/voter\.php\?node=(\d+)$/", $uri, $matches)) {
    $pageTitle .= "RTCM " . $matches[1];
} elseif (preg_match("/link\.php\?nodes=(.+)$/", $uri, $matches)) {
    $pageTitle .= $matches[1];
} elseif (preg_match("/about/", $uri, $matches)) {
    $pageTitle .= "" . ucfirst($matches[0]);
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $pageTitle; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="generator" content="By hand with a text editor">
<meta name="description" content="Allstar node manager">
<meta name="keywords" content="allstar monitor, app_rpt, asterisk">
<meta name="author" content="Tim Sawyer, WD6AWP">
<link type="text/css" rel="stylesheet" href="allmon.css">
<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.1/themes/base/jquery-ui.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script type="text/javascript" src="jquery.cookie.js"></script>
<script type="text/javascript" src="allmon.js"></script>
</head>
<body>
<div id="header">
<div id="headerTitle">Allstar Monitor II</div>
<div id="headerTag">Monitoring the World One Node at a Time</div>
<div id="headerImg"><img src="allstarLogo.png" alt="Allstar Logo"></div>
</div>
<?php include "menu.php"; ?>
