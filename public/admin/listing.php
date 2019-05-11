<?php require_once("../../init.php"); ?>
<?php if (!$session->isLoggedIn()) { Redirect::to("login.php"); } ?>
<?php
  // Find all the photos
  $properties = Property::findAll();
?>
<?php include_layout_template('admin_header.php'); ?>

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
    <td><?php echo $property->property_name; ?></td>
    <td><?php echo $property->bedrooms; ?></td>
    <td><?php echo $property->bathrooms; ?></td>
    <td><?php echo $property->owner; ?></td>
    <td><?php echo "K ".(int) $property->market_price; ?></td>
    <td><?php echo $property->added_on; ?></td>
    <td><?php echo $property->available; ?></td>
    <td><?php echo $property->market_name; ?></td>
    <td><?php echo $property->flags; ?></td>
		<td><a href="delete_property.php?id=<?php echo $property->id; ?>">Delete</a></td>
  </tr>
<?php endforeach; ?>
</table>

<?php include_layout_template('footer.php'); ?>
