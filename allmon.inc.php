<?php
error_reporting(0);

// Reads output lines from Asterisk Manager
function get_response($fp, $actionID) {
    while (TRUE) {
		$str = fgets($fp);
		# Looking for our actionID
		if ("ActionID: $actionID" == trim($str)) {
			$response = $str;
			while (TRUE) {
				$str = fgets($fp);
		        #if (strlen(trim($str)) != 0 ) {
                if ($str != "\r\n") {
		            $response .= $str;
		        } else {
		            return($response);
		        }
			}
		}
    }
}

function AMIconnect($host) {
    // Set default port if not provided
    $arr = explode(":", $host);
    $ip = $arr[0];
    if (isset($arr[1])) {
        $port = $arr[1];
    } else {
        $port = 5038;
    }
    
    // Open a manager socket.
    $fp = @fsockopen($ip, $port, $errno, $errstr, 5);
    #print "parms: $ip $port $errno $errstr";
    return ($fp);
}

function AMIlogin($fp, $user, $password) {
    // Login
	$actionID = $user . $password;
    fwrite($fp,"ACTION: LOGIN\r\nUSERNAME: $user\r\nSECRET: $password\r\nEVENTS: 0\r\nActionID: $actionID\r\n\r\n");
    $login = get_response($fp, $actionID);
	if (preg_match("/Authentication accepted/", $login) == 1) {
		return(TRUE);
	} else {
		return(FALSE);
	}
}

function getAstInfo($nodeNum, $node=array()) {
    global $astdb;
    #print '<pre>'; print_r($nodeNum); print '</pre>';
    
    // Build info string
    //if (array_key_exists($nodeNum, $astdb)) {
    if (isset($astdb[$nodeNum])) {
        $dbNode = $astdb[$nodeNum];
        $info = $dbNode[1] . ' ' . $dbNode[2] . ' ' . $dbNode[3];
    } elseif ($nodeNum > 3000000) {
        $info = "Echolink";
    } elseif (!empty($node['ip'])) {
        if (strlen(trim($node['ip'])) > 3) {
            $info = '(' . $node['ip'] . ')';
        } else {
            $info = '&nbsp;';
        }
    } else {
        $info = '&nbsp;';
    }

    return $info;
}

?>
