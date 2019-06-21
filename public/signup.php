<?php
require '../init.php';
require LIB_PATH.DS.'formr'.DS.'class.formr.php';
require PACKAGE_PATH;

if($session->isLoggedIn()){ Redirect::to("index.php");}

use Rakit\Validation\Validator;
$validator = new Validator;

//Add this rule to the Validator Class
$validator->addValidator('unique', new UniqueRule());

if(Input::exists()):   
    if(Session::checkToken(Input::get('token'))) {

        $validation = $validator->make($_POST, [
            'name'       => 'required|max:50',
            'phone'      => 'required|numeric|min:10|max:14|unique:users,phone',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6'
        ]);

        $validation->setAliases([
            'name'  => 'Fullname',
            'phone' => 'Phone number',
            'email' => 'Email address'
        ]);

        $validation->setMessages([
            'name:required' => ':attribute can not be blank',
            'email:required' => 'Please provide a valid :attribute',
            'unique'        => ':attribute has already been taken'
        ]);

        // run the validation method
        $validation->validate();

        if($validation->fails()) {
            // handling errors
            $errors  = $validation->errors();
            $message = implode(", ", $errors->firstOfAll());
        }
        else{

            $fullname = explode(" ", Input::get('name'));
            if (count($fullname) === 2){  
                
                // Assign variables to the exploded values
                list($first_name, $last_name) = $fullname;

                // Add the user to the database
                $user = new User();
                $user->group_id     = (int)    1;            
                $user->location_id  = (string) 1;
                $user->username     = (string) strtolower($first_name . $last_name);
                $user->first_name   = (string) ucfirst($first_name);
                $user->last_name    = (string) ucfirst($last_name);
                $user->phone        = (string) Input::get('phone');
                $user->status       = (string) "active";
                $user->last_login   = (string) "0000-00-00 00:00:00";
                $user->ip           = (string) $_SERVER['REMOTE_ADDR'];
                $user->email        = (string) Input::get('email');
                $user->password     = (string) password_hash(Input::get('password'), PASSWORD_DEFAULT);

                if($user->create()){
                // Log the newly created user
                    if ($user){
                            $session->login($user);
                            //See if there is a return URL
                            if(Input::get('returnurl')){
                                //Then redirect to the return url
                                Redirect::to("location.php?returnurl={Input::get('returnurl')}");
                            }
                            Session::flash("joined", "Welcome <strong>". $user->fullname() ."</strong>, Thank you for joining Nyumba yanga");
                            Redirect::to('location.php');
                    }
                } else{
                    $message = "Oops! Something went wrong, please try again";
                }
            }
            elseif((count($fullname) >= 2)){
                $message = "Only the first name and the last name are required, no middle names";
            }
            else{
                $message = "Full name is required, Please separate your first and last name with a space";
            }         
        }            
    }
endif; //End if(Input::exists())

$page_title = "Sign up - Nyumba Yanga";
?>
<?php include_layout_template('header.php'); ?>

<h2 class="text-center mb-4 font-weight-bold" style="text-align: center;margin-bottom: 0;">Join Nyumba Yanga</h2>

<p style="text-align: center;">Join Nyumba yanga and see Houses, Apartments, Flats and Town House's on rent and sale by property owners.</p>

<?php
    $form = new Formr('bootstrap');
 
    $form->html5 = true; 
    $form->method = 'POST';

    $html_form  = $form->form_open();
    $html_form .= $form->input_text('name',  '', escape(Input::get('name')),'full_name', 'placeholder="First & Last Name"');
    $html_form .= $form->input_tel('phone', '', escape(Input::get('phone')),'phone_number', 'placeholder="Phone Number"');
    $html_form .= $form->input_email('email', '', escape(Input::get('email')),'email', 'placeholder="Email Address"');
    $html_form .= $form->input_password('password',  '', escape(Input::get('password')),'password', 'placeholder="Create password"');
    $html_form .= '<p class="text-center text-muted py-2 px-4 small" style="text-align: center;">By signing up, you agree to Nyumba Yanga Terms and  Privacy Policy</p>';
    $html_form .= $form->input_hidden('token', Session::generateToken());
    $html_form .= $form->input_submit('submit', '', 'SIGN UP', 'sign_up', 'class="btn-success btn-block font-weight-bold"');
    $html_form .= output_message($message, "text-danger");
    $html_form .= '<p class="my-3 text-center" style="text-align: center;"><a href="login.php" class="small text-muted">Already on Nyumba yanga?&nbsp;Log in</a></p>';  
    $html_form .= $form->form_close();

    // Display the generated Form
    echo $html_form;
?>
