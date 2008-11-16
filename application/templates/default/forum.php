{nest:global/header title="Show Forum"}

<div id="breadcrumb">
<a href="{base_url}">Home</a> &rsaquo;
{forum}
	{parents}<a href="{url:{p:node_id} path='{p:node_type}'}">{p:title}</a> &rsaquo; {/parents} {title}
{/forum}
</div>

{forum}

	<h1>{title}</h1>
	<p>{description}</p>
	
	{forums}
	<div class="forum">
		<h3>{f:title}</h3>
		<p>{f:description}</p>
	</div>
	{f:empty}<p>There are no forums.</p>{/empty}
	{/forums}
	
	{threads}
	<div class="thread">
		<h3>{t:title}</h3>
		<p>{t:description}</p>
	</div>
	{t:empty}<p>No threads.</p>{/empty}
	{/threads}
	
{/forum}

{nest:global/footer}