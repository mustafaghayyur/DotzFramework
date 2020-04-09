<?php
namespace DotzFramework\Modules\User;

use DotzFramework\Core\Dotz;

/**
 * You can write your own Validation class, and pass its instance object
 * to Auth::login() and Auth::check() as the fourth parameter. 
 * 
 * Just make sure your validation class also has these methods.
 */
class ValidateAccess {

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
	 * If a user's access level integer is less than or equal to the required 
	 * level integer; they are granted access.
	 *
	 * Your app may have different requirements. We recommend you create your
	 * own ValidateAccess class with a checkAccessLevel() method that uses two inputs:
	 * 	- user access level (int)
	 * 	- required access level (int)
	 *
	 * And based on these inputs, determines the outcome.
	 *
	 * Return true if the user gets access. Return False if the user does not. 
	 */
	public function checkAccessLevel($userLevel, $requiredLevel){

		if($userLevel <= $requiredLevel){
			return true;
		}else{
			return false;
		}
		
	}

	/**
	 * User status determines their state in the system.
	 * Typically new users are given a status of probation;
	 * until they confirm their email address, upon which
	 * time they are changed to a status of confirmed.
	 *
	 * A user could be removed from the system, at which point
	 * their status may be changed to 'removed', 'banned', etc...
	 */
	public function checkStatus($userStatus){
		
		$okayStatusArray = Dotz::config('user.okayStatus');
		
		if(is_array($okayStatusArray)){
			if(in_array($userStatus, $okayStatusArray)){
				return true;
			}
		}

		return false;
	}

	/**
	 * for token authentication, we want to stay current..
	 * a token can stay valid indefinitely, and if so,
	 * we want to ensure that the token belongs to a valid user.
	 * 
	 * confirm user has not become un-authorized to access resource..
	 * by searching his/her credentials in the database.
	 */
	public function checkTokenAgainstDB($payload, $requiredLevel){

		// perform this operation only if the token is older 
		// than $okayTime seconds. This saves server resources.
		$okayTime = (int)Dotz::config('user.tokenOkayTime');
		$t = (int)$payload->iat;
		if((time() - $t) < $okayTime){
			return true;
		}

		$a = new Auth(true);
		
		$userRecord = $a->authenticateUser(
			$payload->user, 
			null, 
			$requiredLevel, 
			$this, 
			true
		);
		
		if(is_array($userRecord)){
			
			if($this->checkStatus($userRecord['status'])){
				
				if($this->checkAccessLevel($userRecord['access_level'], $requiredLevel)){
					return true;
				}
			}
		}

		return false;
		
	}

}