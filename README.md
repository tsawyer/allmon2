# Allmon2

## About
Allmon2 is a web site for managing one or more app_rpt (aka Allstar) nodes.
Each managed local node shows a list of connected nodes. The list is
sorted in reverse order of the most recently received node. So the last
node to talk is always at the top of the list. Any node that is currently
being received will be highlighted by a green background as well as moving
to the top of the list. The node list is updated once a second giving near-
real-time status.

Nodes may be grouped together when configuring Allmon. Groups enable two
or mode nodes to be displayed on the same page. This can be handy if you
use devices with smaller screens or you want to see a couple of your nodes
at the same time. Don't try to display more than a few per group as performance
will suffer.

Logged in users can perform connect and disconnects. Users are maintained
with the Apache htpasswd utility. There is only one login level, admin.

Allmon2 will also monitor VOTER clients. Each VOTER instance displays a list
attached Radio Thin Client Modules (RTCM). The RSSI for each RTCM is displayed
in bar graph style along with a color to indicate the currently voted RTCM.

## Installation

### Prerequisities
- Apache, including the **htpasswd** utility.
- PHP 5.2 or above
- Latest version of Asterisk preferred for best functionality

If you installed your node(s) from the Allstar web portal and never touched the command line, you have a fairly steep learning curve ahead of you. On the other hand, if you know Apache and Linux then installation should be a piece of cake.

The web server can be local to your node or on a stand alone server. If you are concerned about performance of your node use a stand alone server. Also, you will be able to view two or mode of your nodes (even if on separate app_rpt servers). The Asterisk Manager port 5038 (or another of your choosing) must be open towards your web server.

### Basic Installation

1. Put these files somewhere in your web servers document tree. That can be the docroot or any subdir you like.
2. Copy allmon.ini.txt to allmon.ini.php and then edit it for your node(s) information. The user ID and passwords you enter here are the one you will
   use in manager.conf on your node server(s).
3. Optionally copy voter.ini.txt to voter.ini.php and edit with your voter info.

4. Modify **header.inc** and change the variable `$site_title` to whatever you would like.
5. Create your .htpasswd file for the admin user(s). In your web server directory, on the command line execute:
```
htpasswd -c .htpasswd username
```
Some systems will need the -d option to force crypr() encryption needed by php

6. Edit /etc/asterisk/manager.conf

7. Mark astdb.php executable and add to cron. **Run just once a day please.**
```
01 03 * * * cd /var/www/html/allmon2; ./astdb.php
```

8. If you have private nodes rename the privatenodes.sample.txt to privatenodes.txt and edit it with your information. The line with `NODE|CALL|INFO|LOCATION` can be removed. It's there to show the format only.

9. Edit controlpanel.ini.php for your desired commands. Be sure to keep the labels[] and the cmds[] tags in assoicated pairs. 

### Allstar Database
 - If you don't have the Allstar "database" (really just a text file) the
   Node Information column will only show the IP address of the remote nodes.
 - ACID and Limey users should run the astdb.php script to get an initial 
   copy of the database. It should be periodically updated with the
   astdb.php script. Please set your cron job to a reasonable time, like 
   once a day and manually run when you need the occasional adhoc update.
 - The Beagle Bone Black already has a daily updated astdb.txt file. BBB
   users should add a symbolic link to that file with 
   ```
   ln -s /var/log/asterisk/astdb.txt astdb.txt
   ```

## Known Bugs
 - If you have only a group and no individual nodes displayed (menu=yes)
   index.php will not redirect to the group.

## Updates
  - 2012/12/06 Private nodes may be appended to astdb.txt
  - 2012/12/10 Fixed groups where more than one group is used
  - 2012/12/17 Menu items may be added to allmon.ini
  - 2013/04/19 Changes to Voter Menu, see github for details.
  - 2014/04/05 Allmon II branch
    - Internet Explorer is no longer supported. But every other modern
      browser on earth works fine.
    - Uses HTML 5 sever-sent events. SSE replaces JavaScript long poling to
      increase update frequency and reduce the load on Asterisk. IE does
      not support SSE.
    - The way the node list is updated has changed because sometimes it
      took 5 or 6 clicks before a response. That’s been solved by on-demand
      updating parts of the table when the data changes.
    - The header rows (with grey background) only update when the page is
      first loaded.
    - The time columns update every second or so.
    - Everything else updates only when something other than the time
      columns change.
    - Clicks now work first time - every time.
    - Other UI improvements.
    - Groups are now handled with the nodes=x,x,x key/value in the node
      stanza of allmon.ini.php. Groups.ini is no longer used.
    - Voter display is a selected node rather than all nodes on the server.
  - 2014/04/13 Updated README
  - 2014/04/22 Lots of code changes to make this few things hapening:
    - Bad login to a server should not prevent sucessfully logged in
      node display
    - Connecting to AMI, connection failed and login failed status
      messages now show
    - Show "No Connections." when there aren't any.
    - astdb.php won't loop forever when file is not found
  - 2014/04/24 Added the Control Panel 
    - See controlpanel.ini.txt to set up. 
  - 2014/11/30 Bringing git repository up to date.
    - Added astdb.txt changes to README. 
    - astdb.txt is no longer distributed with Allmon for BBB 
      compatibility. ACID/Limey users just run astdb.php as before.
    - Updated .ini.txt sample files.
    - menu.php has a bit more flexibility.
    - Better compatibility with private systems.
    - Added more example control panel commands.
  - 2016/07/07 Added favicon. Thanks KC9ONA
  - 2016/07/27 Taged version 2.1 - Changes Navbar to CSS Dropdown Style Menu
    - !!!NOTICE!!! !!!WARNING!!! User changes are to allmon.ini.php are 
       required to continue hiding hidden menus. Default behavior is to show
       menus items unless hidden with the new INI directive nomenu=yes.
    - Other than hidden menus, existing items should continue to function 
       as before. 
    - The INI [Break] stanza is no longer used. It's not needed in the 
       dropdown context. 
    - Bookmarks should work as before 2.1. A browser refresh may be required
       (due to CSS cashing) to corectly display the menu the first time in.
    - The login/logout link has moved above (and out of the way of) the Navbar. 
    - New INI directive "system=<name>" creates and/or adds item to dropdown named. 
       See sample allmon.ini.txt
    - The about.php page has been deleted and it's text moved to the new home 
       page. 
    - The site title is now a link to the new home page. 
  - 2016/12/18
    - Can show more than one RTCM at a time i.e. link.php?nodes=1111,1112
        see example in allmon.ini.txt
    - Fixed INI hideNodeURL=yes setting.
  - 2017/08/29
    - Please see git comments for updates henceforth.
  - 2018/02/02
    - Security update. PHP session replaces login cookies.
    - See release notes from 2016/07/27 regarding nomenu=
    - Thanks to KB4FXC, M0NFI, KN2R & WA3DSP
  - 2018/02/05
    - Thanks to coolacid for the X-Accel-Buffering pr
    - Also added X-Accel-Buffering to voterserver.php
  - 2020/03/15
    - Created Bootstrap version/branch, migrated to jQuery 3.4.1 and removed jQuery-ui dependancy.
