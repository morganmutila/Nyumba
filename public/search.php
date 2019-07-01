<?php
include '../init.php';

//Redirect if a search query has not been set
if(Input::get('q') == false){
	Session::flash('invalid_location', "Enter a location to search for property listings");
	Redirect::home();
}

// Store the search query string into a variable
$search_query = escape(trim(Input::get('q')));

//Query to find the Location
$sql  = "SELECT * FROM location ";
$sql .= " WHERE location LIKE ? LIMIT 1";

if($location = Location::findFirst($sql, array($search_query."%"))){			
	//Found location name and ID
	$found_location    = ucwords($location->location);
	$found_location_id = (int) $location->id;
}else{
	Session::flash('invalid_location', "'".$search_query."' is not in our database");
	Redirect::home();
}


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


//Get the sort by from the Query string if any
if(Input::get('price') && Input::get('beds')) {
	// Put the GET values in a SESSION
	Session::put('SRCH_FILTER_PRICE', escape(trim(Input::get('price'))));
	Session::put('SRCH_FILTER_BEDS' , escape(trim(Input::get('beds'))));

	// Assign variables from the SESSION values
	$filter_price = $_SESSION['SRCH_FILTER_PRICE'];
	$filter_beds  = $_SESSION['SRCH_FILTER_BEDS'];

} 
elseif(Session::exists('SRCH_FILTER_PRICE') && Session::exists('SRCH_FILTER_BEDS')){
	$filter_price = Session::get('SRCH_FILTER_PRICE');
	$filter_beds  = Session::get('SRCH_FILTER_BEDS');
}
else{
	Session::put('SRCH_FILTER_PRICE', escape(trim(Config::get('default_srch_filter/filter_price'))));
	Session::put('SRCH_FILTER_BEDS' , escape(trim(Config::get('default_srch_filter/filter_beds'))));
	$filter_price = escape(Config::get('default_srch_filter/filter_price'));
	$filter_beds  = escape(Config::get('default_srch_filter/filter_beds'));
}


// Get the total number of property from that Location
$sql_count  = "SELECT COUNT(*) AS count FROM property WHERE status >= ? AND location_id = ? ";	
$sql_count .= search_filters($filter_price, $filter_beds);
DB::getInstance()->query($sql_count, array(2, $found_location_id));

// Number of property found
$property_count = DB::getInstance()->result('count');

// ACTIVATE PAGINATION
// 1. the current page number ($current_page)
$page = Input::get('page') ? (int) Input::get('page') : 1;

// 2. records per page ($per_page)
$per_page = Config::get('records_per_page');

// 3. total record count ($total_count)
$total_count = $property_count;

$pagination = new Pagination($page, $per_page, $total_count);

// Build the query for found property
$sql  = "SELECT * FROM property WHERE status >= ? AND location_id = ? ";	
$sql .=   search_filters($filter_price, $filter_beds);
$sql .= " ORDER BY ". sortby_filters(Session::get('SORT_BY'));
$sql .= " LIMIT {$per_page} ";
$sql .= " OFFSET {$pagination->offset()}";
$properties = Property::findBySql($sql, array(1, $found_location_id));


$number_of_homes = 458;
?>

<?php include_layout_template('header.php'); ?>
<?php echo NY_SEARCH_ENGINE(); ?>

<h2 style="font-size: 1.15rem;"><?php echo Location::cityLocation($found_location_id); ?></h2>
<p style="color: #555;margin-top: -0.6rem"><?php echo number_format($property_count);?>&nbsp;properties found on market</p>

<form action="<?php echo escape($_SERVER['PHP_SELF']);?>" method="get" accept-charset="utf-8" style="display: inline;">
	<?php
	  	$price_filters = array(
	  		"Any Price"		 => "anyprice",
	  		"Below K2k"      => "below2k",
   			"K2k - K5k"      => "between2kto5k",
  			"K5k - K10k"	 => "between5kto10k",
  			"K10k - K15k"	 => "between10kto15k",
  			"K15k - K20k"	 => "between15kto20k",
  			"Above K20k"	 => "above20k",
	  	);

        $price_select = "<select onchange=\"this.form.submit()\" name=\"price\" style=\"width: auto;display:inline;background-color:transparent;margin:0;border:1px solid #ccc; padding:.5rem .8rem;font-size:.8rem;border-radius:4px;height:2rem;\">";
            foreach ($price_filters as $type => $value) {
                $price_select .= "<option value=\"$value\" ";
                    if(Session::get('SRCH_FILTER_PRICE') == $value || Config::get('default_srch_filter/filter_price') == $value){
                        $price_select .= "selected";
                    }
                $price_select .= ">".$type."</option>";
            }
        $price_select .= "</select>";
        echo $price_select;
	?>
    &nbsp;
	<?php
		$beds_filters = array(
			"Any" => "any",
			"1"   =>  1,
			"2"   =>  2,
			"3"   =>  3,
			"4"   =>  4,
			"5+"   => "above5",
		);

   		$bed_select = "<select onchange=\"this.form.submit()\" name=\"beds\" style=\"width: auto;display:inline;background-color:transparent;margin:0;border:1px solid #ccc; padding:.5rem .8rem;font-size:.8rem;border-radius:4px;height:2rem;\">";
            foreach ($beds_filters as $type => $value) {
                $bed_select .= "<option value=\"$value\" ";
                    if(Session::get('SRCH_FILTER_BEDS') == $value || Config::get('default_srch_filter/filter_beds') == $value){
                        $bed_select .= "selected";
                    }
                $bed_select .= ">".$type."</option>";
            }
        $bed_select .= "</select>";
        echo $bed_select;
	?>
	<input type="hidden" name="q" value="<?php echo escape($search_query);?>"/>
