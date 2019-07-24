<?php
include '../private/init.php';
require_login("index.php");

if(Input::exists()){
    if(Session::checkToken(Input::get('token'))){
        $validation = new Validation();

        $validation->check($_POST, array(
            'firstname'  => array(
                'required' => true,
                'min'      => 2,
                'max'      => 50
            ),
            'lastname'  => array(
                'required' => true,
                'min'      => 2,
                'max'      => 50
            )
        ));

        if($validation->passed()){
            try{
                $user->update(array(
                    'firstname'  => Input::get('firstname'),
                    'lastname' => Input::get('lastname')
                ));

                Session::flash('success', 'Information Updated Successfully');
                Redirect::to('index.php');

            }catch (Exception $e){
                die($e->getMessage());
            }
        }else{
            pre($validation->errors());
        }
    }
}

$page_title = "Update";

?>

<form action="<?php echo escape($_SERVER['PHP_SELF'])?>" method="post" accept-charset="utf-8">
    <div class="field">
        <label for="first_name">First Name</label>
        <input type="text" name="firstname" id="first_name" value="<?php echo $user->data()->firstname;?>"/>
    </div>  
    <div class="field">  
        <label for="last_name">Last Name</label>
        <input type="text" name="lastname" id="last_name" value="<?php echo $user->data()->lastname;?>"/>
    </div>
    <div class="field">
        <input type="submit" value="Update"/>
        <input type="hidden" name="token" value="<?php echo Session::generateToken();?>"/>
    </div>
</form>