{nest:global/header title="Homepage"}

<div id="breadcrumb">
	Home
</div>

<div id="info_block">
<p>
	<a href="{backend_login}">Backend Login</a><br />
	<a href="{frontend_login}">Frontend Login</a><br />
</p>
</div>

<div id="root_content">

{root}
<div class="root_div">
	<div class="root_header">
		<h3>{title} <small><a href="{url:{node_id} path='category'}">[view]</a></small></h3>
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