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
<?php echo NY_SEARCH_ENGINE(); ?>

<?php echo output_message($message, "success"); ?>

<?php if($session->location):?>
<h4><?php echo Location::findLocationOn($session->location);//The Location name?>&nbsp;homes</h4>
<div class ="properties">
	<?php foreach ($properties_2 as $property_2):?>
		<div style=" margin: 20px 0 2rem 0;">
			<?php
				if(new_listing($property_2->added)){echo "<span style=\"color:#11cc11;font-size:0.7rem;\">NEW</span><br>";}
				echo "<strong style=\"letter-spacing: 0.05rem;font-size:1.1rem;\">".amount_format($property_2->price)."&nbsp;<small>".$property_2->rentTerms()."</small></strong>";
				if(isset($user)){
					echo ($user->SavedProperty($property_2->id)) ?
						"<a href=\"listremove.php?id=$property_2->id\" style=\"float:right;\">❤️</a>":
						"<a href=\"listsave.php?id=$property_2->id\" style=\"float:right;\">Save</a>";
				}else{
					echo "<a href=\"login.php?redirect=saved\" style=\"float:right;\">Save</a>";
				}
				echo ($property_2->negotiable == true) ? "&nbsp;<span style=\"color:#11cc11;font-size:0.7rem;\">NEG</span>" : "";
				echo "<br>";
				echo $property_2->beds    . " beds<strong>&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;</strong>";
				echo $property_2->baths   . " baths<strong>&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;</strong>";
				echo $property_2->size    . " Sqft<br>";
				echo "<a href=\"property.php?id={$property_2->id}\">";
				echo "<strong>".$property_2->address . ", ". $property_2->Location() ."</strong><br>";
				echo $property_2->type    . " for ".ucfirst($property_2->market);
				echo "</a>";
				echo "<p style=\"color:#666;font-size:0.75rem;margin-top:0.6rem;\">".time_ago($property_2->added)."</p>";
			 ?>
	 	</div>
	<?php endforeach; ?>
	<?php if(empty($properties_2)){echo "<div style=\"padding: 1rem 0.3rem;\">There is currently no listing at the moment</div>";}?>
</div>
<?php endif; ?>


<h4>Houses on Nyumba Yanga</h4>
<div class ="properties">
	<?php foreach ($properties as $property):?>
		<!-- <div style=" margin: 20px 0 0 0;">
			<img src="../uploads/property/default.png">
		</div> -->
		<div style=" margin: 10px 0 2rem 0;">
			<?php
				if(new_listing($property->added)){echo "<span style=\"color:#11cc11;font-size:0.7rem;\">NEW</span><br>";}
				echo "<strong style=\"letter-spacing: 0.05rem;font-size:1.1rem;\">".amount_format($property->price)."&nbsp;<small>".$property->rentTerms()."</small></strong>";
				if(isset($user)){
					echo ($user->SavedProperty($property->id)) ?
						"<a href=\"listremove.php?id=$property->id\" style=\"float:right;\">❤️</a>":
						"<a href=\"listsave.php?id=$property->id\" style=\"float:right;\">Save</a>";
				}else{
					echo "<a href=\"login.php?redirect=saved\" style=\"float:right;\">Save</a>";
				}
				echo ($property->negotiable == true) ? "&nbsp;<span style=\"color:#11cc11;font-size:0.7rem;\">NEG</span>" : "";
				echo "<br>";
				echo $property->beds    . " beds<strong>&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;</strong>";
				echo $property->baths   . " baths<strong>&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;</strong>";
				echo $property->size    . " Sqft<br>";
				echo "<a href=\"property.php?id={$property->id}\">";
				echo "<strong>".$property->address . ", ". $property->Location() ."</strong><br>";
				echo $property->type    . " for ".ucfirst($property->market);
				echo "</a>";
				echo "<p style=\"color:#666;font-size:0.75rem;margin-top:0.6rem;\">".time_ago($property->added)."</p>";
			 ?>
	 	</div>
	<?php endforeach; ?>
	<?php if(empty($properties)){echo "<div style=\"padding: 1rem 0.3rem;\">There is currently no listing at the moment</div>";}?>
</div>


<div id="pagination" style="clear: both;">
<?php
	if($pagination->total_pages() > 1) {
		if($pagination->has_previous_page()) {
    		echo "<a href=\"index.php?page=";
	     	echo $pagination->previous_page();
	        echo "\">< Prev</a> ";
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
			echo "\">Next ></a> ";
	    }

	}
?>
</div>

<?php include_layout_template('footer.php'); ?>
