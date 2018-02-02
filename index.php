<?php include "session.inc"; ?>
<?php include "header.inc"; ?>
<p>
Allmon is a web site for monitoring and managing ham radio 
<a href="http://allstarlink.org" target="_blank">Allstar</a> and <a href="http://ohnosec.org/drupal/" target="_blank">app_rpt</a>
node linking and <a href="http://micro-node.com/thin-m1.html" target="_blank">RTCM clients</a>. 
This is version 2.1 of Allmon which has a number of internal and UI improvements. (see changes below)
</p>
<p>
On the menu bar click on the node numbers to see, and manage if you have a login ID, each local node. 
These pages dynamically display any remote nodes that are connected to it. 
When a signal is received the remote node will move to the top of the list and will have a green background. 
The most recently received nodes will always be at the top of the list. 
<ul>
<li>
The <b>Direction</b> column shows IN when another node connected to us and OUT if the connection was made from us. 
</li>
<li>
The <b>Mode</b> column will show Transceive when this node will transmit and receive to/from the connected node. It will show Rx only if this node only receives from the connected node.
</li>
</ul>
</p>
<p>
Any Voter pages will show RTCM receiver details. The bars will move in near-real-time as the signal strength varies. 
The voted receiver will turn green indicating that it is being repeated.
The numbers are the relative signal strength indicator, RSSI. The value ranges from 0 to 255, a range of approximately 30db.
A value of zero means that no signal is being selected. 
The color of the bars indicate the type of RTCM client as shown on the key below the voter display.
</p>
<p>Please feel free to <a href="https://github.com/tsawyer/allmon2">download Allmon2</a> for your own site. Enjoy!</p>

Version 2 changes:
<ul>
<li>Uses HTML 5 sever-sent events. SSE replaces JavaScript long poling to increase update frequency and reduce the load on Asterisk.
	Unfortunately, IE is not supported.
<li>UI improvements. 
<li>Groups are now handled with the nodes=x,x,x key/value in the node stanza of allmon.ini. Groups.ini is no longer used.
<li>Voter display is a selected node rather than all nodes on the server.
</ul>

Version 2.1:
<ul>
    <li>The primary new feature of Allmon 2.1 is the addition of dropdown menus. My Allmon menu was getting was getting out of control
        due to manageing more and more clients. Dropdowns organize your menu items (usually nodes and RTCMs) by system. 
        For example you might have a Los Angles, a Las Vegas, a San Francisco and a New York system. 
        Or you could put your nodes in one system, your RTCMs in another system and your hubs in yet another.</li>
    <li>Dropdowns are organized by the system= directive within allmon.ini.php. Any items with no system= directive 
        will be shown on the navbar, as in v2. </li>
    <li>The INI file format has changed slightly: 
        <ul>
            <li>Added the system= directive as mentioned above.</li>
            <li>RTCM's are now placed in the allmon INI with the rtcmnode= directive.</li>
            <li>The INI [break] stanza is non-operational and is ignored.</li> 
            <li>The menu=no directive is now nomenu=yes (it's an INI quirk).</li> 
            <li>Updated allmon.ini.txt to refelect these changes.</li> 
        </ul>
    </li>
    <li>The voter INI file (voter.ini.php) is no longer used and will be ignored if it exists.</li>
    <li>The login/logout link has been moved above the navbar and below the tag line.</li>
    <li>A click on the page title will fetch the Allmon index page.</li> 
    <li>The about page text has been moved to the index page.</li>
</ul>
<?php include "footer.inc"; ?>
