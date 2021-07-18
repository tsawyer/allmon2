# Allmon2-Bootstrap

Allmon2 modified to have Bootstrap 4 libraries. This is based on the great work of WD6AWP and others.

## Features
- Added Bootstrap 4 Libraries
- Removed jquery-ui and switched to Bootstrap components (modal for login)
- External links open in new tab/window by default

## TODO
- Make all libraries offline (havent changed the locations in the files but the files are local.
- Enhance login mechanism *** Can be changed in class="modal-content".
	use syntax  {style="background color:#333333;color:white" } 
	you can use any color hex value or certain color names
- ***Add extra config file/modify existing config for site-wide settings (title, color prefs, etc)*** Global.inc
- ~~Change Control Panel display~~
- Add voter formatting


## Setup

1. Change parameters in global.inc
2. Copy **allmon.ini.txt** to **allmon.ini.php** 
3. Modify **allmon.ini.php** to suit your setup
4. Point your browser to wherever you have served up this folder. Usually http://*your IP*/allmon2 
