<?php 
include '../init.php';
$session->comfirm_logged_in("login.php");

// Get all the property for the user
$sql = "SELECT * FROM property WHERE user_id = ? AND status >= ?";
$properties = Property::findBySql($sql, array($user->id, 1));

if(count($properties) == 0){
    Redirect::home();   
}

$page_title = "My Listings";
?>

<?php include_layout_template('header.php'); ?>

	<h2>My Listings&nbsp;&nbsp;<small style="font-weight: normal;"><a href="add.php">+ Add property</a></small></h2>
	
	<?php echo output_message($message); ?>

	<div class="properties">
		<?php 
			foreach ($properties as $property): ?>
				<?php if($property->status <= 1): ?>

					<div class="listing" style="border:none;padding:.5rem">
						<?php				  
							echo "<div style=\"color:#777;font-size:.85rem;\">";
							echo "For ".ucfirst($property->market)." ".$property->address." in ". $property->Location();
							echo "</div>";
							echo "<p>20% complete</p>";

							echo "<a href=\"list.php?id={$property->id}\"><button type=\"button\" class=\"btn btn-white btn-block font-weight-bold\">Finish listing</button></a>";
							echo "&nbsp;&nbsp;&nbsp;";
							echo "<a href=\"delete.php?id={$property->id}\"><button type=\"button\" class=\"btn btn-white btn-block font-weight-bold\">Delete</button></a>";
						?>
					</div>				

				<?php else: ?>

					<div class="listing" style="border:none;padding:.5rem">
						<div style=" margin:.5rem .7rem 0 0;position:relative;margin-bottom:1rem;float:left;">
							<img src="<?php echo $property->photo();?>", style="width:100px;"/>
						</div>
						<div style="padding:.5rem;">	
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
								echo "</a></div>";
								echo "<div><a href=\"delete.php?id={$property->id}\">Delete</a></div>";
							?>
						</div>
					</div>	
					
				<?php endif ?>	
		<?php endforeach; ?>  
	</div>	

<?php include_layout_template('footer.php'); ?>		
