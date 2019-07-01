<?php 
include '../init.php';
$session->comfirm_logged_in("login.php?redirect=savedproperty");

$saved_sql  = "SELECT DATE_FORMAT(saved, '%W, %D %M') AS saved, property_id FROM saved";
$saved_sql .= " WHERE user_id = ? ORDER BY id DESC";

// Get all the saved properties for the user
$favourites = Saved::findBySql($saved_sql, array($user->id));

$page_title = "Saved Properties";	

?>

<?php include_layout_template('header.php'); ?>

	<h2>Saved Properties</h2>

	<?php echo output_message($message); ?>
	
	<div class="properties">
		<?php 
		// List all the property that the user saved
			foreach ($favourites as $fav): ?>			
			<?php
				$sql = "SELECT * FROM property WHERE id = ?";
				$properties = Property::findBySql($sql, array($fav->property_id));
				foreach ($properties as $property): ?>
				<div class="listing" style="border:none;padding:.5rem">
					<div style=" margin:.5rem .7rem 0 0;position:relative;margin-bottom:1rem;float:left;">
						<img src="<?php echo $property->photo();?>", style="width:100px;"/>
						<?php
						    echo '<div style="position:absolute;top: 0;right:0;left:0;width:100%;">';
							 	echo "<div style=\"float:left\">";
								echo "</div>";	
								if(isset($user)){
								echo "<a href=\"list_unsave.php?id=$property->id\" style=\"float:left;padding:.3rem;color:#1db954;\"><i class=\"mdi mdi-heart mdi-24px\"></i></a>";
								}
							echo '</div>';
						?>
					</div>
					<div style="padding: .5rem;">	
					<?php				  
						echo "<div style=\"letter-spacing: 0.02rem;\">".amount_format($property->price)."&nbsp;<small>".$property->terms()."</small>";
						echo  ($property->negotiable == true) ? "<small style=\"color:#11cc11;\">NG</small>" : "";
						echo "<span style=\"float:right;\">".$property->priceCut()."</span></div>";
						echo "<div style=\"font-size:.8rem;\">";
						echo $property->beds    . " bd · ";
						echo $property->baths   . " ba · ";
						echo number_format($property->size)    . " Sqft";
						echo "</div>";
						echo "<div><a href=\"property.php?id={$property->id}\" style=\"color:#777;font-size:.85rem;\">";
						echo "For ".ucfirst($property->market)." in ". $property->Location();
						echo "</a><br>";
						echo "<small>Saved on ".$fav->saved."</small></div>";
					?>
					</div>
				</div>
			<?php endforeach ?>

		<?php endforeach ?>
		<?php if(!count($favourites)){?>
				<div style="padding: 1rem 0.3rem;">
					You haven't saved any properties yet.
					<p>Click save to add a listing to saved property</p>
				</div>
		<?php } ?>
  
	</div>

<?php include_layout_template('footer.php'); ?>
		