</form>
&nbsp;
<button type="button" style="height: 2rem"><i class="mdi mdi-tune"></i>&nbsp;Filters</button>

<?php echo output_message($message, "success"); ?>
<br><br>

<div class ="properties">
	<?php foreach ($properties as $property):?>
		<div class="listing">
			<div style=" margin:0;position:relative;">
				<img src="<?php echo $property->photo();?>"/>
				<?php
				    echo '<div style="position:absolute;top: 0;right:0;left:0;width:100%;">';
					 	echo "<div style=\"float:left\">";
					 		if(new_listing($property->added)){
					 			echo "<span style=\"background-color:#11cc11;color:#fff;padding:0 .2rem;font-weight:bold;font-size:0.7rem;float:left;line-height:1rem;\">NEW</span><br>";
							}
							if(end_post_date($property->added)){
								echo "<span style=\"float:left;background:rgba(0,0,0,.54);color:#FFF;padding: 5px 15px;font-size:12px;\">".time_ago($property->added)."</span>";
							}
						echo "</div>";	
						if(isset($user)){
							echo ($user->savedProperty($property->id)) ? fav_remove($property->id) : fav_add($property->id);		
						}else{
							echo '<a href="login.php?redirect=saved" style="color:#fff;float:right;padding:1rem .5rem;"><i class="mdi mdi-heart-outline mdi-36px"></i></a>';
						}
					echo '</div>';
				?>
			</div>	
			<div style="padding:.5rem">		
			<?php				  
				echo "<div>";
					echo "<div style=\"float:left;letter-spacing: 0.02rem;font-size:1.2rem;\">".amount_format($property->price)."</div>";
					echo "<span style=\"float:left;\">&nbsp;".$property->terms()."</span>";
					echo  $property->negotiable == true ? "<span style=\"color:#11cc11;float:left;\">NG</span>" : "";
					echo "<div style=\"float:left;margin-left:1rem\">";
						echo "<span>" .$property->beds  . " beds<strong>&nbsp;·&nbsp;</strong></span>";
						echo "<span>" .$property->baths . " baths";
						//echo "<strong>&nbsp;·&nbsp;</strong></span><span>" .number_format($property->size)    . " Sqft</span>";
					echo "</div>";
				echo "<div style=\"clear:both\"></div></div>";
				echo "<div style=\"padding:.4rem 0;font-size:.85rem;\"><a href=\"property.php?id={$property->id}\" style=\"color:#777;\">";
				echo $property->type    . " for ".ucfirst($property->market)." ".$property->address .", ". $property->Location();
				echo "</a></div>";
			?>
			</div>
	 	</div>
	<?php endforeach; ?>
	<?php if(empty($properties)){ ?><div style="text-align: center;color:#777;">Oohh no,  there is currently no listings at the moment</div><?php } ?>
</div>
	
<div style="text-align: center">
	<?php echo NY_PAGINATION(); ?>
	<h4 style="margin:0 0 2.5rem 0;"><?php echo Location::cityLocation($found_location_id, true); ?></h4>
</div>

<div>
	<h4>About&nbsp;<?php echo $found_location; ?></h4>
	<span>Properties on market:...............................<?php echo number_format($number_of_homes);?> </span><br>
	<span>Average rent pricet:..................................<?php echo $number_of_homes;?> </span><br>
	<span>Average sale price:...................................<?php echo $number_of_homes;?> </span><br>
</div>	
<hr>
<div>
	<h4>Find by style</h4>
	<a href="#">Houses in&nbsp;<?php echo $found_location; ?></a><br>
	<a href="#">Apartments in&nbsp;<?php echo $found_location; ?></a><br>
	<a href="#">Semi-detached apartments in &nbsp;<?php echo $found_location; ?></a><br>
	<a href="#">Flats in &nbsp;<?php echo $found_location; ?></a><br>
	<a href="#">Townhouses in &nbsp;<?php echo $found_location; ?></a><br>
</div>
<hr>
<div>
	<h4>Number of bedrooms</h4>
	<a href="#">1 bedrooms</a><br>
	<a href="#">2 bedrooms</a><br>
	<a href="#">3 bedrooms</a><br>
	<a href="#">4 bedrooms</a><br>
	<a href="#">5 bedrooms</a><br>
</div>

<?php include_layout_template('footer.php'); ?>
