<?php
include '../../private/init.php';
if (!$session->isLoggedIn()) { Redirect::to("login.php"); } 
  // Find all the photos
  $properties = Property::findAll();
?>
<?php layout_template('header.php'); ?>

<h2>Property</h2>

<?php echo output_message($message); ?>
<table class="bordered">
  <tr>
    <th>Image</th>
    <th>Property name</th>
    <th>Bedrooms</th>
    <th>Bathrooms</th>
    <th>Owner</th>
    <th>Market price</th>
    <th>Added on</th>
    <th>Available</th>
    <th>Market name</th>
    <th>Flags</th>
		<th>&nbsp;</th>
  </tr>
<?php foreach($properties as $property): ?>
  <tr>
    <td><img src="../<?php echo $property->image_path(); ?>" width="100" /></td>
    <td><?php echo $property->address; ?></td>
    <td><?php echo $property->beds; ?></td>
    <td><?php echo $property->baths; ?></td>
    <td><?php echo $property->fullName; ?></td>
    <td><?php echo "K ".(int) $property->market; ?></td>
    <td><?php echo $property->added; ?></td>
    <td><?php echo $property->available; ?></td>
    <td><?php echo $property->market; ?></td>
		<td><a href="delete_property.php?id=<?php echo $property->id; ?>">Delete</a></td>
  </tr>
<?php endforeach; ?>
</table>

<?php layout_template('footer.php'); ?>
