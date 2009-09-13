<p class="openid_link">Login using <a href="#" onclick="$('#openid_details').show('fast'); return false;">OpenID</a></p>
<div id="openid_details" style="display:none">
  <form method="post" action="openid/login/">
  <label for="openid_url">OpenID URL:</label>
  <input type="text" name="openid_url" id="openid_url" value="{$openid_url}" size="25" />
  <input type="submit" name="submit" id="submit" value="Login with OpenID" />
  </form>
</div>