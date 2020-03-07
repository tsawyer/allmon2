<?php
include ("session.inc");
$node = @trim(strip_tags($_GET['node']));
if (!is_numeric($node)) {
    die ("Please provide a properly formated URI. (ie controlpanel.php?node=1234)");
}

$title = "Allmon Node $node Control Panel";
    
if ($_SESSION['loggedin'] === true) {
    // Read allmon INI file
    if (!file_exists('allmon.ini.php')) {
        die("Couldn't load file allmon.ini.php.\n");
    }
    $allmonConfig = parse_ini_file('allmon.ini.php', true);
    
    // Read cintrolpanel INI file
    if (!file_exists('controlpanel.ini.php')) {
        die("Couldn't load file controlpanel.ini.php.
            \n");
    }
    $cpConfig = parse_ini_file('controlpanel.ini.php', true);
    
    //combine [general] stanza with this node's stanza
    $cpCommands = $cpConfig['general'];
    if (isset($cpConfig[$node])) {
        foreach ($cpConfig[$node] as $type => $arr) {
            if ($type == 'labels') {
                foreach($arr as $label) {
                    $cpCommands['labels'][] = $label;
                }
            } elseif ($type == 'cmds') {
                foreach($arr as $cmd) {
                    $cpCommands['cmds'][] = $cmd;
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="generator" content="By hand with a text editor">
<meta name="description" content="Allmon Control Panel">
<meta name="keywords" content="allstar monitor, app_rpt, asterisk">
<meta name="author" content="Tim Sawyer, WD6AWP">
<link type="text/css" rel="stylesheet" href="allmon.css">
<link type="text/css" rel="stylesheet" href="jquery-ui.css">
<script src="jquery.min.js"></script>
<script src="jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    // Is user authenticated

<?php if ($_SESSION['loggedin'] !== true) { ?>

        alert ('Must login to use the Control Panel.');

<?php }	else { ?>

        // css hides 
        $("#cpMain").show();

<?php }	?>
    // When Ok is clicked
    $('#cpExecute').click(function() {
        var localNode = $('#localnode').val();
        var cpCommand = $('#cpSelect').val();
        
        // Do Ajax get
        $.get('controlserver.php?node=' + localNode + '&cmd=' + cpCommand, function( data ) {
            $('#cpResult').html( data );
        });
    });
});    
</script>
</head>
<body>
<div id="header">
<div id="headerTitle">Allstar Monitor II</div>
<div id="headerTag"><?php echo $title ?></div>
<div id="headerImg"><img src="allstarLogo.png" alt="Allstar Logo"></div>
</div>
<div id="cpMain">
Control (select one): <select name="cpSelection" id="cpSelect">
<?php 
for($i=0; $i < count($cpCommands['labels']); $i++) {
    print "<option value=\"" . $cpCommands['cmds'][$i] . "\">" . $cpCommands['labels'][$i] . "</option>\n";
}
?>
</select>
<input type="hidden" id="localnode" value="<?php echo $node ?>">
<input type="button" value="Ok" id="cpExecute">
<br/>
<div id="cpResult">
    <!-- Results shown here -->
</div>
</div>
<?php include "footer.inc"; ?>
