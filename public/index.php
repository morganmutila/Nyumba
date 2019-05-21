<?php require '../init.php';

	// 1. the current page number ($current_page)
	$page = !empty(Input::get('page')) ? (int)Input::get('page') : 1;

	// 2. records per page ($per_page)
	$per_page = Config::get('records_per_page');

	// 3. total record count ($total_count)
	$total_count = Property::total();
	
	$pagination = new Pagination($page, $per_page, $total_count);
	
	// Instead of finding all records, just find the records 
	// for this page
	$sql  = "SELECT * FROM property WHERE status >= ? ";
	$sql .= "ORDER BY added DESC "; 
	$sql .= "LIMIT {$per_page} ";
	$sql .= "OFFSET {$pagination->offset()}";
	$properties = Property::findBySql($sql, array(1));


	// Instead of finding all records, just find the records 
	// for this page
	if($session->location): 
		$sql_2  = "SELECT * FROM property WHERE status >= ? AND location_id = ?";
		$sql_2 .= "ORDER BY added DESC "; 
		$sql_2 .= "LIMIT {$per_page} ";
		$sql_2 .= "OFFSET {$pagination->offset()}";
		$properties_2 = Property::findBySql($sql_2, array(1, $session->location));
	endif;
?>

<?php include_layout_template('header.php'); ?>

<h2>Houses on Nyumba Yanga</h2>

<?php echo output_message($message); ?>
<div class ="properties">
	<?php foreach ($properties as $property):?>
		<div style=" margin: 20px 0 0 0;">
			<img src="../uploads/property/default.png">		
		</div>
		<div style=" margin: 10px 0 2rem 0;">
			<?php 
				echo "<a href=\"property.php?id={$property->id}\">";			
				echo "<strong>".amount_format($property->price)."&nbsp;<small>".$property->rentTerms()."</small></strong>";
				echo ($property->negotiable == true) ? "&nbsp;<span style=\"color:#11cc11;font-size:0.7rem;\">NEG</span>" : "";
				echo "<br>";		
				echo $property->beds    . " beds - "; 
				echo $property->baths   . " baths - ";
				echo $property->size    . " Sqft<br>";  
				echo $property->address . ", ". $property->Location() ."<br>";
				echo "For ".ucfirst($property->market);
				echo "</a>";
				if(isset($user)){
					echo ($user->SavedProperty($property->id)) ?
						"<a href=\"listremove.php?id=$property->id\" style=\"margin-left: 4.5rem;\">❤️</a>":
						"<a href=\"listsave.php?id=$property->id\" style=\"margin-left: 4.5rem;\">Save</a>";
				}else{
					echo "<a href=\"login.php?redirect=saved\" style=\"margin-left: 4.5rem;\">Save</a>";
				}		
			 ?>
	 	</div>
	<?php endforeach; ?>
	<?php if(empty($properties)){echo "<div style=\"padding: 1rem 0.3rem;\">There is currently no listing at the moment</div>";}?>
</div>


<?php if($session->location):?>
<h2><?php echo Location::findLocationOn($session->location); //The Location name?></h2>
<div class ="properties">
	<?php foreach ($properties_2 as $property_2):?>
		<div style=" margin: 20px 0 2rem 0;">
			<?php 
				echo "<a href=\"property.php?id={$property_2->id}\">";			
				echo "<strong>".amount_format($property_2->price)."&nbsp;<small>".$property_2->rentTerms()."</small></strong>";
				echo ($property_2->negotiable == true) ? "&nbsp;<span style=\"color:#11cc11;font-size:0.7rem;\">NEG</span>" : "";
				echo "<br>";		
				echo $property_2->beds    . " beds <strong>·</strong> "; 
				echo $property_2->baths   . " baths <strong>·</strong> ";
				echo $property_2->size    . " Sqft<br>";  
				echo $property_2->address . ", ". $property_2->Location() ."<br>";
				echo "For ".ucfirst($property_2->market);
				echo "</a>";
				if(isset($user)){
					echo ($user->SavedProperty($property_2->id)) ?
						"<a href=\"listremove.php?id=$property_2->id\" style=\"margin-left: 4.5rem;\">❤️</a>":
						"<a href=\"listsave.php?id=$property_2->id\" style=\"margin-left: 4.5rem;\">Save</a>";
				}else{
					echo "<a href=\"login.php?redirect=saved\" style=\"margin-left: 4.5rem;\">Save</a>";
				}		
			 ?>
	 	</div>
	<?php endforeach; ?>
	<?php if(empty($properties_2)){echo "<div style=\"padding: 1rem 0.3rem;\">There is currently no listing at the moment</div>";}?>
</div>
<?php endif; ?>


<div id="pagination" style="clear: both;">
<?php
	if($pagination->total_pages() > 1) {		
		if($pagination->has_previous_page()) { 
    		echo "<a href=\"index.php?page=";
	     	echo $pagination->previous_page();
	        echo "\">&laquo; Previous</a> "; 
	    }

			for($i=1; $i <= $pagination->total_pages(); $i++) {
				if($i == $page) {
					echo " <span class=\"selected\">{$i}</span> ";
				} else {
					echo " <a href=\"index.php?page={$i}\">{$i}</a> "; 
				}
			}

		if($pagination->has_next_page()) { 
			echo " <a href=\"index.php?page=";
			echo $pagination->next_page();
			echo "\">Next &raquo;</a> "; 
	    }
		
	}
?>
</div>

<?php include_layout_template('footer.php'); ?>
