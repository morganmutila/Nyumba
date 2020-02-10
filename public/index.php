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

<!DOCTYPE html>
<html lang="en">
	<?php layout_template('head.php'); ?>
	<body class="bg-white">

		<div id="root__ny">

			<?php layout_template('nav.php'); ?>

			<section id="main-content" class="d-flex flex-column" style="position: relative;max-width: 100vw; min-height: calc(100vh - 60px);">
				<div class="message-alerts">
					<?php echo output_message($message, "success"); ?>
					<?php echo flash("invalid_location", "warning"); ?>
				</div>
				<div class="filters" style="height: 70px">
					<div class="d-flex p-3 align-items-center border-bottom" style="height:70px;">
						<button class="btn btn-white border rounded-lg mr-2 text-dark" style="max-width: calc(100% - 160px);"><i class="mdi mdi-filter-variant text-success"></i>&nbsp;Filter</button>
						<form action="<?php echo escape($_SERVER['PHP_SELF']);?>" method="get" accept-charset="utf-8" class="pl-0 col-6" style="width:160px;">
							<?php
							  	$sortby_types = array(	  			  		       
							        "Just for you"  	  => "best",
							        "Newest"      	      => "new",
							        "Bedrooms"    	  	  => "beds",
							        "Price (high to low)" => "price_asc",
							        "Price (low to high)" => "price_desc"
							  	);

						        $select_sortby = "<select onchange=\"this.form.submit()\" name=\"sort\" class=\"form-control rounded-lg\">";
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
				</div>
				<div class="results_column d-flex flex-column flex-grow-1 flex-shrink-0">
					<div class="results_container d-flex flex-column flex-grow-1 flex-shrink-1 py-4 px-3">
						<div class="results_heading d-flex flex-column flex-grow-1 flex-shrink-1 pb-2">
							<div class="heading-text mr-2">
								<h1 class="font-weight-bold mb-3" style="font-size:1.25rem;">Properties for Rent & Sale on Nyumba Yanga</h1>
								<h2 class="text-black-50 pb-3 m-0"  style="font-size: 1rem;"><?php echo Property::total()." homes found on Nyumba yanga";?></h2>
							</div>
						</div>
						<div class ="properties">
							<?php foreach ($properties as $property):?>
								<div class="mb-4 rounded shadow-sm" style="position: relative; height: 260px; width: 100%; overflow: hidden;" role="presentation">
									<div class="property-photo">
										<div style="user-select: none; height: 100%;">
											<div style="top: 0; height: inherit; position: relative;">
												<div style="width: 100%; height: 158px; padding: 0; overflow: hidden; top: 0; left: 0;position: relative;background: transparent;"> 
													<img src="<?php echo $property->photo();?>" class="bg-dark" style="object-fit: cover; height: 160px; width: 100%"/>
													<?php
													    echo '<div style="position:absolute;top: 0;right:0;left:0;width:100%;">';
														 	echo "<div class=\"float-left\">";
														 		if(new_listing($property->added)){
														 			echo "<span class=\"d-inline-block bg-white text-success p-1 ml-1 mr-0 my-1 rounded font-weight-bold text-center\" style=\"font-size:.8rem;\">NEW</span>";
																}
																if(end_post_date($property->added)){
																	echo "<span class=\"d-inline-block bg-secondary text-white p-1 ml-1 mr-0 my-1 rounded font-weight-bold text-center\" style=\"font-size:.7rem;\">".time_ago($property->added)."</span>";
																}
															echo "</div>";	
															if(isset($user)){
																echo ($user->savedProperty($property->id)) ? fav_remove($property->id) : fav_add($property->id);		
															}else{
																echo '<a href="login.php?redirect=saved" class="float-right px-2 py-1 m-1 bg-white align-bottom text-success rounded-circle"><i class="mdi mdi-heart-outline mdi-18px"></i></a>';
															}
														echo '</div>';
													?>
												</div>
											</div>
										</div>	
									</div>	
									<div class="ml-2" style="position: absolute; width: 100%; top: 165px">		
										<div class="d-flex justify-content-between align-items-center mr-2">
											<?php echo $property->priceValue();?>
										</div>
										<div class="d-flex justify-content-start align-items-center mr-2" style="font-size:.94rem;">
											<span><?php echo $property->beds;?> bed</span>
											<small class="px-2">•</small>
											<span><?php echo $property->baths;?> bath</span>
											<small class="px-2">•</small>
											<span><?php echo $property->plotSize();?></span>						
										</div>
										<div class="mr-2" style="font-size:.96rem;"><?php echo $property->location();?></div>
									</div>
								</div>
							<?php endforeach; ?>
							<?php if(empty($properties)){ ?><div class="text-center gray-300">Oohh no,  there is currently no listings at the moment</div><?php } ?>
						</div>
					</div>
					<div class="results_pagination">
						<?php echo NY_PAGINATION(); ?>
					</div>	
				</div>

				<?php layout_template('footer.php'); ?>
			</section>

		</div>	

    	<script src="assets/js/jquery.js"></script>
    	<script src="assets/js/bootstrap.bundle.js"></script>
	</body>
</html>
