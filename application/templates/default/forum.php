{nest:header title="Show Forum"}

{forum}

	<h1>{title}</h1>
	<p>{description}</p>
	
	{forums}
	<div class="forum">
		<h3>{f:title}</h3>
		<p>{f:description}</p>
	</div>
	{/forums}
	{forums:empty}
		<p>There are no forums.</p>
	{/empty}
	
	{threads}
	<div class="thread">
		<h3>{t:title}</h3>
		<p>{t:description}</p>
	</div>
	{/threads}
	
{/forum}

{nest:footer}