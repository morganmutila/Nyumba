<?php require '../init.php';

	// 1. the current page number ($current_page)
	$page = Input::get('page') ? (int) Input::get('page') : 1;

	// 2. records per page ($per_page)
	$per_page = Config::get('records_per_page');

	// 3. total record count ($total_count)
	$total_count = Property::total();

	$pagination = new Pagination($page, $per_page, $total_count);

	// Instead of finding all records, just find the records
	// for this page

	//Get the sort by from the Query string if any
	if(Input::get('sortby')) {
		Session::put('SORT_BY', escape(Input::get('sortby')));
		$sortby = Session::get('SORT_BY');
	}elseif(Session::exists('SORT_BY') == true){
		$sortby = Session::get('SORT_BY');
	}
	else{
		Session::put('SORT_BY', Config::get('default_sortby'));
		$sortby = Config::get('default_sortby');
	}


	$sql  = "SELECT * FROM property WHERE status >= ? ";
	$sql .= "ORDER BY ";
	$sql .=  sortby_filters($sortby);	
	$sql .= " LIMIT {$per_page} ";
	$sql .= "OFFSET {$pagination->offset()}";

	$properties = Property::findBySql($sql, array(1));
	

	// Instead of finding all records, just find the records
	// for this page
	if($session->location):
		// Get the number of listings in the selected Location
		$sql_count = "SELECT COUNT(*) AS count FROM property WHERE location_id = ?";
		DB::getInstance()->query($sql_count, array($session->location));
		$number_of_homes = DB::getInstance()->result('count');

		$sql_2  = "SELECT * FROM property WHERE status >= ? AND location_id = ? ";
		$sql_2 .= "ORDER BY ";
		$sql_2 .=  sortby_filters($sortby);
		$sql_2 .= " LIMIT {$per_page} ";
		$sql_2 .= "OFFSET {$pagination->offset()}";

		$properties_2 = Property::findBySql($sql_2, array(1, $session->location));
	endif;
?>


<?php include_layout_template('header.php'); ?>
<?php echo NY_SEARCH_ENGINE(); ?>

<form action="<?php echo escape($_SERVER['PHP_SELF']);?>" method="get" accept-charset="utf-8" style="border:1px solid #ccc; padding: .5rem;width: auto;display:inline;border-radius:4px;">
	<?php
	  	$sortby_types = array(	  		
  			"Newest"		=> "new",  			
  			"Best match"	=> "best",
  			"Price (L-H)"   => "price_asc",
  			"Price (H-L)"	=> "price_desc",
  			"Bedrooms"		=> "beds"
	  	);

        $select_sortby = "<span>Sort:</span><select onchange=\"this.form.submit()\" name=\"sortby\" style=\"width: auto;height:35px;display:inline;border:0;padding: 0;background-color:transparent;margin:0;font-size:.9rem;\">";
            foreach ($sortby_types as $type => $value) {
                $select_sortby .= "<option value=\"$value\" ";
                    if(Session::get('SORT_BY') == $value || Config::get('default_sortby') == $value){
                        $select_sortby .= "selected";
                    }
                $select_sortby .= ">".$type."</option>";
            }
        $select_sortby .= "</select>";
        echo $select_sortby;
	?>
</form>
<button type="button" style="float: right;"><i class="mdi mdi-tune"></i>&nbsp;Filters</button>


<?php echo output_message($message, "success"); ?>
<?php echo flash("invalid_location", "warning"); ?>

