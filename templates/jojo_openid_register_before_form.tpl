{if $openid_url}
<div>
<p><img src="images/150/openid-logo.gif" alt="OpenID" style="float: right;" />You are currently logged into your OpenID account, <strong>{$openid_url}</strong>. Please complete the registration form as normal and once registered, your OpenID account will be attached to your new user profile.</p>
<p><a href="openid/clear/" rel="nofollow">[Clear OpenID]</a></p>
</div>
{else}
<p class="openid_link">Register using <a href="#" onclick="$('#openid_details').show('fast'); return false;">OpenID</a></p>
<div id="openid_details" style="display:none">
<h3>OpenID registration</h3>
  <form method="post" action="openid/login/">
  <p><img src="images/150/openid-logo.gif" alt="OpenID" style="float: right;" />After you have authenticated with your OpenID provider, complete the registration form as normal. Once registered, your OpenID account will be attached to your new user profile.</p>
  <label for="openid_url">OpenID URL:</label>
  <input type="hidden" name="openid_required_fields" value="fullname,email" />
  <input type="hidden" name="openid_optional_fields" value="" />
  <input type="text" name="openid_url" id="openid_url" value="{$openid_url}" size="25" />
  <input type="submit" name="submit" id="submit" value="Register with OpenID" />
  </form>
</div>
{/if}