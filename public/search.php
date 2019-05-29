<?php require '../init.php';

	//Redirect to the referer page if exits	
	if (!Input::get('q')) {
		!empty($_SERVER['HTTP_REFERER']) ? Redirect::to($_SERVER['HTTP_REFERER']) : Redirect::to('index.php');
	}

	//Query to find the Location
	$sql  = "SELECT * FROM location ";
	$sql .= " WHERE location LIKE ? LIMIT 1";

	$params = array("%".Input::get('q')."%");

	if($location = Location::findFirst($sql, $params)){			
		//Found location name and ID
		$found_location    = ucwords($location->location);
		$found_location_id = (int) $location->id;
	}else{
		Session::flash('invalid_location', "'".escape(Input::get('q'))."' is not a valid location");
		Redirect::to('index.php');
	}

	//get the total number of property from that Location
	$sql_count = "SELECT COUNT(*) AS count FROM property WHERE location_id = ?";
	DB::getInstance()->query($sql_count, array($found_location_id));

	//Number of prperty found
	$number_of_homes = DB::getInstance()->result('count');
	
	// 1. the current page number ($current_page)
	$page = !empty(Input::get('page')) ? (int)Input::get('page') : 1;

	// 2. records per page ($per_page)
	$per_page = Config::get('records_per_page');

	// 3. total record count ($total_count)
	$total_count = $number_of_homes;

	$pagination = new Pagination($page, $per_page, $total_count);


	//Get the sort by from the Query string if any
	if(Input::get('sortby')) {
		Session::put('SORT_BY', Input::get('sortby'));
		$sortby = Session::get('SORT_BY');
	}elseif(Session::exists('SORT_BY')){
		$sortby = Session::get('SORT_BY');
	}
	else{
		$sortby = Config::get('default_sortby');
	}

	// Build the query for found property

	$sql  = "SELECT * FROM property WHERE status >= ? AND location_id = ? ";
	$sql .= "ORDER BY ";
	$sql .=  sort_filter($sortby);
	$sql .= " LIMIT {$per_page} ";
	$sql .= "OFFSET {$pagination->offset()}";

	$properties = Property::findBySql($sql, array(1, $found_location_id));
	
?>

<?php include_layout_template('header.php'); ?>
<?php echo NY_SEARCH_ENGINE(); ?>

<form action="<?php echo escape($_SERVER['PHP_SELF']);?>" method="get" accept-charset="utf-8" style="border:1px solid #ccc; padding: .5rem;width: auto;display:inline;border-radius:4px;">
	<?php
	  	$price_filters = array(
	  		"Any Price"			 => "anyprice",
	  		"Below K1,000"       => "below1k",
   			"K1,000 - K2,000"    => "between1kto2k",
  			"K2,000 - K3,000"	 => "between2kto3k",
  			"K3,000 - K4,000"	 => "between3kto4k",
  			"K4,000 - K5,000"	 => "between4kto5k",
  			"K5,000 +"			 => "above5k",
	  	);

        $price_select = "<select onchange=\"this.form.submit()\" name=\"price\" style=\"width: auto;height:35px;display:inline;border:0;padding: 0;background-color:transparent;margin:0;font-size:.9rem;\">";
            foreach ($price_filters as $type => $value) {
                $price_select .= "<option value=\"$value\" ";
                    if(Session::exists('SORT_BY') && Session::get('SORT_BY') == $value){
                        $price_select .= "selected";
                    }
                $price_select .= ">".$type."</option>";
            }
        $price_select .= "</select>";
        echo $price_select;
	?>
