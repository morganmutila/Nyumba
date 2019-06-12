<?php require_once("../init.php"); ?>
<?php if (!$session->isLoggedIn()) { Redirect::to("login.php?redirect=savedproperty"); } ?>

<?php


$page_title = "Saved Properties";		 

	$sql  = "SELECT DATE_FORMAT(saved.saved, '%W, %d %M') AS added, property.* FROM saved";
	$sql .= " INNER JOIN property ON ('saved.property_id' = 'property.id')";
	$sql .= " WHERE saved.user_id = ?";

	// Get all the saved properties for the user
	$properties = Property::findBySql($sql, array($session->user_id));
?>


<?php include_layout_template('header.php'); ?>

	<h2>Saved Properties(<?php echo Saved::total();?>)</h2>
	
	<?php echo output_message($message); ?>

	<?php 
	// List all the property that the user saved
		foreach ($properties as $property): ?>
			<div style=" margin:0; position: relative;">
				<img src="<?php echo $property->photo();?>"/>
				<?php
				    echo '<div style="position:absolute;top: 0;right:0;left:0;width:100%;">';
				 		if(new_listing($property->added)){
							echo "<span style=\"background-color:#11cc11;color:#fff;padding:0 .2rem;font-weight:bold;font-size:0.6rem;float:left;line-height:1rem;\">NEW</span>";
						}
						else{echo "&nbsp;";}
						echo "<span style=\"color:#fff;font-size:0.75rem;float:right;line-height:1rem;background-color: #333333b0;padding:0 .3rem;\">".time_ago($property->added)."</span>";
					echo '</div>';
				?>
			</div>
			<?php
				echo "<a href=\"listremove.php?id=$property->id&redirect=saved\">x</a><br>";
				echo "<p style=\"margin-bottom:1rem;\"><a href=\"property.php?id=$property->id\"><strong>K ".(int)$property->price."&nbsp;<small>".$property->rentTerms()."</small></strong><br>";	
				echo $property->beds   . " beds <strong>·</strong> "; 
				echo $property->baths  . " baths <strong>·</strong> ";
				echo $property->size   . " sqft<br>";
				echo $property->address."<br>";
				// echo "Saved on ".$property->saved."<br>";
				echo "<a href=\"listremove.php?id=$property->id&redirect=saved\">Share -></a></p>";

			?>
		<?php endforeach; ?>
		<?php if(!count($properties)){?>
				<div style="padding: 1rem 0.3rem;">
					You currently do not have any saved properties
					<p>Click save to add a listing to saved property</p>
				</div>
		<?php } ?>
  

<?php include_layout_template('footer.php'); ?>
		
