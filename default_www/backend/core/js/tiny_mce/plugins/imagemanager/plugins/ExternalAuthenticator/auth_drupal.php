<?php
	require_once('./includes/bootstrap.inc');
	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

	// Change this secret key so it matches the one in the imagemanager/filemanager config
	$secretKey = "someSecretKey";

	// Check if user is valid or not
	if (!user_access('access tinymce imagemanager') && !user_access('access tinymce filemanager'))
		die("Sorry, you don't have access to the imagemanager/filemanager.");

	// Override any config values here
	$config = array();

	// Uncomment this one to create user specific file areas. Change the path if needed
	// $config['filesystem.rootpath'] = realpath('./sites/all/files') . '/' . $user->uid;

	// Generates a unique key of the config values with the secret key
	$key = md5(implode('', array_values($config)) . $secretKey);
?>

<html>
<body onload="document.forms[0].submit();">
<form method="post" action="<?php echo htmlentities($_GET['return_url']); ?>">
<input type="hidden" name="key" value="<?php echo htmlentities($key); ?>" />
<?php
	foreach ($config as $key => $value) {
		echo '<input type="hidden" name="' . htmlentities(str_replace('.', '__', $key)) . '" value="' . htmlentities($value) . '" />';
	}
?>
</form>
</body>
</html>