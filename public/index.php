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

<div class="d-flex flex-row justify-content-between">	
	<div class="order-1">
		<h4 class="mb-2">Properties on Nyumba Yanga</h4>
		<p class="text-secondary mb-4"><?php echo Property::total()." properties available on Nyumba yanga";?></p>
	</div>	
	<form action="<?php echo escape($_SERVER['PHP_SELF']);?>" method="get" accept-charset="utf-8" class="order-2 float-right">
		<?php
		  	$sortby_types = array(	  		
		        "Newest"      	      => "new",       
		        // "Best match"  	  => "best",
		        "Bedrooms"    	  	  => "beds",
		        "Price (high to low)" => "price_asc",
		        "Price (low to high)" => "price_desc"
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

<div class ="properties row pt-4 d-flex flex-wrap">
	<?php foreach ($properties as $property):?>
		<div class="col-3 mb-4">
			<a href="property.php?id=<?php echo $property->id; ?>">
				<div style="position:relative;">
					<img src="<?php echo $property->photo();?>" class="rounded img-fluid"/>
					<?php
					    echo '<div style="position:absolute;top: 0;right:0;left:0;width:100%;">';
						 	echo "<div style=\"float:left\">";
						 		if(new_listing($property->added)){
						 			echo "<div style=\"background-color:#11cc11;color:#fff;padding:.1rem;border-radius:4px;margin:.5rem 0 .1rem .4rem;font-weight:bold;font-size:.7rem;width:38px;text-align:center;line-height:1rem;\">NEW</div>";
								}
								if(end_post_date($property->added)){
									echo "<div style=\"background:rgba(0,0,0,0.3);color:#FFF;padding:2px 4px;margin-left:.4rem;border-radius:4px;font-size:.7rem;font-weight:bold;\">".time_ago($property->added)."</div>";
								}
							echo "</div>";	
							if(isset($user)){
								echo ($user->savedProperty($property->id)) ? fav_remove($property->id) : fav_add($property->id);		
							}else{
								echo '<a href="login.php?redirect=saved" style="color:#fff;float:right;padding:0 .4rem;margin-top:-.3rem;"><i class="mdi mdi-heart-outline mdi-36px"></i></a>';
							}
						echo '</div>';
					?>
				</div>	
				<div class="py-1">		
					<div style="font-size:1.26rem;">
						<?php echo $property->priceValue();?>
					</div>
					<div style="font-size:.95rem;">
						<span class="pr-2"><?php echo $property->beds;?> Beds</span>
						<span class="pr-2"><?php echo $property->baths;?> Baths</span>
						<span><?php echo $property->plotSize();?></span>
					</div>
					<div style="font-size:.95rem;"><?php echo "{$property->type} for {$property->market} {$property->address}";?></div>
					<div style="font-size:.95rem;"><?php echo $property->location();?></div>
				</div>
			</a>	
		</div>
	<?php endforeach; ?>
	<?php if(empty($properties)){ ?><div style="text-align: center;color:#777;">Oohh no,  there is currently no listings at the moment</div><?php } ?>
</div>


<?php echo NY_PAGINATION(); ?>

<?php layout_template('footer.php'); ?>
