<?php 
include "session.inc";
include "header.inc";

$passedNodes = explode(',', @trim(strip_tags($_GET['node'])));

// If no node number use first non-voter in INI
if (empty($passedNodes[0])) {
    die ("Please provide voter node number. (ie voter.php?node=1234)");
}
?>
<script>
    // prevent IE caching
    $.ajaxSetup ({  
        cache: false,
        timeout: 3000
    });    

    // when DOM is ready
    $(document).ready(function() {
	    if(typeof(EventSource)!=="undefined") {
<?php foreach ($passedNodes as $node) { ?>
	        var source<?php echo $node; ?>=new EventSource("voterserver.php?node=<?php echo $node; ?>");
	        //var source=new EventSource("sse_demo.php");
	        source<?php echo $node; ?>.onmessage=function(event) {
	            $("#link_list_<?php echo $node; ?>").html(event.data);
	        };
<?php } ?>
	    } else {
	        $("list_link_<?php echo $passedNodes[0]; ?>").html("Sorry, your browser does not support server-sent events...");
	    }
    });            
</script>
<br/>
<?php foreach ($passedNodes as $node) { ?>
<div id="link_list_<?php echo $node; ?>">Loading voter...</div>
<?php } ?>
<div style='width:500px; text-align:left;'>
The numbers indicate the relative signal strength. The value ranges from 0 to 255, a range of approximately 30db.
A value of zero means that no signal is being received. The color of the bars indicate the type of RTCM client.
</div>
<div style='width: 240px; text-align:left; position: relative; left: 160px;'>
<div style='background-color: #0099FF; color: white; text-align: center;'>A blue bar indicates a voting station.</div>
<div style='background-color: greenyellow; color: black; text-align: center;'>Green indicates the station is voted.</div>
<div style='background-color: cyan; color: black; text-align: center;'>Cyan is a non-voting mix station. </div>
</div>
<?php include "footer.inc"; ?>
