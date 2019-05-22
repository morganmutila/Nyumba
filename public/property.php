<?php require_once("../init.php"); 

if(!Input::get('id')) {
  Redirect::to('index.php');
}
  
$property = Property::findById(Input::get('id'));
  
if(!$property) {
  $session->message("The property could not be found.");
  redirect_to('index.php');
}else{
	$property->views = $property->views + 1;
	$property->save();
}
	
?>

<?php include_layout_template('header.php'); ?>

<p><a href="properties.php">My properties &raquo;</a></p>

<?php echo output_message($message); ?>

<div style=" margin: 20px 0;">
<?php 				
		// echo thumb_image($property->image()) . "<br>";
	echo "<strong>K ".(int)$property->price." ".$property->terms."</strong><br>";		
	echo $property->beds . " bedrooms <strong>·</strong> "; 
	echo $property->baths . " bathrooms<br>"; 
	echo $property->address . " ". $property->location() ."<br>";
	echo $property->type. " for ".$property->market."<br>";
	echo "Listed by: ".$property->propertyUser()."<br>";
	echo "<p>Views: ".$property->views."&nbsp;&nbsp; Status: <strong>".$property->propertyStatus()."</strong></p>";

	echo "<h4 style=\"margin-bottom: 0;\">Description</h4>";
	echo"<p>".ucfirst($property->description)."</p>";
 ?>
</div>

<?php include_layout_template('footer.php'); ?>
