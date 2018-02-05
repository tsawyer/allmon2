<?php
#error_reporting(E_ALL);
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');
date_default_timezone_set('America/Los_Angeles');
#print date("Y-m-d H:i:s") . "<br/>\n";
#print microtime() . "<br/>New voter app under development.<br/>\n";

include('allmon.inc.php');

// Sanity check
if (empty($_GET['node'])) {
    echo "data: Unknown voter request!\n\n";
	ob_flush();
	flush();
	exit;
}

// Read parameters passed to us
$node = trim(strip_tags($_GET['node']));

// Get Allstar database file
$db = "astdb.txt";
$astdb = array();
if (file_exists($db)) {
    $fh = fopen($db, "r");
    if (flock($fh, LOCK_SH)){
        while(($line = fgets($fh)) !== FALSE) {
            $arr = explode("|", trim($line));
            $astdb[$arr[0]] = $arr;
        }
    }
    flock($fh, LOCK_UN);
    fclose($fh);
}
#print_r($astdb);

// Read config INI file
if (!file_exists('allmon.ini.php')) {
    die("data: Couldn't load allmon ini file.\n\n");
}
$config = parse_ini_file('allmon.ini.php', true);
#print "<pre>"; print_r($config[$node]); print "</pre>";

// Open a socket to Asterisk Manager
echo "data: Connecting...\n\n";
ob_flush();
flush();
$fp = AMIconnect($config[$node]['host']);
if (FALSE === $fp) {
	die("Could not connect to Asterisk Manager.\n\n");
}
if (FALSE === AMIlogin($fp, $config[$node]['user'], $config[$node]['passwd'])) {
	die("Could not login to Asterisk Manager.");
}
#print "data: Connected and logged in.\n\n";
#ob_flush();
#flush();

$ticToc = '*';
$actionID = "voter$node" . mt_rand();    # Asterisk Manger Interface an actionID so we can find our own response	
while(TRUE) {
	// Get voter response
	$response = get_voter($fp, $actionID);
	if ($response === FALSE) {
	    die ("data: Bad voter response!<br/>");
	}
	
	// Build an array of nodes containing client, rssi and IP
	$lines = preg_split("/\n/", $response);
	$voted = array();
	$nodes=array();
	foreach ($lines as $line) {
		$line = trim($line);
		if (strlen($line) == 0) {
	    	continue;
		}
	    list($key, $value) = explode(": ", $line);
	    $$key = $value;

	    if ($key == "Node") {
	        $nodes[$Node]=array();
	    }
    
	    if ($key == "RSSI") {
	        $nodes[$Node][$Client]['rssi']=$RSSI;
	        $nodes[$Node][$Client]['ip']=$IP;
	    }
    
	    if ($key == 'Voted') {
	        $voted[$Node] = $value;
	    } 
	}
	#print "\$nodes: "; print_r($nodes[2532]);
	
	// Print tables for each node
#	foreach($nodes as $nodeNum => $clients) {
#		print_r($clients);
#	}
	$message = printNode($node, $nodes, $voted, $config[$node]);

	// Make a stupid blinky
	if ($ticToc == '*') {
		$ticToc = '<br/>';
	} else {
		$ticToc = '*';
	}
	
	print "data: $message\n";
	print "data: $ticToc\n\n";
	ob_flush();
	flush();
	
	usleep(100000);
}
exit;

function printNode($nodeNum, $nodes, $voted, $config) {
    #print '<pre>'; print_r($config); print '</pre>';
	$message = '';
	
    $info = getAstInfo($nodeNum);
    if (@$config['hideNodeURL'] == 1) {
        $message .= "<table class='rtcm'><tr><th colspan=2><i>Node $nodeNum - $info</i></th></tr>";
    } else {
        $nodeURL = "http://stats.allstarlink.org/nodeinfo.cgi?node=$nodeNum";
        $message .= "<table class='rtcm'><tr><th colspan=2><i>Node <a href=\"$nodeURL\" target=\"_blank\">$nodeNum</a> - $info</i></th></tr>";
    }
    $message .= "<tr><th>Client</th><th>RSSI</th></tr>";

    if (isset($clients) && count($clients) == 0) {
        $message .= "<td><div style='width: 120px;'>&nbsp;</div></td>";
        $message .= "<td><div style='width: 339px;'>&nbsp;</div></td>";
    }
	
	$clients = $nodes[$nodeNum];
    foreach($clients as $clientName => $client) {
    
        $rssi = $client['rssi'];
        $percent = ($rssi/255)*100;
        if ($percent == 0) {
            $percent = 1;
        }

        // find voted if any
        if (@$voted[$nodeNum] != 'none' && strstr($clientName, @$voted[$nodeNum])) {
                $barcolor = 'greenyellow';
                $textcolor = 'black';
        } elseif (strstr($clientName, 'Mix')) {
            $barcolor = 'cyan';
            $textcolor = 'black';
        } else {
            $barcolor = "#0099FF";
            $textcolor = 'white';
        }

        // print table rows
        $message .= "<tr>";
#        $message .= "<td><div style='width: 150px;'>$clientName</div></td>";
        $message .= "<td><div>$clientName</div></td>";
        $message .= "<td><div class='text'>&nbsp;<div class='barbox_a'>";
        $message .= "<div class='bar' style='width: " . $percent * 3 . "px; background-color: $barcolor; color: $textcolor'>$rssi</div>";
        $message .= "</div></td></tr>";
    }
	$message .= "<tr><td colspan=2>&nbsp;</td></tr>";
    $message .= "</table><br/>";
	
	return $message;
}

function get_voter($fp, $actionID) {
    // Voter status
    if ((@fwrite($fp,"ACTION: VoterStatus\r\nActionID: $actionID\r\n\r\n")) > 0) {
        // Get Voter Status
        return get_response($fp, $actionID);
    } else {
        return FALSE;
    }
}
?>
