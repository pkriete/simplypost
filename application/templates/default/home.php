{nest:global/header title="Homepage"}

<h2>Hi</h2>
<p>
	Howdy ho - content.<br />
	<a href="{backend_login}">Backend Login</a><br />
	<a href="{frontend_login}">Frontend Login</a><br />
</p>
<div id="root_content">

{root}
<div class="root_div">
	<div class="root_header">
		<h3>{title}</h3>
		<p>{description}</p>
	</div>
	
	<div class="root_children">
	{forums}
		<h6>{f:title}</h6>
	{/forums}
	</div>
</div>
{/root}

</div>

{nest:global/footer}