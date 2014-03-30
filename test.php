<?php
$link = mysql_connect('198.61.225.58', 'stdtesting', 'stdt4nutr');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully';
mysql_close($link);
?>

