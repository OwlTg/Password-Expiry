<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Settings extends Authenticated_API {

	var $object_name = "Settings";

	function __construct()
	{
		parent::__construct();

		$this->load->model(array('ajax/user_mo'));
        $this->load->library('encrypt');
	}

    // Update settings -> change password
	public function change_password_put() {
		$data = $this->put();


		$this->account = new Account($this->user->account_id);
        $user = $this->user;

        if($data['password_expired']){ // function call coming from password expired form
            $status = $this->user_mo->validate_change_password_with_confirm($this->user, $this->account, $data);
        } else { // other function calls that use this setting
            $status = $this->user_mo->validate_change_password($this->user, $this->account, $data);
        }


		if ($status === TRUE) {
			$this->user_mo->change_password($this->user, $this->account, $data);
			$this->message = "Password has been changed";
            if($data['password_expired']) { // for password expired, set a higher session for token
                $data = array(
                    'id'        => $user->id,
                    'accountId' => $user->account_id,
                    'isAdmin'   => $user->role_is_non_account == "1"? TRUE:FALSE,
                    'token'     => $user->token,
                    'isEngine'  =>  $user->is_engine,
                );
                $encoded  = json_encode($data);
                $this->data['token'] = $this->encrypt_jwt_token($encoded);
            }

			$this->_200();
		}
		else
		{
			$this->data = $status;
			$this->_validation_error();
			$this->_400();
		}
	}



}
