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

<div class="row">
<div class="col-sm-5">
<?php foreach ($passedNodes as $node) { ?>
<div id="link_list_<?php echo $node; ?>">Loading voter...</div>
<?php } ?>

</div>





<div class="col">
The numbers indicate the relative signal strength. The value ranges from 0 to 255, a range of approximately 30db.
A value of zero means that no signal is being received. The color of the bars indicate the type of RTCM client.
<p>
<div class="progress" style="height: 40px">
  <div class="progress-bar" style="width: 100%" role="progressbar" aria-valuenow="76" aria-valuemin="0" aria-valuemax="100">Blue - Voting Station</div>
</div></p>
<p>
<div class="progress" style="height: 40px">
  <div class="progress-bar bg-success" style="width: 100%" role="progressbar" aria-valuenow="76" aria-valuemin="0" aria-valuemax="100">Green - Station is voted</div>
</div>
</p>
<p>
<div class="progress" style="height: 40px">
  <div class="progress-bar bg-info" style="width: 100%" role="progressbar" aria-valuenow="76" aria-valuemin="0" aria-valuemax="100">Teal - Non-voting mix station</div>
</div>
</p>

</div>
</div>
<?php include "footer.inc"; ?>
