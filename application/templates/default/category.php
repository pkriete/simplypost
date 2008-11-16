{nest:global/header title="Show Category"}

<div id="breadcrumb">
<a href="{base_url}">Home</a> &rsaquo;
{category}
	{parents}<a href="{url:{p:node_id} path='{p:node_type}'}">{p:title}</a> &rsaquo; {/parents} {title}
{/category}
</div>

{category}

	<h1>{title}</h1>
	<p>{description}</p>
	
	{forums}

	<div class="forum">
		<h3>{f:title}</h3>
		<p>{f:description}</p>

		<a href="{url:{f:node_id} path='forum'}">Here</a>
	</div>
	{/forums}
	
{/category}

{nest:global/footer}