</form>
<form action="<?php echo escape($_SERVER['PHP_SELF']);?>" method="get" accept-charset="utf-8" style="border:1px solid #ccc; padding: .5rem;width: auto;display:inline;border-radius:4px;">
	<?php
		$beds_filters = array(
			"Any" => "Any",
			"1"   =>  1,
			"2"   =>  2,
			"3"   =>  3,
			"4"   =>  4,
			"5+"   => "5+",
		);

   		$bed_select = "<select onchange=\"this.form.submit()\" name=\"beds\" style=\"width: auto;height:35px;display:inline;border:0;padding: 0;background-color:transparent;margin:0;font-size:.9rem;\">";
            foreach ($beds_filters as $type => $value) {
                $bed_select .= "<option value=\"$value\" ";
                    if(Session::exists('SRCH_FILTER') && Session::get('SRCH_FILTER') == $value){
                        $bed_select .= "selected";
                    }
                $bed_select .= ">".$type."</option>";
            }
        $bed_select .= "</select>";
        echo $bed_select;
	?>
</form>&nbsp;
<button type="button"><i class="mdi mdi-tune"></i>&nbsp;Filters</button>

<?php echo output_message($message, "success"); ?>


<h4><?php echo $found_location; ?>&nbsp;homes&nbsp;&nbsp;·&nbsp;&nbsp;<small style="color: #555;"><?php echo $number_of_homes;?>&nbsp; Results found</small></h4>
<div class ="properties">
	<?php foreach ($properties as $property):?>
		<!-- <div style=" margin: 20px 0 0 0;">
			<img src="../uploads/property/default.png">
		</div> -->
		<div class="listing">
			<?php
				echo "<p>";
				    if(new_listing($property->added)){echo "<span style=\"background-color:#11cc11;color:#fff;padding:0 .2rem;font-weight:bold;font-size:0.7rem;float:left;line-height:1rem;\">NEW</span>";}

				echo "<span style=\"color:#666;font-size:0.75rem;float:right;line-height:1rem;\">".time_ago($property->added)."</span>";
				echo "<div style=\"clear:both;\"></div></p>";   
				echo "<span style=\"letter-spacing: 0.02rem;font-size:1.1rem;\">".amount_format($property->price)."&nbsp;<small>".$property->rentTerms()."</small></span>";
				echo ($property->negotiable == true) ? "<span style=\"color:#11cc11;font-size:0.7rem;\">NEG</span>" : "";
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
	<?php if(empty($properties)){ ?><div style="text-align: center;color:#aaa;"><p><i class="mdi mdi-home-map-marker mdi-48px"></i></p><div>Oohh no&nbsp;<?php echo "<strong>".Location::findLocationOn($found_location_id)."</strong>&nbsp;"; ?>is quiet,  there is currently no listings at the moment, choose another location to see listings you might like</div><?php } ?>
</div>


<div style="text-align: center">
	<ul class="pagination">					    			
		<?php
			$pages = ceil($pagination->offset() - 1);
			if($pagination->total_pages() > 1){
				if($pagination->has_previous_page()){								  
				    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"search.php?page=";
				    echo $pagination->previous_page();
				    echo "\"><i class=\"mdi mdi-chevron-left\"></i></a></li>";	   
				}
			    if($pagination->previous_page() == 0){
					echo "<li class=\"page-item disabled\"><span class=\"page-link\"><i class=\"mdi mdi-chevron-left\"></i></span></li>";			
				}	
			    for($i = 1; $i <= $pagination->total_pages(); $i++){
			    	if($i == $page){
			    		echo "<li class=\"page-item active\"><span class=\"page-link\">{$i}</span></li>";
			    	}else{
				    	echo "<li class=\"page-item\"><a href=\"search.php?page={$i}\" class=\"page-link\">{$i}</a></li>";
				    }
			    }								    	

				if($pagination->total_pages() < $pagination->next_page()){
			    	echo "<li class=\"page-item disabled\"><span class=\"page-link\"><i class=\"mdi mdi-chevron-right\"></i></span></li>";
			    }				
				if($pagination->has_next_page()){										  
				    echo "<li class=\"page-item\"><a class=\"page-link\" href=\"search.php?page=";
				    echo $pagination->next_page();
				    echo "\"><i class=\"mdi mdi-chevron-right\"></i></a></li>";
				}									
			}							
		?>
	</ul>
</div>

<?php include_layout_template('footer.php'); ?>
