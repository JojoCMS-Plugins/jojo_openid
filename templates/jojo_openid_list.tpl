{if $loggedIn}
<p>The following OpenIDs are attached to your user account. You can remove an OpenID using the links below.</p>
<ul>
{section name=o loop=$openids}
<li>
  <a href="{$openids[o]}" target="_BLANK" rel="nofollow">{$openids[o]}</a>
  <form style="border: 0; background: none; display:inline" method="post" action="openid/delete/">
  <input type="hidden" name="openid_url" value="{$openids[o]}" />
  <input type="image" src="images/cms/icons/delete.png" title="Delete this OpenID from your account" />
  </form>
</li>
{sectionelse}
<li>There are no OpenIDs attached to your account.</li>
{/section}
</ul>

<div>
  <h3>Attach an OpenID to your account</h3>
  <form method="post" action="openid/attach/">
    <label for="openid_url">OpenID URL:</label>
    <input type="text" name="openid_url" id="openid_url" value="{$openid_url}" size="40" />
    <input type="submit" name="submit" id="submit" value="Attach OpenID" />
  </form>
</div>

{else}
Please <a href="login/" rel="nofollow">login</a> to view your OpenIDs.
{/if}