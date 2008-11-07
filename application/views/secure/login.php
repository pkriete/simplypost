<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title>Secure Login</title>

	<!-- Meta Tags -->
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="Pascal Kriete" />
	<meta name="copyright" content="Pascal Kriete" />

	<meta name="keywords" content="Key, words" />
	<meta name="description" content="Description" />
</head>

<body>

<h1>Please Log In</h1>

<p><?php echo validation_errors(); ?></p>

<?php echo form_open_safe( current_url() ); ?>

<p>
	<?php echo lang('login_username', 'username'); ?>
	<?php echo form_input('username'); ?>
</p>
<p>
	<?php echo lang('login_password', 'password'); ?>
	<?php echo form_password('password')?>
</p>
<p>
	<?php echo form_submit('submit', 'Submit'); ?>
<?php echo form_close(); ?>

</body>
</html>