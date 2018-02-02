<?php 
include "session.inc";
error_reporting(E_ALL);
include "header.inc";
$parms = @trim(strip_tags($_GET['nodes']));
$passedNodes = explode(',', @trim(strip_tags($_GET['nodes'])));
#print_r($nodes);

if (count($passedNodes) == 0) {
    die ("Please provide a properly formated URI. (ie link.php?nodes=1234 | link.php?nodes=1234,2345)");
}

// Get Allstar database file
$db = "astdb.txt";
$astdb = array();
if (file_exists($db)) {
    $fh = fopen($db, "r");
    if (flock($fh, LOCK_SH)){
        while(($line = fgets($fh)) !== FALSE) {
            $arr = preg_split("/\|/", trim($line));
            $astdb[$arr[0]] = $arr;
        }
    }
    flock($fh, LOCK_UN);
    fclose($fh);
    #print "<pre>"; print_r($astdb); print "</pre>";
}

// Read allmon INI file
if (!file_exists('allmon.ini.php')) {
    die("Couldn't load allmon ini file.\n");
}
$config = parse_ini_file('allmon.ini.php', true);
#print "<pre>"; print_r($config); print "</pre>";
#print "<pre>"; print_r($config[$node]); print "</pre>";

// Remove nodes not in our allmon.ini file.
$nodes=array();
foreach ($passedNodes as $i => $node) {
	if (isset($config[$node])) {
		$nodes[] = $node;
	} else {
		print "Warning: Node $node not found in our allmon ini file.";
	}
}

?>
<script type="text/javascript">
// when DOM is ready
$(document).ready(function() {
    if(typeof(EventSource)!=="undefined") {
		
		// Start SSE 
        var source=new EventSource("server.php?nodes=<?php echo $parms; ?>");
        
        // Fires when node data come in. Updates the whole table
        source.addEventListener('nodes', function(event) {
			//console.log('nodes: ' + event.data);	        
            // server.php returns a json formated string
            var tabledata = JSON.parse(event.data);
        	for (var localNode in tabledata) {
        		var tablehtml = '';
				if (tabledata[localNode].remote_nodes.length == 0) {
					$('#table_' + localNode  + ' tbody:first').html('<tr><td colspan="7">No connections.</td></tr>');
				} else {
	        		for (row in tabledata[localNode].remote_nodes) {
        		
	        			// Set green, red or no background color 
	            		if (tabledata[localNode].remote_nodes[row].keyed == 'yes') {
		            		tablehtml += '<tr class="rColor">';
	            		} else if (tabledata[localNode].remote_nodes[row].mode == 'C') {
		            		tablehtml += '<tr class="cColor">';
	            		} else {
		            		tablehtml += '<tr>';
	            		}
	            		var id = 't' + localNode + 'c0' + 'r' + row;
	            		//console.log(id);
	            		tablehtml += '<td id="' + id + '" class="nodeNum">' + tabledata[localNode].remote_nodes[row].node + '</td>';
            		
	            		// Show info or IP if no info
	            		if (tabledata[localNode].remote_nodes[row].info != "") {
		            		tablehtml += '<td>' + tabledata[localNode].remote_nodes[row].info + '</td>';
	            		} else {
		            		tablehtml += '<td>' + tabledata[localNode].remote_nodes[row].ip + '</td>';
	            		}
            		
	            		tablehtml += '<td id="lkey' + row + '">' + tabledata[localNode].remote_nodes[row].last_keyed + '</td>';
	            		tablehtml += '<td>' + tabledata[localNode].remote_nodes[row].link + '</td>';
	            		tablehtml += '<td>' + tabledata[localNode].remote_nodes[row].direction + '</td>';
	            		tablehtml += '<td id="elap' + row +'">' + tabledata[localNode].remote_nodes[row].elapsed + '</td>';
            		
	            		// Show mode in plain english
	            		if (tabledata[localNode].remote_nodes[row].mode == 'R') {
		            		tablehtml += '<td>RX only</td>';
	            		} else if (tabledata[localNode].remote_nodes[row].mode == 'T') {
		            		tablehtml += '<td>Transceive</td>';
		            	} else if (tabledata[localNode].remote_nodes[row].mode == 'C') {
		            		tablehtml += '<td>Connecting</td>';
	            		} else {
		            		tablehtml += '<td>' + tabledata[localNode].remote_nodes[row].mode + '</td>';
	            		}
	            		tablehtml += '</tr>';
	        		}
        		
					//console.log('tablehtml: ' + tablehtml);
	        		$('#table_' + localNode + ' tbody:first').html(tablehtml);
				}
        	}
        });
        
        // Fires when new time data comes in. Updates only time columns
        source.addEventListener('nodetimes', function(event) {
			//console.log('nodetimes: ' + event.data);	        
			var tabledata = JSON.parse(event.data);
			for (localNode in tabledata) {
				tableID = 'table_' + localNode;
				for (row in tabledata[localNode].remote_nodes) {
					//console.log(tableID, row, tabledata[localNode].remote_nodes[row].elapsed, tabledata[localNode].remote_nodes[row].last_keyed);
					rowID='lkey' + row;
					$( '#' + tableID + ' #' + rowID).text( tabledata[localNode].remote_nodes[row].last_keyed );
					rowID='elap' + row;
					$( '#' + tableID + ' #' + rowID).text( tabledata[localNode].remote_nodes[row].elapsed );
				}
			}
			
			if (blinky == "*") {
				blinky = "&nbsp;";
			} else {
				blinky = "*";
			}
			$('#blinky').html(blinky);
        });
        
        // Fires when conncetion message comes in.
        source.addEventListener('connection', function(event) {
			//console.log(statusdata.status);
			var statusdata = JSON.parse(event.data);
			tableID = 'table_' + statusdata.node;
			$('#' + tableID + ' tbody:first').html('<tr><td colspan="7">' + statusdata.status + '</td></tr>');
		});
		       
    } else {
        $("#list_link").html("Sorry, your browser does not support server-sent events...");
    }
});
</script>
<br/>
<!-- Connect form -->
<div id="connect_form">
<?php 
if (count($nodes) > 0) {
    if (count($nodes) > 1) {
        print "<select id=\"localnode\">";
        foreach ($nodes as $node) {
		if (isset($astdb[$node]))
			$info = $astdb[$node][1] . ' ' . $astdb[$node][2] . ' ' . $astdb[$node][3];
		else
			$info = "Node not in database";
            print "<option value=\"$node\">$node - $info</option>";
        }
        print "</select>\n";
    } else {
        print "<input type=\"hidden\" id=\"localnode\" value=\"{$nodes[0]}\">\n";
    }
?>
<input type="text" id="node">
Permanent <input type="checkbox"><br/>
<input type="button" value="Connect" id="connect">
<input type="button" value="Disconnect" id="disconnect">
<input type="button" value="Monitor" id="monitor">
<input type="button" value="Local Monitor" id="localmonitor">
<input type="button" value="Control Panel" id="controlpanel">
<?php
} #endif (count($nodes) > 0)
?>
</div>

