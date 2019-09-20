{if $rejected}
	<p class="error">{lang}wcf.acp.pluginStore.authorization.credentials.rejected{/lang}</p>
{/if}

<section class="section">
	<header class="sectionHeader">
		<h2 class="sectionTitle">{lang}wcf.acp.pluginStore.authorization.credentials{/lang}</h2>
		<p class="sectionDescription">{lang}wcf.acp.pluginStore.authorization.credentials.description{/lang}</p>
	</header>
	
	<dl>
		<dt><label for="pluginStoreAuthCode">{lang}wcf.acp.pluginStore.authorization.authCode{/lang}</label></dt>
		<dd><input type="text" id="pluginStoreAuthCode" value="{$authCode}" class="long"></dd>
	</dl>
</section>

<div class="formSubmit">
	<button>{lang}wcf.global.button.submit{/lang}</button>
</div>
