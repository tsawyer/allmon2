<?php
include('session.inc');
include('allmon.inc.php');

if ($_SESSION['loggedin'] !== true) {
        die("Please login to to user the Control Panel.\n");
}

if (!isset($_GET['node'])) {
	die('Unspecified failure!');
}

if (!isset($_GET['cmd'])) {
	die ('Unspecified failure!');
}

#print_r($_GET);
$node = @trim(strip_tags($_GET['node']));
$cmd = @trim(strip_tags($_GET['cmd']));

// Read allmon INI file
if (!file_exists('allmon.ini.php')) {
    die("Couldn't load allmon ini file.\n");
}
$config = parse_ini_file('allmon.ini.php', true);

// Check if node exists in ini
if (!isset($config[$node])) {
    die("Node $node is not in allmon ini file.");
}

// Set up connection
$fp = AMIconnect($config[$node]['host']);
if (FALSE === $fp) {
    die("Could not connect to Asterisk Manager.");
}

// Login 
if (FALSE === AMIlogin($fp, $config[$node]['user'], $config[$node]['passwd'])) {
    die("Could not login to Asterisk Manager.");
}

// Substitute node number
$cmdString = preg_replace("/%node%/", $node, $cmd);

// AMI needs an ActionID so we can find our own response
$actionRand = mt_rand(); 
$actionID = 'cpAction_' . $actionRand;

if ((@fwrite($fp,"ACTION: COMMAND\r\nCOMMAND: $cmdString\r\nActionID: $actionID\r\n\r\n")) > 0 ) {
    $rptStatus = get_response($fp, $actionID);
    print "<pre>\n===== $cmdString =====\n";
    print $rptStatus;
    #print "===== end =====\n</pre>\n";
} else {
    die("Get node $cmdString failed!");
}
?>
