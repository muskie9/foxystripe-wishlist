<h3>$Title</h3>
$Content
$Form
<% if WishList %>
<ul>
	<% loop WishList %>
	<li>$Title</li>
	<% end_loop %>
</ul>
<% end_if %>