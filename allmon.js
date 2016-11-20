// when DOM is ready
$(document).ready(function() {

    //Set to hide Login Link
    hideLoginLink = true
    
    // Hide login link on page load?
    if (hideLoginLink) {
        $('#loginlink').hide();
    }

    // Is user authenticated
    if ($.cookie('allmon_loggedin') == 'yes') {
        $('#loginlink').hide();
    } else {
        $('#connect_form').hide();
        $('#logoutlink').hide();
    }
    
    // Login dialog
    $("#login").dialog( {
        autoOpen: false,
        title: 'Manager Login',
        modal: true,
        buttons: { "Login": function() {
            var user = $('form input:text').val();
            var passwd = $('input:password').val();
            $(this).dialog("close"); 
            $('#test_area').load("login.php", { 'user' : user, 'passwd' : passwd }, function(response) {
                if (response.substr(0,5) != 'Sorry') {
                    $('#connect_form').show();
                    $('#logoutlink').show();
                    $('#loginlink').hide();
                    $.cookie('allmon_loggedin', 'yes', { expires: 7, path: '/' });
                }
            });
            $('#test_area').stop().css('opacity', 1).fadeIn(50).delay(1000).fadeOut(2000);
        } }
    });

    // Login dialog opener
    $("#loginlink").click(function() {
        $("#login").dialog('open');
        return false;
    });
    
    // Logout 
    $('#logoutlink').click(function(event) {
        $.cookie('allmon_loggedin', null, { path: '/' });
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
        var windowSize = 'height=300, width=640';

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

            if ((checkWebkitandIE || checkMoz) && $.cookie('allmon_loggedin') != 'yes') {
                $("#login").dialog('open');
                return false;
            }
        }
      
    });

});