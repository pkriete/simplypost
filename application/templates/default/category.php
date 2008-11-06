{nest:header title="Show Category"}

{category}

	<h1>{c:title}</h1>
	<p>{c:description}</p>
	
	{forums}
	<div class="forum">
		<h3>{f:title}</h3>
		<p>{f:description}</p>
	</div>
	{/forums}
	
{/category}

{nest:footer}