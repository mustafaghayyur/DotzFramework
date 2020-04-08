<?php
namespace DotzFramework\Modules\User;

/**
 * You can write your own Validation class, and pass its instance object
 * to Auth::login() and Auth::check() as the fourth parameter. 
 * 
 * Just make sure your validation class also has these methods.
 */
class AccessValidator {

	/**
	 * Validates the user's access level against required access level.
	 *
	 * Our validator work's like this:
	 *
	 * 	- Super user access level: 0
	 * 	- Admin user access level: 1
	 * 	- Limited Admin's user access level: 2
	 * 	- User access level: 3
	 *
	 * If a user's access level is less than or equal to the required level, 
	 * they are granted access.
	 *
	 * Your app may have different requirements. We recommend you create your
	 * own AccessValidator class with a check() method that uses two inputs:
	 * 	- user access level (int)
	 * 	- required access level (int)
	 *
	 * And based on these inputs, determines the outcome.
	 *
	 * Return true if the user gets access. Return False if the user does not. 
	 */
	public function check($userLevel, $requiredLevel){
		
		if($userLevel <= $requiredLevel){
			return true;
		}else{
			return false;
		}
		
	}

}