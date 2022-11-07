{if $workflow == 'standard' }
	<div class="laybuy-inline-widget">
	or 6 weekly interest-free payments from <strong>{$amount}</strong> with <img style = "{$attrs['style']}" src="{$logo_url}" alt = "Laybuy" />
	</div>
{/if}

{if $workflow == 'pay_today' }
	<div class="laybuy-inline-widget">
		or from <strong>{$pay_today}</strong> today & 5 weekly interest-free payments of <strong>{$amount}</strong> with <img style = "{$attrs['style']}" src="{$logo_url}" alt = "Laybuy" />
	</div>
{/if}