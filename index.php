<?php 
// Read configuration file
if (!file_exists('allmon.ini.php')) {
    die("Couldn't load ini file.\n");
}
$config = parse_ini_file('allmon.ini.php', true);

// Get first node in INI file
$node = array_shift(array_keys($config));
if ($config[$node]['voter'] == 1) {
    $url = "voter.php?node=" . $node;
} else {
    $url = "link.php?nodes=" . $node;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Allstar Monitor</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="REFRESH" content="0;url=<?php echo $url; ?>">
</head>
<body>
<p>Redirecting to <?php echo $url; ?></p>
</body>
</html>