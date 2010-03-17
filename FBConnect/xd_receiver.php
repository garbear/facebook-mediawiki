<?php

// Cache the xd_receiver.php file
header('Cache-Control: max-age=225065900');
header('Expires:');
header('Pragma:');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<title>Cross Domain Receiver Page</title>
</head>
<body>
<?php
/*
 * This is a cross domain (XD) receiver page. It needs to be placed on your
 * domain so that the Javascript library can communicate within the iframe
 * permission model. Put it here:
 * 
 * http://www.example.com/path_to_extensions/FBConnect/xd_receiver.php
 */
?>
	<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/XdCommReceiver.js" type="text/javascript"></script>
</body>
</html>
