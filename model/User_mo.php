<?php

class User_mo extends Base_mo
{

    var $defaultOrderColumns = array(
        'id' => 'asc'
    );

    function __construct()
    {
        parent::__construct();
        parent::__construct();

        $this->object = new User();
        $this->load->library('Data_validator');
        $this->data_validator->set_rules($this->validation_rules);
    }


    function validate_change_password_with_confirm($user, $account, $data, $validate_current_password = true)
    {
        list($user, $account) = $this->check_instance($user, $account); // get user account

        $this->validation_rules = array( // validation rules for new password set
            'password' => array(
                'label' => "Password",
                'rules' => array(
                    'required' => '%label% is required',
                    'min_length[8]' => '%label% must be at least 8 characters',
                    'max_length[30]' => '%label% must be at most 30 characters',
                    'callback_check_password' => array($this, 'callback_check_password'),
                    'callback_password_used_recently' => array($this, 'callback_password_used_recently'),
                )
            ),
            'confirm_password' => array(
                'label' => "Confirm Password",
                'rules' => array(
                    'callback_password_not_identical' => array($this, 'callback_password_not_identical'),
                )
            )
        );

        if ($validate_current_password) { // Force to check the existing password
            $this->validation_rules['current_password'] = array(
                'label' => "Current Password",
                'rules' => array(
                    'required' => '%label% is required',
                    'callback_valid_password' => array($this, 'callback_valid_password')
                )
            );
        }

        $this->user = $user;
        $this->account = $account;
        $this->data = $data;
        return $this->run_validation($data);
    }

    //callback functions should return specific error messages

    public function callback_check_password($field, $value, $data)
    {
        if ($value) {

            $lower_regex  = '/^(?=.*[a-z]).+$/';
            $upper_regex  = '/^(?=.*[A-Z]).+$/';
            $digit_regex  = '/^(?=.*\d)/';
            $non_an_regex = '/^(?=.*(_|[^\w])).+$/';

            if (!preg_match($lower_regex, $value)) {
                return '%label% should contain at least one lowercase letter';
            }

            if (!preg_match($upper_regex, $value)) {
                return '%label% should contain at least one uppercase letter';
            }

            if (!preg_match($digit_regex, $value)) {
                return '%label% should contain at least one number';
            }

            if (!preg_match($non_an_regex, $value)) {
                return '%label% should contain at least one non-alphanumeric character';
            }
        }

        return TRUE;
    }

    public function callback_valid_password($field, $value, $data)
    {
        if ($this->user->exists() && $this->user->is_valid) { // check if user exists
            $verified = $this->verify_password($this->user);
            if (!$verified) {
                return "Invalid password";
            }
        } else {
            return "User not found dsd";
        }
        return TRUE;
    }

    public function callback_password_not_identical($field, $value, $data)
    {
        if ($data['confirm_password'] != $data['password']) {
            return "New Password and confirm password does not match.";
        }
        return TRUE;
    }

    public function callback_password_used_recently($field, $value, $data)
    {

        // get current user details
        if ($this->user->exists() && $this->user->is_valid) {  // function call is coming an actual user
            $user = $this->user;
        }
        if ($data["token"] !== null && $data["code"]) { // function call is coming from system engines
            $user = new User();
            $user->where('verification_code', $data["code"]);
            $user->where('verification_token', $data["token"]);
            $user->get();
        }

        $verified = $this->verify_password($user);

        if ($verified) {
            return "Please choose a password that you haven't used recently.";
        }

        return TRUE;
    }

    public function verify_password($user){ // checking password from database

        if ($user->password_encryption == PTX_PASSWORD_ENCRYPTION_BCRYPT) { // check password  using new encryption
            return $verified = $this->bcrypt_verify($user->salt . $value, $user->password);
        } else if ($user->password_encryption == PTX_PASSWORD_ENCRYPTION_SHA1) { // check password using old encryption
            $hashed = $this->encrypt($user->salt . $value);
            return $verified = $hashed == $user->password;
        }
        return FALSE;
    }
}
