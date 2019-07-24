<?php
include '../private/init.php';

// 1. the current page number ($current_page)
$page = Input::get('page') ? Input::get('page') : 1;

// 2. records per page ($per_page)
$per_page = Config::get('records_per_page');

// 3. total record count ($total_count)
$total_count = Property::total();

$pagination = new Pagination($page, $per_page, $total_count);

// Instead of finding all records, just find the records
// for this page

//Get the sort by from the Query string if any
if(Input::get('sort')) {
	Session::put('SORT', escape(Input::get('sort')));
	$sortby = Session::get('SORT');
} elseif(Session::exists('SORT') == true){
	$sortby = Session::get('SORT');
} else{
	Session::put('SORT', Config::get('default_sort'));
	$sortby = Config::get('default_sort');
}


$sql  = " SELECT * FROM property WHERE status >= ? ";
$sql .= " ORDER BY ";
$sql .=   sortby_filters($sortby);	
$sql .= " LIMIT {$per_page} ";
$sql .= " OFFSET {$pagination->offset()}";

$properties = Property::findBySql($sql, array(2));

?>


<?php layout_template('header.php'); ?>

<?php echo output_message($message, "success"); ?>
<?php echo flash("invalid_location", "warning"); ?>

<h4 class="mb-4">Properties on Nyumba Yanga</h4>

<div class="d-flex flex-row pb-3">	
	<?php echo NY_SEARCH_ENGINE(); ?>
	<form action="<?php echo escape($_SERVER['PHP_SELF']);?>" method="get" accept-charset="utf-8" class="ml-5">
		<?php
		  	$sortby_types = array(	  		
		        "Newest"      		=> "new",       
		        // "Best match"  		=> "best",
		        "Bedrooms"    	  	=> "beds",
		        "Price: Highest"    => "price_asc",
		        "Price: Lowest"     => "price_desc"
		  	);

	        $select_sortby = "<select onchange=\"this.form.submit()\" name=\"sort\" class=\"form-control\">";
	            foreach ($sortby_types as $type => $value) {
	                $select_sortby .= "<option value=\"$value\" ";
	                    if(Session::get('SORT') == $value || Config::get('default_sort') == $value){
	                        $select_sortby .= "selected";
	                    }
	                $select_sortby .= ">".$type."</option>";
	            }
	        $select_sortby .= "</select>";
	        echo $select_sortby;
		?>
	</form>
</div>

<div class ="properties pt-4 d-flex flex-row flex-wrap">
	<?php foreach ($properties as $property):?>
		<div class="card-deck col-4 mb-5">			
			<div class="card">
				<div style="position:relative;">
					<img src="<?php echo $property->photo();?>" class="card-img-top"/>
					<?php
					    echo '<div style="position:absolute;top: 0;right:0;left:0;width:100%;">';
						 	echo "<div style=\"float:left\">";
						 		if(new_listing($property->added)){
						 			echo "<span style=\"background-color:#11cc11;color:#fff;padding:0 .2rem;font-weight:bold;font-size:0.7rem;float:left;line-height:1rem;\">NEW</span><br>";
								}
								if(end_post_date($property->added)){
									echo "<span style=\"float:left;background:rgba(0,0,0,.54);color:#FFF;padding:4px 10px;font-size:12px;\">".time_ago($property->added)."</span>";
								}
							echo "</div>";	
							if(isset($user)){
								echo ($user->savedProperty($property->id)) ? fav_remove($property->id) : fav_add($property->id);		
							}else{
								echo '<a href="login.php?redirect=saved" style="color:#fff;float:right;padding: .2rem .4rem;"><i class="mdi mdi-heart-outline mdi-36px"></i></a>';
							}
						echo '</div>';
					?>
				</div>	
				<div class="card-body">		
					<div class="font-weight-bold">
						<?php echo amount_format($property->price);?>
						<small><?php echo $property->terms();?></small>
						<?php
							if($property->negotiable == true){
								echo "<small>NG</small>";
							}
						?>
					</div>
					<div>
						<span class="pr-2"><?php echo $property->beds;?> Beds</span>
						<span class="pr-2"><?php echo $property->baths;?> Baths</span>
						<span><?php echo number_format($property->size);?> Sqft</span>
					</div>
					<div>
						<a href="property.php?id=<?php echo $property->id; ?>">
							<?php echo $property->type . " for ".ucfirst($property->market)." ".$property->address .", ". $property->Location();?>
						</a>
					</div>
				</div>
		 	</div>
		</div>
	<?php endforeach; ?>
	<?php if(empty($properties)){ ?><div style="text-align: center;color:#777;">Oohh no,  there is currently no listings at the moment</div><?php } ?>
</div>


<?php echo NY_PAGINATION(); ?>

<?php layout_template('footer.php'); ?>
