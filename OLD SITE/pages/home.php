<? if ($_SESSION["me"] == "") { 
	die("You must be logged in");
	}
	$me = me();
?>
<? if (isAdmin() || isLabel()) { ?>
<style> .entry { display: none; } </style>
<script>
	$(document).ready(function(){
	
		$('.view').click(function(){
			$(this).next().slideToggle();
		});
	});
</script>
<? } ?>

				<div id="content">
				
					<?
						
						// Delete Artist
						if ($_GET["delete"] == "true") {
							if ($_GET["type"] == "useraudio") {
								mq("DELETE FROM `[p]musicplayer_audio` WHERE `id`='{$_GET["id"]}' and `user`='{$_GET["a"]}'");
							}
							if ($_GET["type"] == "artist") {
								mq("DELETE FROM `[p]musicplayer` WHERE `id`='{$_GET["id"]}'");
							}
							if ($_GET["type"] == "audio") {
								mq("DELETE FROM `[p]musicplayer_audio` WHERE `id`='{$_GET["id"]}' and `artistid`='{$_GET["a"]}'");
							}
							if ($_GET["type"] == "content") {
								mq("DELETE FROM `[p]musicplayer_content` WHERE `id`='{$_GET["id"]}' and `artistid`='{$_GET["a"]}'");
							}
							if ($_GET["type"] == "products") {
								mq("DELETE FROM `[p]musicplayer_ecommerce_products` WHERE `id`='{$_GET["id"]}' and `artistid`='{$_GET["a"]}'");
							}
							$successMessage = "<div id='notify'>Successfully deleted!</div>";
						}
						
						echo $successMessage;
						
						if (isAdmin()) {
							$load = mq("select * from `[p]musicplayer` where (`url` != 'admin') order by `artist` asc");
						} else if (isLabel()) {
							$load = mq("select * from `[p]musicplayer` where `root` = '{$_SESSION["me"]}' order by `artist` asc");
						} else if ($_SESSION["me"]) {
							$load = mq("select * from `[p]musicplayer` where (`id` = '{$_SESSION["me"]}') limit 1");
						} else {
							refresh("0","?p=index");
						}
						while ($row = mf($load)) {
							$pageList = "";
							$musicList = "";
							$productsList = "";
							$artist_id = $row["id"];
							$artist_artist = stripslashes($row["artist"]);
							$artist_website = $row["website"];
							$artist_twitter = $row["twitter"];
							$artist_facebook = $row["facebook"];
							$artist_appid = $row["appid"];
							$artist_url = $row["url"];
							$artist_type = $row["type"];
							$artist_root = $row["root"];
							
							if (isFan()) {
								$loada = mq("select `id`,`artist` from `[p]musicplayer` where `type`='0' order by `artist` asc");
								while ($rowa = mf($loada)) {
									$artistList .= '<li class="order"><a href="#" class="viewartist">'.nohtml($rowa["artist"]).'</a></li>';
								}
								
								// Build Music List
								$loadmusic = mq("select `id`,`name`,`artistid`,`order` from `[p]musicplayer_audio` where `user`='{$artist_id}' and `type`='1' order by `order` asc, `id` desc");
								while ($music = mf($loadmusic)) {
									$music_id = $music["id"];
									$music_name = stripslashes($music["name"]);
									$musicList .= '<li id="arrayorder_'.$music_id.'" class="order"><a href="#" onclick="confirmDelete(\'?p=home&delete=true&type=useraudio&a='.$me.'&id='.$music_id.'\')">'.$music_name.'</a></li>'."\n";
								}
								
							} else {					
								if ($artist_type == "3") {
									// Build Artist List
									$loadartist = mq("select `id`,`artist` from `[p]musicplayer` where `root`='{$artist_id}' and `type`='0'");
									while ($music = mf($loadartist)) {
										$art_id = $music["id"];
										$art_name = stripslashes($music["artist"]);
										$musicList .= '<li id="arrayorder_'.$music_id.'" class="order"><a href="?p=addartist&id='.$art_id.'">'.$art_name.'</a></li>'."\n";
									}
								} else {
							
									// Build Page List
									$loadpages = mq("select `id`,`name`,`artistid`,`order` from `[p]musicplayer_content` where `artistid`='{$artist_id}' order by `order` asc, `id` desc");
									while ($pages = mf($loadpages)) {
										$page_id = $pages["id"];
										$page_name = stripslashes($pages["name"]);
										$pageList .= '<li id="arrayorder_'.$page_id.'" class="order"><a href="?p=addcontent&artist='.$artist_id.'&id='.$page_id.'">'.$page_name.'</a></li>'."\n";
									}
									
									// Build Music List
									$loadmusic = mq("select `id`,`name`,`artistid`,`order` from `[p]musicplayer_audio` where `artistid`='{$artist_id}' and `type`='0' order by `order` asc, `id` desc");
									while ($music = mf($loadmusic)) {
										$music_id = $music["id"];
										$music_name = stripslashes($music["name"]);
										$musicList .= '<li id="arrayorder_'.$music_id.'" class="order"><a href="?p=addmusic&artist='.$artist_id.'&id='.$music_id.'">'.$music_name.'</a></li>'."\n";
									}
									
									// Build Products List
									$loadpro = mq("select `id`,`name`,`artistid`,`order` from `[p]musicplayer_ecommerce_products` where `artistid`='{$artist_id}' order by `order` asc, `id` desc");
									while ($pro = mf($loadpro)) {
										$product_id = $pro["id"];
										$product_name = stripslashes($pro["name"]);
										$productsList .= '<li id="arrayorder_'.$product_id.'" class="order"><a href="?p=addproduct&artist='.$artist_id.'&id='.$product_id.'">'.$product_name.'</a></li>'."\n";
									}
								}
							}
							
							echo '
								<div class="post">';
								if (isAdmin() || isLabel()) {
									echo '
									<div class="view">'.$artist_artist.'</div>';
								}
								if ($artist_type == "3") {
									echo '
										<div class="entry">
											<div class="entryfloat">
												<p><a href="" class="title">'.$artist_artist.'</a></p>
												<hr />
												<strong>Music Player &raquo;</strong>
												<br />
												<a href="?p=addlabel&id='.$artist_id.'">Edit</a> ';
												if (isAdmin()) {
													echo '
													| <a href="#" class="xdelete" onclick="confirmDelete(\'?p=home&delete=true&type=artist&id='.$artist_id.'\')">Delete</a> ';
												}
												echo'
												<hr />
											</div>
											';
											
												echo '
												<div class="entryfloat smaller">
													<strong>Artists</strong> <a href="?p=addartist&label='.$artist_id.'"><img src="pages/images/add.png" border="0" alt="Add" /></a><hr />
													<ul>
													'.$musicList.'
													</ul>
												</div>';
											
											
											echo '
											<div class="clear"></div>
										</div>
										';
								} else {
									echo '
										<div class="entry">
											<div class="entryfloat">
												<p><a href="http://'.$artist_url.'.myartistdna.fm" class="title">'.$artist_artist.'</a></p>
												<hr />
												<strong>Music Player &raquo;</strong>
												<br />
												<a href="?p=addartist&id='.$artist_id.'">Edit</a> | 
												<a href="?p=stats&id='.$artist_id.'">Analytics</a>';
												if (!isFan()) { 
												echo ' | 
												<a href="?p=addmusic&artist='.$artist_id.'">Add Music</a> | 
												<a href="?p=addcontent&artist='.$artist_id.'">Add Pages</a>';
												}
												if (isAdmin() || isLabel()) {
													echo '
													| <a href="#" class="xdelete" onclick="confirmDelete(\'?p=home&delete=true&type=artist&id='.$artist_id.'\')">Delete</a> ';
												}
												if (!isFan()) {
												echo '
												<hr />
												<strong>Ecommerce &raquo;</strong><br />
												<a href="?p=store&artist='.$artist_id.'">Settings</a> | 
												<a href="?p=addproduct&artist='.$artist_id.'">Add Product</a>
												';
												}
												echo'
												<hr />
												<p><strong>Embed Code &raquo;</strong></p>
												<textarea class="textarea dash"><iframe src="'.playerUrl().$artist_url.'&embed=true" border="0" width="400" height="600" frameborder="0" name="'.$artist_url.'"></iframe></textarea>
											</div>
											';
											
											if (isFan()) { 
												echo '
												<div class="entryfloat smaller">
													<strong>Artists</strong><hr />
													<ul>
													'.$artistList.'
													</ul>
												</div>
												<div class="entryfloat smaller">
													<strong>Tracks</strong><hr />
													<ul class="listtracks">
													<small><em>Select an Artist to see full list of tracks.</em></small>
													</ul>
												</div>
												<div class="entryfloat smaller">
													<strong>Playlist</strong><hr />
													<ul class="playlist tracks">
													'.$musicList.'
													</ul>
												</div>';
											} else { 
												echo '
												<div class="entryfloat smaller">
													<strong>Playlist</strong> <a href="?p=addmusic&artist='.$artist_id.'"><img src="pages/images/add.png" border="0" alt="Add" /></a><hr />
													<ul class="playlist">
													'.$musicList.'
													</ul>
												</div>
												<div class="entryfloat smaller">
													<strong>Pages</strong> <a href="?p=addcontent&artist='.$artist_id.'"><img src="pages/images/add.png" border="0" alt="Add" /></a><hr />
													<ul class="pages">
													'.$pageList.'
													</ul>
												</div>
												<div class="entryfloat smaller">
													<strong>Products</strong> <a href="?p=addproduct&artist='.$artist_id.'"><img src="pages/images/add.png" border="0" alt="Add" /></a><hr />
													<ul class="products">
													'.$productsList.'
													</ul>
												</div>';
											}
											
											echo '
											<div class="clear"></div>
										</div>
										';
								}
										echo '
									</div>
								';
						}
					?>

					<div style="clear: both;">&nbsp;</div>