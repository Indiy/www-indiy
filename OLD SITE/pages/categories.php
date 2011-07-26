<? 

	$database = "[p]musicplayer_ecommerce_categories";
	if (isAdmin()) {
		$me = $_GET["artist"];
	} else {
		$me = $_SESSION["me"];
	}
	
	/* Delete Item */
	if (isset($_GET["delete"]) && $_GET["delete"] != "") {
		$getid = $_GET["id"];
		/* Remove from database */  
		mq("DELETE FROM `{$database}` WHERE `id`='$getid' and `user`='{$me}'");
	}	
	
	
	/* Add New Page */

	if (isset($_POST["submit"])) {
	
		$name = my($_POST["name"]);
		$tables = "artistid|name";
		$values = "{$me}|{$name}";

		if ($_GET["edit"] != "") {
			update($database,$tables,$values,"id",$_GET["id"]);
		} else {
			insert($database,$tables,$values);
			$newpageid = mysql_insert_id();
		}
		
		$status = "<div id='notify'>Success!</div>";
		$action = "?p=categories&artist={$me}";
	
	} else {
	
		if ($_GET["edit"] == "true") { 
			$edityo = mf(mq("select `name` from `{$database}` where `id`='{$_GET["id"]}' limit 1"));
			$e_name = nohtml($edityo["name"]);
			
			echo '
				<script type="text/javascript">
					$(document).ready(function(){
						$(".newpage").show();
					});
				</script>			
			';
			$action = "?p=categories&artist={$me}&edit=true&id={$_GET["id"]}";
		} else {
			$action = "?p=categories&artist={$me}";
			echo '
				<script type="text/javascript">
					$(document).ready(function(){
						$(".newpage").hide();
					});
				</script>			
			';
		}
		
	}


		$form = '
					<form method="post">
					
						<label>Name</label>
						<input type="text" name="name" value="'.$e_name.'" />
						<div class="clear"></div>				
						
						<div>
							<input type="submit" class="submit" name="submit" value="Save" />
						</div>
						
					</form>';




/* Load all products */
$customvideo = mq("select * from `{$database}` where `artistid`='{$me}' ORDER BY `order` ASC");

  
	
//// Include Template Design ///////////////////////

?>             
<script type="text/javascript">
	$(document).ready(function(){

		$(".addnewpage").click(function(){
			$(".newpage").slideToggle(600);
		});

		$("#response").hide();
		$(function() {
		$("ul.list").sortable({ opacity: 0.8, cursor: 'move', update: function() {
				
				var order = $(this).sortable("serialize") + '&update=update'; 
				$.post("http://chez.hi-fimedia.com/system/ajax.php", order, function(theResponse){
					$("#response").html(theResponse);
					$("#response").slideDown('slow');
					slideout();
				}); 															 
			}								  
			});
		});

	});

</script>


		<div id="content">
			<?=$successMessage;?>
			<div class="post">
				<h2 class="title"><a href="#">Categories Management</a></h2>
			
				<div class="box">

				<div class="button addnewpage">
					Add Category
				</div>
				<?=$status;?>
				<div class="newpage">
					<?=$form;?>
				</div>
				

			
				<ul class='list'>

				<?

					while ($row = mf($customvideo)) {

						$cvideo_id = $row["id"];
						$cvideo_title = $row["name"];

						echo '<li id="arrayorder_'.$cvideo_id.'" class="order">'."\n";
						echo "<span class='orderTitle'><strong>{$cvideo_title}</strong></span>"."\n";
						echo " - <a href='?p=categories&artist={$me}&edit=true&type={$e_type}&id={$cvideo_id}'>Edit</a> <a href=\"#\" class=\"xdelete\" onclick=\"confirmDelete('?p=categories&artist={$me}&delete=true&id={$cvideo_id}')\">Delete</a>"."\n";
						
						echo "</li>"."\n";
					}
				?>
			    
			</div>
			
		</div>