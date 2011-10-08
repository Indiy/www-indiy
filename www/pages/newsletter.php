<?

	if (isLoggedIn() != "true") {
		if (isAdmin() || isLabel()) {
			
		} else {
			die("You must be logged in");
		}
	}
	
	$database = "[p]musicplayer_subscribers";
	$artist = $_SESSION["me"];
	
	if ($_GET["delete"] != "") {
		$delete = $_GET["delete"];
		mq("delete from `{$database}` where `id`='{$delete}'");
	}
		
?>
				
				
				<div id="content">
					<?=$successMessage;?>
					<div class="post">
						<h2 class="title"><a href="#">Manage Subscriber List</a></h2>
						
						<ul class="subscriberlist">
							<?
								$load = mq("select * from `{$database}` where `artistid`='{$artist}' order by `id` desc");
								while ($ro = mf($load)) {
									echo "<li><div class='name'>".nohtml($ro["name"])."</div><div class='email'>".nohtml($ro["email"])."</div><div class='options'><a href=\"#\" onclick=\"confirmDelete('?p=newsletter&id=".$artist."&delete=".$ro["id"]."')\">Delete</a></div><div class='clear'></div></li>\n";
								}
							?>
							
						</ul>
						
					</div>
					<div style="clear: both;">&nbsp;</div>
				</div>
				<!-- end #content -->
				<div id="sidebar">

				</div>
				<!-- end #sidebar -->
				<div style="clear: both;">&nbsp;</div>