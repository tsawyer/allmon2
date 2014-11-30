<div id="menu">
<?php
$var1 = explode('/', $_SERVER['REQUEST_URI']);
$var2 = array_pop($var1);
$current_url = urldecode($var2);
#$current_url = urldecode(array_pop(explode('/', $_SERVER['REQUEST_URI'])));

// Read allmon INI file
if (!file_exists('allmon.ini.php')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('allmon.ini.php', true);
#print "<pre>"; print_r($config); print "</pre>";

if (count($config) == 0) {
    die("Check ini file format.\n");
}

// Make a list of menu items
$items = array();
$i=0;
foreach($config as $name => $data) {
    if (@$data['menu'] == 1) {

        // Group nodes?
        if (empty($data['nodes'])){
            $items[$i]['url'] = "link.php?nodes=$name";
        } else {
            $items[$i]['url'] = "link.php?nodes={$data['nodes']}";
        }

        // Menu display text
        if (empty($data['text'])) {
            $items[$i]['node'] = $name;
        } else {
            $items[$i]['node'] = $data['text'];
        }
        $i++;
    } elseif (!empty($data['url'])) {
        $items[$i]['node'] = $name;
        $items[$i]['url'] = $data['url'];
        $i++;
    }
}

// Add Voter(s) if any
if (file_exists('voter.ini.php')) {
    $arr = parse_ini_file('voter.ini.php', true);

    foreach($arr as $name => $data) {
        $items[$i]['node'] = $name;
        $items[$i]['url'] = "voter.php?node={$data['node']}";
        $i++;
    }
}
?>
<ul>
<?php
foreach ($items as $item) {
    // Start a new menu div if there is a [break] in ini.
    if ($item['node'] == 'break') {
        print '</ul></div><br/><div id="menu"><ul>';
        continue;
    }
    if($current_url == $item['url']) {
        print "<li><a class=\"active\" href=\"" . $item['url'] .  "\">" . $item['node'] . "</a></li>\n";
    } else {
        print "<li><a href=\"" . $item['url'] .  "\">" . $item['node'] . "</a></li>\n";
    }
}
?>
<!-- Login opener -->
<li><a href="#" id="loginlink">Login</a></li>
<li><a href="#" id="logoutlink">Logout</a></li>

</ul>
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

<!-- Command response area -->
<div id="test_area"></div>
<?php #print "<pre>data: "; print_r($current_url); print "</pre>"; ?>
