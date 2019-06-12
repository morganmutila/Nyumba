<?php 
require '../init.php';
require LIB_PATH.DS.'formr'.DS.'class.formr.php';
require PACKAGE_PATH;
if (!$session->isLoggedIn()) { Redirect::to("login.php?redirect=listproperty"); } 


$page_title = "List property";

use Rakit\Validation\Validator;
$validator = new Validator;


if(Input::exists()){

    $validation = $validator->make($_POST, [
        'property_type'     => 'required',
        'location'          => 'required',
        'market_name'       => 'required'
    ]);

    $validation->setAliases([
        'property_address'  => 'Property address',
        'property_type'     => 'Property type',
        'market_name'       => 'Market type'
    ]);

    $validation->setMessages([
        'property_type:required' => 'Please tell us the type of property you are listing',
        'location:required'      => 'Specify where your property is located',
        'required'               => ':attribute can not be blank'
    ]);

    // Run the validation method
    $validation->validate();

    if($validation->fails()) {
        // handling errors
        $errors  = $validation->errors();
        $message = implode(", ", $errors->firstOfAll());
    }
    else{
        $property = new Property;

        $property->user_id 			= (int)    $session->user_id;
        $property->location_id  	= (int)    Input::get('location');
        $property->address     		= (string) Input::get('property_address');
        $property->beds      	    = 0;
        $property->baths     	    = 0;
        $property->terms      	    = "";
        $property->size      		= 0;
        $property->type      	    = (string) Input::get('property_type');
        $property->price            = 0;
        $property->price_old        = 0;
        $property->negotiabe 		= 0;
        $property->description      = "";
        $property->cphoto           = "";
        $property->contact_number   = $user->phone;
        $property->contact_email    = $user->email;
        $property->owner     	    = $user->fullName();
        $property->available        = "";
        $property->reference        = (string) rand();
        $property->status           = (int)    1;
        $property->units            = (int)    1;
        $property->views            = (int)    0;
        $property->flags            = (int)    0;
        $property->available    	= "";
        $property->listed_by        = (int)    1;
        $property->market     		= (string) strtolower(Input::get('market_name'));

    	if($property && $property->create()){
			// Add the property and re-direct
            Redirect::to('activate.php?property='.$property->id);
        } else{
            $message = "Oops! could not add your property, something went wrong, please try again";
        }
    }
}

?>
<?php include_layout_template('header.php'); ?>

	<h2>Add a property</h2>

    <?php
    // Initialise Formr
    $form = new Formr('bootstrap');
 
    $form->html5   = true; 
    $form->method  = 'POST';
    $form->action  = "list.php";

    // Property Type array
    $property_types = [
        "House"                     => "House",
        "Flat"                      => "Flat",
        "Apartment"                 => "Apartment",
        "Apartment(semi-detached)"  => "Apartment(semi-detached)",
        "Townhouse"                 => "Townhouse",
        "Condo"                     => "Condo"
    ];

    // Property Type Dynamic Select
    $property_type_data = [
        'type'      => 'select',
        'name'      => 'property_type',
        'label'     => 'Property Type',
        'value'     =>  Input::get('property_type'),
        'id'        => 'property_type',
        'string'    => '',
        'inline'    => '',
        'selected'  => 'Please select --',
        'options'   => $property_types
    ];

    // Location Dynamic Select
    $location_data = [
        'type'      => 'select',
        'name'      => 'location',
        'label'     => 'Location',
        'value'     =>  Input::get('location'),
        'id'        => 'location',
        'string'    => '',
        'inline'    => '',
        'selected'  => 'Please select --',
        'options'   => array_flip(Location::AllLocations())
    ];

    $html_form  = output_message($message, "danger");  

    $html_form .= $form->form_open();
    $html_form .= $form->input_select($property_type_data);
    $html_form .= $form->input_select($location_data);
    $html_form .= $form->input_text('property_address', 'Property Name / Address', escape(Input::get('address')),'property_address', 'placeholder="Address or name"');
    $html_form .= $form->input_radio_inline('market_name', 'For Rent', 'rent', 'rent', '','', 'checked');
    $html_form .= $form->input_radio_inline('market_name', 'For Sale', 'sale', 'sale');
    $html_form .= $form->input_submit('submit', '', 'Add property', '', 'class="btn-success btn-block font-weight-bold"');
    $html_form .= $form->form_close();

    // Display the generated Form
    echo $html_form;
?>

<?php include_layout_template('footer.php'); ?>