<?php if($session->location):?>
<h4><?php echo Location::findLocationOn($session->location);//The Location name?>&nbsp;homes&nbsp;&nbsp;·&nbsp;&nbsp;<small style="color: #555;"><?php echo $number_of_homes;?>&nbsp; homes found</small></h4>
<div class ="properties">
	<?php foreach ($properties_2 as $property_2):?>
		<div class="listing">
			<?php
				echo "<div>";
				    if(new_listing($property_2->added)){echo "<span style=\"background-color:#11cc11;color:#fff;padding:0 .2rem;font-weight:bold;font-size:0.7rem;float:left;line-height:1rem;\">NEW</span>";}else{echo "&nbsp;";}

				echo "<span style=\"color:#666;font-size:0.75rem;float:right;line-height:1rem;\">".time_ago($property_2->added)."</span><div style=\"clear:both;\"></div></div>";   
				echo "<div style=\"letter-spacing: 0.02rem;font-size:1.1rem;\">".amount_format($property_2->price)."&nbsp;<small>".$property_2->rentTerms()."</small>";
				echo  ($property_2->negotiable == true) ? "<small style=\"color:#11cc11;\">NEG</small>" : "";
				echo "<span style=\"float:right;\">".$property_2->oldPrice()."</span></div>";
				echo "<br>";
				echo $property_2->beds    . " beds<strong>&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;</strong>";
				echo $property_2->baths   . " baths<strong>&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;</strong>";
				echo number_format($property_2->size)    . " Sqft";
				if(isset($user)){
					echo ($user->SavedProperty($property_2->id)) ?
						"<a href=\"listremove.php?id=$property_2->id\" style=\"float:right;\"><i class=\"mdi mdi-heart mdi-24px\"></i></a>":
						"<a href=\"listsave.php?id=$property_2->id\" style=\"float:right;\"><i class=\"mdi mdi-heart-outline mdi-24px\"></i></a>";
				}else{
					echo "<a href=\"login.php?redirect=saved\" style=\"float:right;\"><i class=\"mdi mdi-heart-outline mdi-24px\"></i></a>";
				}
				echo "<br>";
				echo "<a href=\"property.php?id={$property_2->id}\">";
				if(!empty($property_2->address)){
					echo "<strong>".$property_2->address . ", ". $property_2->Location() ."<br>";
					echo $property_2->type    . " for ".ucfirst($property_2->market)."</strong>";
				}else{
					echo "<strong>".$property_2->type    . " for ".ucfirst($property_2->market).", ". $property_2->Location() ."</strong>";
				}
				echo "</a>";
			 ?>
	 	</div>
	<?php endforeach; ?>
	<?php if(empty($properties_2)){ ?><div style="text-align: center;color:#777;"><p><i class="mdi mdi-home-map-marker mdi-48px"></i></p><div>Oohh no,  there is currently no listings at the moment</div><?php } ?>
</div>
<?php endif; ?>


<h4>Featured Houses</h4>
<div class ="properties">
	<?php foreach ($properties as $property):?>
		<!-- <div style=" margin: 20px 0 0 0;">
			<img src="../uploads/property/default.png">
		</div> -->
		<div class="listing">
			<?php
				echo "<div>";
				    if(new_listing($property->added)){echo "<span style=\"background-color:#11cc11;color:#fff;padding:0 .2rem;font-weight:bold;font-size:0.7rem;float:left;line-height:1rem;\">NEW</span>";}else{echo "&nbsp;";}

				echo "<span style=\"color:#666;font-size:0.75rem;float:right;line-height:1rem;\">".time_ago($property->added)."</span><div style=\"clear:both;\"></div></div>";   
				echo "<div style=\"letter-spacing: 0.02rem;font-size:1.1rem;\">".amount_format($property->price)."&nbsp;<small>".$property->rentTerms()."</small>";
				echo  ($property->negotiable == true) ? "<small style=\"color:#11cc11;\">NEG</small>" : "";
				echo "<span style=\"float:right;\">".$property->oldPrice()."</span></div>";
				echo "<br>";
				echo $property->beds    . " beds<strong>&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;</strong>";
				echo $property->baths   . " baths<strong>&nbsp;&nbsp;&nbsp;·&nbsp;&nbsp;&nbsp;</strong>";
				echo number_format($property->size)    . " Sqft";
				if(isset($user)){
					echo ($user->SavedProperty($property->id)) ?
						"<a href=\"listremove.php?id=$property->id\" style=\"float:right;\"><i class=\"mdi mdi-heart mdi-24px\"></i></a>":
						"<a href=\"listsave.php?id=$property->id\" style=\"float:right;\"><i class=\"mdi mdi-heart-outline mdi-24px\"></i></a>";
				}else{
					echo "<a href=\"login.php?redirect=saved\" style=\"float:right;\"><i class=\"mdi mdi-heart-outline mdi-24px\"></i></a>";
				}
				echo "<br>";
				echo "<a href=\"property.php?id={$property->id}\">";
				if(!empty($property->address)){
					echo "<strong>".$property->address . ", ". $property->Location() ."<br>";
					echo $property->type    . " for ".ucfirst($property->market)."</strong>";
				}else{
					echo "<strong>".$property->type    . " for ".ucfirst($property->market).", ". $property->Location() ."</strong>";
				}
				echo "</a>";
			 ?>
	 	</div>
	<?php endforeach; ?>
	<?php if(empty($properties)){ ?><div style="text-align: center;color:#777;"><p><i class="mdi mdi-home-map-marker mdi-48px"></i></p><div>Oohh no,  there is currently no listings at the moment</div><?php } ?>
</div>


<div style="text-align: center">
	<?php echo NY_PAGINATION(); ?>
</div>

<?php include_layout_template('footer.php'); ?>
