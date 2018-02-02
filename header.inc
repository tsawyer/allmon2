<?php
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
<link type="text/css" rel="stylesheet" href="allmon.css">
<link type="text/css" rel="stylesheet" href="jquery-ui.css">
<script src="jquery.min.js"></script>
<script src="jquery-ui.min.js"></script>
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
    $("#login").dialog( {
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
        $("#login").dialog('open');
        return false;
    });
    
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
<div id="header">
<div id="headerTitle"><a href="<?php echo $home ?>">Allstar Monitor II</a></div>
<div id="headerTag">
Monitoring the World One Node at a Time
</div>
<!-- Login opener -->
<div id="headerLogin">
<a href="#" id="loginlink">Login</a>
<a href="#" id="logoutlink">Logout</a>
</div>
<div id="headerImg"><img src="allstarLogo.png" alt="Allstar Logo"></div>
</div>
<div class="clearer"></div>
<!-- Login form -->
<div id="login">
<div>
<form method="post" action="">
<table>
<tr><td>Username:</td><td><input style="width: 150px;" type="text" name="user" autocapitalize="none"></td></tr>
<tr><td>Password:</td><td><input style="width: 150px;" type="password" name="password"></td></tr>
</table>
</form>
</div>
</div>
<?php include "menu.inc" ?>
<!-- Command response area -->
<div id="test_area"></div>
