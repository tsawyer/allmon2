<?php
include('session.inc');
include('allmon.inc.php');

if ($_SESSION['loggedin'] !== true) {
	die("Please login to use connect/disconnect functions.\n");
}


// Filter and validate user input
$remotenode = @trim(strip_tags($_POST['remotenode']));
$perm = @trim(strip_tags($_POST['perm']));
$button = @trim(strip_tags($_POST['button']));
$localnode = @trim(strip_tags($_POST['localnode']));

if (! preg_match("/^\d+$/",$remotenode)) {
    die("Please provide node number to connect.\n");
}
if (! preg_match("/^\d+$/",$localnode)) {
    die("Please provide local node number.\n");
}

// Read configuration file
if (!file_exists('allmon.ini.php')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('allmon.ini.php', true);
#print "<pre>"; print_r($config); print "</pre>";

// Open a socket to Asterisk Manager
$fp = AMIconnect($config[$localnode]['host']);
if (FALSE === $fp) {
	die("Could not connect to Asterisk Manager.\n\n");
}
if (FALSE === AMIlogin($fp, $config[$localnode]['user'], $config[$localnode]['passwd'])) {
	die("Could not login to Asterisk Manager.");
}

// Which ilink command?
if ($button == 'connect') {
    if ($perm == 'on') {
        $ilink = 13;
        print "<b>Permanent Connecting $localnode to $remotenode</b>";
    } else {
        $ilink = 3;
        print "<b>Connecting $localnode to $remotenode</b>";
    }
} elseif ($button == 'monitor') {
    if ($perm == 'on') {
        $ilink = 12;
        print "<b>Permanent Monitoring $remotenode from $localnode</b>";
    } else {
        $ilink = 2;
        print "<b>Monitoring $remotenode from $localnode</b>";
    }
} elseif ($button == 'localmonitor') {
    if ($perm == 'on') {
        $ilink = 18;
        print "<b>Permanent Local Monitoring $remotenode from $localnode</b>";
    } else {
        $ilink = 8;
        print "<b>Local Monitoring $remotenode from $localnode</b>";
    }
} elseif ($button == 'disconnect') {
    if ($perm == 'on') {
        $ilink = 11;
        print "<b>Permanent Disconnect $remotenode from $localnode</b>";
    } else {
        $ilink = 1;
        print "<b>Disconnect $remotenode from $localnode</b>";
    }
}

#exit;

// Asterisk Manger Interface needs an actionID so we can find our own response
$actionRand = mt_rand();    
$actionID = 'connect' . $actionRand;

// Do it
if ((@fwrite($fp,"ACTION: COMMAND\r\nCOMMAND: rpt cmd $localnode ilink $ilink $remotenode\r\nActionID: $actionID\r\n\r\n")) > 0 ) {
    // Get response, but do nothing with it
    $rptStatus = get_response($fp, $actionID);
    #print "<pre>===== start =====\n";
    #print_r($rptStatus);            
    #print "===== end =====\n</pre>";
} else {
    die("Command failed!\n");
}

?>
