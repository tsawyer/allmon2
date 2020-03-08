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
<!--<link type="text/css" rel="stylesheet" href="allmon.css">-->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

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

<!--<div id="header">
<div id="headerTitle">Allstar Monitor II</div>
<div id="headerTag"><?php echo $title ?></div>
<div id="headerImg"><img src="allstarLogo.png" alt="Allstar Logo"></div>
</div>-->
<div class="container-md">

<nav class="navbar navbar-dark navbar-expand-lg bg-dark">
<span class="navbar-brand"><?php echo $title ?></span>
</nav>




<div class="row">
<div id="cpMain" class="col">

	
	<div class="input-group mb-3 mt-2">
		
		<select class="custom-select" name="cpSelection" id="cpSelect">
		<?php 
		for($i=0; $i < count($cpCommands['labels']); $i++) {
			print "<option value=\"" . $cpCommands['cmds'][$i] . "\">" . $cpCommands['labels'][$i] . "</option>\n";
		}
		?>
		</select>

		<div class="input-group-append">
			<button type="button" class="btn btn-success" id="cpExecute">OK</button>
		</div>
	</div>
	<input type="hidden" id="localnode" value="<?php echo $node ?>">




<div id="cpResult">
    <!-- Results shown here -->
</div>

</div>
</div>

<?php include "footer.inc"; ?>
