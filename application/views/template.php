<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<title><?= $title ?></title>

	<!-- Meta Tags -->
	<?php echo meta(array('name' => 'Content-type', 'content' => 'text/html; charset=utf-8', 'type' => 'equiv')); ?>
	<?php echo meta('author', 'Pascal Kriete'); ?>
	<?php echo meta('copyright', 'Pascal Kriete'); ?>

	<meta name="keywords" content="Key, words" />
	<meta name="description" content="Description" />
	
	<!-- Stylesheets -->
	<?php echo link_tag( backend_url('assets/css/grid/reset') )."\n"; ?>
	<?php echo link_tag( backend_url('assets/css/grid/960') )."\n"; ?>
	<?php echo link_tag( backend_url('assets/css/grid/text') )."\n"; ?>
	<?php echo link_tag( backend_url('assets/css/site') )."\n"; ?>
	
</head>

<body>
	
<div class="container_12" id="header">
	<div class="grid_3" id="logo">
		<h1>SimplyPost</h1>
	</div>

	<div class="grid_3 prefix_5">
		<div id="session">
			<div id="session_user"><?php echo current_user('username'); ?> <span id="user_group">[Administrator]</div>
			
			<div id="session_links">
				<?php echo anchor( backend_url('member/account'), 'Account'); ?> |
				<?php echo anchor( backend_url('member/profile'), 'Profile'); ?> |
				<?php echo anchor( backend_url('session/logout'), 'Logout', array('id' => 'logout')); ?>
			</div>
			
			<div class="clear">&nbsp;</div>
		</div>
	</div>
	<div class="clear">&nbsp;</div>
	
</div>
<!-- End Header -->
<div class="clear">&nbsp;</div>



<div class="container_16" id="wrapper">

<div class="grid_16 shadow">&nbsp;</div>

<div class="grid_12">
	<div id="content">
		<?php $this->load->view($content); ?>
	</div>
</div>
<!-- End Content -->

<div class="grid_4">
	<div id="sidebar">
		<ul>
			<li>
				<a class="<?php echo $section=='home' ? 'selected' : ''?>" href="<?php echo site_url(backend_url('')); ?>">
					<div class="icon" id="home_icon"></div>
					Home
					<p>Get Started Here</p>
				</a>
			</li>
			<li>
				<a class="<?php echo $section=='content' ? 'selected' : ''?>" href="<?php echo site_url(backend_url('content')); ?>">
					<div class="icon" id="content_icon"></div>
					Content
					<p>Styles, Permissions, etc</p>
				</a>
			</li>
			<li>
				<a class="<?php echo $section=='members' ? 'selected' : ''?>" href="<?php echo site_url(backend_url('members')); ?>">
					<div class="icon" id="member_icon"></div>
					Members
					<p>Create, Ban, Delete</p>
				</a>
			</li>
			<li>
				<a class="<?php echo $section=='statistics' ? 'selected' : ''?>" href="<?php echo site_url(backend_url('statistics')); ?>">
					<div class="icon" id="statistics_icon"></div>
					Statistics
					<p>Visitors, Posts, Page Views</p>
				</a>
			</li>
		</ul>
	</div>
</div>
<!-- End Sidebar -->
<div class="clear">&nbsp;</div>

</div>
<!-- End Wrapper -->
<div class="clear">&nbsp;</div>

</div>
<!-- End Container -->
</body>
</html>