<!-- Nodes table -->
<div>
<?php
#print '<pre>'; print_r($nodes); print '</pre>';
foreach($nodes as $node) {
    #print '<pre>'; print_r($config[$node]); print '</pre>';
	if (isset($astdb[$node]))
		$info = $astdb[$node][1] . ' ' . $astdb[$node][2] . ' ' . $astdb[$node][3];
	else
		$info = "Node not in database";
    if (($info == "Node not in database" ) || (isset($config[$node]['hideNodeURL']) && $config[$node]['hideNodeURL'] == 1)) {
        $nodeURL = $node;
        $title = "$node - $info";
    } else {
        $nodeURL = "http://stats.allstarlink.org/nodeinfo.cgi?node=$node";
        $bubbleChart = "http://stats.allstarlink.org/getstatus.cgi?$node";
    	$title = "Node <a href=\"$nodeURL\" target=\"_blank\">$node</a> - $info ";
    	$title .= "<a href=\"$bubbleChart\" target=\"_blank\" id=\"bubblechart\">Bubble Chart</a>";
    }
?>
	<table class=gridtable id="table_<?php echo $node ?>">
	<colgroup>
       <col span="1" style="width: 50px;">
       <col span="1" style="width: 300px;">
       <col span="1" style="width: 80px;">
       <col span="1" style="width: 100px;">
       <col span="1" style="width: 70px;">
       <col span="1" style="width: 80px;">
       <col span="1" style="width: 90px;">
    </colgroup>
	<thead>
	<tr style="font-size:20px"><th colspan="7"><i><?php echo $title; ?></i></th></tr>
	<tr><th>Node</th><th>Node Information</th><th>Received</th><th>Link</th><th>Direction</th><th>Connected</th><th>Mode</th></tr>
	</thead>
	<tbody>
	<tr><td colspan="7">Waiting...</td></tr>
	</tbody>
	</table><br />
<?php
}
?>
</div>
<div id="blinky">
</div>
<?php include "footer.inc"; ?>
