{nest:global/header title="Show Category"}

{category}

	<h1>{title}</h1>
	<p>{description}</p>
	
	{forums}
	<div class="forum">
		<h3>{f:title}</h3>
		<p>{f:description}</p>
		<a href="{url:a/b path='404'}">Here</a>
	</div>
	{/forums}
	
{/category}

{nest:global/footer}