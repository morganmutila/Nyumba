<?php
include '../init.php';

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
} elseif(Session::exists('SORT_BY') == true){
	$sortby = Session::get('SORT_BY');
} else{
	Session::put('SORT_BY', Config::get('default_sortby'));
	$sortby = Config::get('default_sortby');
}


$sql  = " SELECT * FROM property WHERE status >= ? ";
$sql .= " ORDER BY ";
$sql .=   sortby_filters($sortby);	
$sql .= " LIMIT {$per_page} ";
$sql .= " OFFSET {$pagination->offset()}";

$properties = Property::findBySql($sql, array(2));


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

	$properties_2 = Property::findBySql($sql_2, array(2, $session->location));
	
endif; // End if($session->location)
?>


<?php include_layout_template('header.php'); ?>
<?php echo NY_SEARCH_ENGINE(); ?>

<form action="<?php echo escape($_SERVER['PHP_SELF']);?>" method="get" accept-charset="utf-8">
	<?php
	  	$sortby_types = array(	  		
  			"Newest"		=> "new",  			
  			"Best match"	=> "best",
  			"Price (L-H)"   => "price_asc",
  			"Price (H-L)"	=> "price_desc",
  			"Bedrooms"		=> "beds"
	  	);

        $select_sortby = "<i class=\"mdi mdi-sort-descending mdi-18px\"></i><select onchange=\"this.form.submit()\" name=\"sortby\" style=\"width:auto;height:auto;display:inline;border:0;padding:0;margin:0;font-size:.9rem;background-color:transparent;color:#1db954;\">";
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

<?php echo output_message($message, "success"); ?>
<?php echo flash("invalid_location", "warning"); ?>

<?php if($session->location):?>
	<?php if(count($properties_2)):?>
		<h4>Houses in <?php echo Location::findLocationOn($session->location);//The Location name?>&nbsp;·&nbsp;<small style="color: #666;"><?php echo $number_of_homes;?>&nbsp; found</small></h4>
		<div class ="properties">
			<?php foreach ($properties_2 as $property_2):?>
				<div class="listing">
					<div style="margin:0;position:relative;">
						<img src="<?php echo $property_2->photo();?>"/>
						<?php
						    echo '<div style="position:absolute;top: 0;right:0;left:0;width:100%;">';
							 	echo "<div style=\"float:left\">";
							 		if(new_listing($property_2->added)){
							 			echo "<span style=\"background-color:#11cc11;color:#fff;padding:0 .2rem;font-weight:bold;font-size:0.7rem;float:left;line-height:1rem;\">NEW</span><br>";
									}
									if(end_post_date($property_2->added)){
										echo "<span style=\"float:left;background:rgba(0,0,0,.54);color:#FFF;padding:5px 15px;font-size:12px;\">".time_ago($property_2->added)."</span>";
									}
								echo "</div>";	
								if(isset($user)){
									echo ($user->savedProperty($property_2->id)) ? fav_remove($property_2->id) : fav_add($property_2->id);		
								}else{
									echo '<a href="login.php?redirect=saved" style="color:#fff;float:right;padding:1rem .5rem;"><i class="mdi mdi-heart-outline mdi-36px"></i></a>';
								}
							echo '</div>';
						?>
					</div>	
					<div style="padding:.5rem">
					<?php				  
						echo "<div>";
							echo "<div style=\"float:left;letter-spacing:0.02rem;font-size:1.2rem;\">".amount_format($property_2->price)."</div>";
							echo "<span style=\"float:left;\">&nbsp;".$property_2->terms()."</span>";
							echo  $property_2->negotiable == true ? "<span style=\"color:#11cc11;float:left;\">NG</span>" : "";
							echo "<div style=\"float:left;margin-left:1rem\">";
								echo "<span>" .$property_2->beds  . " beds<strong>&nbsp;·&nbsp;</strong></span>";
								echo "<span>" .$property_2->baths . " baths</span>";
								//echo "<strong>&nbsp;·&nbsp;</strong></span><span>" .number_format($property_2->size)    . " Sqft</span>";
							echo "</div>";
						echo "<div style=\"clear:both\"></div></div>";
						echo "<div style=\"padding:.4rem 0;font-size:.85rem;\"><a href=\"property.php?id={$property_2->id}\" style=\"color:#777\">";
						echo $property_2->type    . " for ".ucfirst($property_2->market)." ".$property_2->address .", ". $property_2->Location();
						echo "</a></div>";
					?>
					</div> 
			 	</div>
			<?php endforeach; ?>
		</div>
	<?php else: ?>

		<div style="margin:1rem 0" class="message-info">Oohh no, there is currently no listings in your location at the moment</div>
	
	<?php endif ?>

<?php endif ?>

<h4>Properties on Nyumba Yanga</h4>
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
</div>

<?php include_layout_template('footer.php'); ?>
