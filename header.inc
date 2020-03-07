<?php
$site_title = "KDØIOE Allstar";

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
$home = '/';
if (!empty($var1[1])) {
   $home="/" . $var1[1];
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
<meta name="robots" content="noindex, nofollow">
<meta name="author" content="Tim Sawyer, WD6AWP">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<!--<link type="text/css" rel="stylesheet" href="allmon.css">-->

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>





<!--<script src="jquery-3.3.1.min.js"></script>
<script src="jquery-ui.min.js"></script>-->
<script type="text/javascript">
// Was allmon.js
// when DOM is ready
$(document).ready(function() {

    //Set to hide Login Link
    hideLoginLink = false
    
    // Hide login link on page load?
    if (hideLoginLink) {
        $('#loginlink').hide();
    }

<?php if ($_SESSION['loggedin'] === true) { ?>

        $('#loginlink').hide();

<?php } else { ?>

        $('#connect_form').hide();
        $('#logoutlink').hide();

<?php } ?>

    
    // Login dialog
    /* $("#login").dialog( {
        autoOpen: false,
        title: 'Manager Login',
        modal: true,
       open: function() {
           // On open, hide the original submit button
           $( this ).find( "[type=submit]" ).hide();
       },
        buttons: [ { 
           text: "OK",
           type: "submit",
           form: "login",
           click: function() {
               var user = $('form input:text').val();
               var passwd = $('input:password').val();
               $(this).dialog("close"); 
               $('#test_area').load("login.php", { 'user' : user, 'passwd' : passwd }, function(response) {
                   if (response.substr(0,5) != 'Sorry') {
                       $('#connect_form').show();
                       $('#logoutlink').show();
                       $('#loginlink').hide();
                   }
               });
               $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(1000).fadeOut(2000);
           }
        } ]
     });

     // make enter key submit login form
     $('#login').on('keyup', function(e){
       if (e.keyCode == 13) {
         $(':button:contains("OK")').click();
       }
     });
     
    // Login dialog opener
    $("#loginlink").click(function() {
        //$("#login").dialog('open');
		$("#loginModal").modal({show:true})
        return false;
    });
    */
    // Logout 
    $('#logoutlink').click(function(event) {
	$.get("logout.php");
        if (! hideLoginLink) {
            $('#loginlink').show();
        }
        $('#logoutlink').hide();
        $('#connect_form').hide();
        event.preventDefault();
    }); 

	// IOE - Login function
	$("#loginForm").submit(function () {
		var user = $('form input:text').val();
               var passwd = $('input:password').val();
               //$(this).dialog("close"); 
               $('#test_area').load("login.php", { 'user' : user, 'passwd' : passwd }, function(response) {
                   if (response.substr(0,5) != 'Sorry') {
                       $('#connect_form').show();
                       $('#logoutlink').show();
                       $('#loginlink').hide();
                   }
               });
               $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(1000).fadeOut(2000);
		
		
		
	});



    // Ajax function a link
    $('#connect, #monitor, #permanent, #localmonitor, #disconnect').click(function() {
        var button = this.id;    // which button was pushed
        var localNode = $('#localnode').val();
        var remoteNode = $('#node').val(); 
        var perm = $('input:checkbox:checked').val();

            if (remoteNode.length == 0) {
                  alert('Please enter the remote node number.');
                  return;
            }
            
            if (button == 'disconnect') {
              r = confirm("Disconnect " + remoteNode + " from " + localNode + "?");
                  if (r !== true) {
                        return;
                  }
                  
            }            
            
        $.ajax( { url:'connect.php', data: { 'remotenode' : remoteNode, 'perm' : perm, 'button' : button, 'localnode' : localNode }, type:'post', success: function(result) {
                $('#test_area').html(result);
                $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(1000).fadeOut(2000);
            }
        });
    });

    $('#controlpanel').click(function (event) {
        var url = "controlpanel.php?node=" + $('#localnode').val();
        var windowName = "controlPanel";
        var windowSize = 'height=750, width=900';

        window.open(url, windowName, windowSize);

        event.preventDefault();
    });
      
    // Click on a cell to populate the input form
    $('table').on('click', 'td', function( event ) {
          // Shows the table ID, the text of the cell, the class of the cell and the ID of the cell.
          //console.log('clicked:', $( this ).closest('table').attr('id'), $( this ).text(), $( this ).attr('class'), $( this ).attr('id'));
          
          // shows x and y coordinates of clicked cell
          //console.log('coordinates:', 'y=' + this.cellIndex, 'x=' + this.parentNode.rowIndex);
          
          if ( $( this ).attr('class') == 'nodeNum') {
                // Put node number into id="node"
                $('#connect_form #node').val($( this ).text());
                
                  // split table ID and put node into id="localnode"
                var idarr = $( this ).closest('table').attr('id').split('_');
                $('#connect_form #localnode').val(idarr[1]);
          }  
    });
    
    // Uncomment this block to allow shift+h to show login dialog.  
    $(document).keypress(function(event) {
        if (hideLoginLink) {
            var checkWebkitandIE=(event.which==72 ? 1 : 0);
            var checkMoz=(event.which==104 && event.shiftKey ? 1 : 0);

<?php if ($_SESSION['loggedin'] !== true) { ?>

            if (checkWebkitandIE || checkMoz) {
                $("#login").dialog('open');
                return false;
            }

<?php } ?>

        }
      
    });

});
</script>
</head>
<body>
<div class="container">
<nav class="navbar navbar-dark navbar-expand-lg bg-dark">
<a class="navbar-brand" href="<?php echo $home ?>"><?php echo $site_title ?></a>
  

<?php include "menu.inc" ?>

<button type="button" class="btn btn-primary my-2 my-lg-0" data-toggle="modal" data-target="#loginModal" id="loginlink" >Login</button>
<button type="button" class="btn btn-warning" id="logoutlink">Logout</button>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
</nav>









<form method="post" action="" id="loginForm">
<div id="loginModal" class="modal hide fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-centered" role="document"> 
	<div class="modal-content">
	<div class="modal-header">
		<h5>Authorized Users Only</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="modal-body">
		<div class="form-group">
			<label for="usernameInput">Username</label>
			<input id="usernameInput" class="form-control" type="text" name="user" autocapitalize="none">
		</div>
		<div class="form-group">
			<label for="passwordInput">Password</label>
			<input id="passwordInput" class="form-control" type="password" name="password">
		</div>
	
			
	</div>
	<div class="modal-footer">
		<button type="submit" class="btn btn-primary" id="loginButton">Login</button>
	</div>
	</div>
	</div>
</div>
</form>
<?php include "menu.inc" ?>
<!-- Command response area -->
<div class="container">&nbsp;
<div id="test_area"></div>
</div>
