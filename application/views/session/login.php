<h2>Login</h2>

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