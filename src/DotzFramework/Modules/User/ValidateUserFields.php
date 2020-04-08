<?php
namespace DotzFramework\Modules\User;

/**
 * This is a basic validation class used by the User module.
 *
 * Notice the very wide net of allowed possibilities for the
 * username, email and password values.
 *
 * You can write your Validation class, and pass its instance object
 * to Auth::register() as the second paremter. Just make sure your
 * validation class also has these methods.
 */
class ValidateUserFields {

	/**
	 * validates the passed email.
	 */
	public function email($email){
		
		if(is_string($email) && !empty($email)){
			
			if(strlen($email) < 121){
				
				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					return true;
				}else{
					throw new \Exception('Email not formatted correctly.');
				}

			}else{
				throw new \Exception('Email value too large. Max 120 chars allowed.');
			}

		}else{
			throw new \Exception('Email value must be a string and not empty.');
		}

		return false;
	}

	/**
	 * validates the passed username
	 */
	public function username($username){
		
		if(is_string($username) && !empty($username)){
			
			if(strlen($username) < 121){
				return true;
			}else{
				throw new \Exception('Username value too large. Max 120 chars allowed.');
			}

		}else{
			
			throw new \Exception('Username must be a string and not empty.');
		
		}

		return false;
	}

	/**
	 * validates the passed password
	 */
	public function password($password){
		
		if(is_string($password) && !empty($password)){
			
			if(strlen($password) > 7 && strlen($password) < 31){
				return true;
			}else{
				throw new \Exception('Password must be between 8 to 30 characters in length.');
			}

		}else{
			throw new \Exception('Password must be a string and not empty.');
		}

		return false;
	}

}