<form action="#" method="post">
	Are you sure you want delete this song?
	<br>
	<input type="hidden" name="action" value="delete">
	<input type="submit" value="Delete" name='YES'>
	<input type="hidden" value=<?=$_REQUEST['song_id']?> name="song_id">
</form>
<form action="#" method="post">
	<input type="submit" name="cancel" value="NO">
</form>