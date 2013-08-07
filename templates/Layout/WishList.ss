<h3>$Title</h3>
<% if MergeLists %>
<p>We've noticed there is an "Offline Wish List." Would you like us to <a href="{$Link}magiclist/">merge them together</a>?</p>
<% end_if %>
$Content
$Form
<% if WishList %>
<ul>
	<% loop WishList %>
	<li>$Title</li>
	<% end_loop %>
</ul>
<% end_if %>