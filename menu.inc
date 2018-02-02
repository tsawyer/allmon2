<?php
// Read allmon INI file
$iniFile = 'allmon.ini.php';
if (!file_exists($iniFile)) {
    die("Couldn't load ini file: $iniFile");
}
$config = parse_ini_file($iniFile, true);
#print "<pre>"; print_r($config); print "</pre>";

if (count($config) == 0) {
    die("Check ini file format");
}

// Make a list of menu items
$systems = array();
foreach($config as $name => $data) {
    
    // Don't show nomenu items
    if(isset($data['nomenu']) and $data['nomenu'] == 1) {
        continue;
    }
    
    // Breaks don't show as a menu item
    if(strtolower($name) == 'break') {
        continue;
    }
    
    // Group menus by system
    $sysName = 'MainNavBar';
    if (isset($data['system'])) {
        $sysName=$data['system'];
    }
    
    // URL: Use 'url', 'rtcmnode', 'nodes', or name
    if (isset($data['url'])) {
        $systems[$sysName][$name]['url'] = $data['url'];
    } elseif(isset($data['rtcmnode'])) {
        $systems[$sysName][$name]['url'] = "voter.php?node={$data['rtcmnode']}";
    } elseif (isset($data['nodes'])) {
        $systems[$sysName][$name]['url'] = "link.php?nodes={$data['nodes']}";
    } else {
        $systems[$sysName][$name]['url'] = "link.php?nodes=$name";
    }
}
//print '<pre>'; print_r($systems); print '</pre>';

// Output menu 
$outBuf = "<div id=\"menu\">\n";
$outBuf .= "<ul>";
foreach ($systems as $sysName => $items) {
    if ($sysName == "MainNavBar") {
        // $itemName = key($items);
        // $url =  $items[$itemName]['url'];
        // $outBuf .= "<li><a href=\"$url\">$itemName</a></li>\n";
        $outBuf .= "<li>";
        foreach($items as $itemName => $itemValue) {
            $outBuf .= "  <a href=\"" . $itemValue['url'] .  "\">$itemName</a>";
        }
        $outBuf .= "</li>";
    } else {
        $outBuf .= "<li class=\"dropdown\">\n";
        $outBuf .= "<a href=\"#\" class=\"dropbtn\">$sysName</a>\n";
        $outBuf .= "<div class=\"dropdown-content\">\n";
        foreach($items as $itemName => $itemValue) {
            $outBuf .= "  <a href=\"" . $itemValue['url'] .  "\">$itemName</a>\n";
        }
        $outBuf .= "</div>\n</li>\n";
    }
}

$outBuf .= "</ul>\n</div>\n";
$outBuf .= "<div class=\"clearer\"></div>";
print $outBuf;

//print '<pre>'; print_r($outBuf); print '</pre>';
?>
