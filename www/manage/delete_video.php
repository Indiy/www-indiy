<?php session_start();
 $_SESSION['tabOpen']='videolist';
?>
<form action="#" method="post">
Are you sure you want delete this video?
<br>
<input type="hidden" name="action" value="delete">
<input type="submit" value="Delete" name='YES'>
<input type="hidden" value=<?=$_REQUEST['video_id']?> name="video_id">
</form>
<form action="#" method="post">
<input type="submit" name="cancel" value="NO">
</form>