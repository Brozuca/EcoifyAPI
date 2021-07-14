<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

header('Content-Type: application/json');

require 'config.php';
require 'functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$headers = apache_request_headers();

$q =$_GET['q'];
$params=explode('/', $q);

$type = $params[0];
$id = $params[1];
$authorization_key = $headers['Authorization'];
if(verificationKey($connect, $authorization_key))
	{
		switch ($method) {
		case 'GET':
			switch ($type ) {
				case 'user':
					getAccounts($connect, $id);
					break;
				case 'activity':
					getActivity($connect, $id);
					break;
				case 'deleteEmail':
				   deleteEmail($connect, $id);
				   default:
					http_response_code(404);
					break;
				case 'getMessages':
				    getMessages($connect, $id);
					break;
			}
			break;
		case 'POST':
			switch ($type ) {
				case 'account':
					addAccounts($connect, $data = array('login' => $_POST['login'], 'password_hash'=> $_POST['password_hash'],'img_url'=>$_POST['img_url'] ));
					break;
				case 'email':
				    $data = file_get_contents('php://input');
					$data = json_decode($data, true);
					insertEmail($connect, $data['email']);
					break;
				case 'verificate':
				    $data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    verificatePin($connect, $id, $data['pin']);
				    break;
				case 'login':
				    $data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    insertLogin($connect, $id, $data['login']);
				    break;
				case 'password':
				    $data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    insertPassword($connect, $id, $data['password_hash']);
				    break;
				case 'signin':
				    $data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    sugnIn($connect, $data['email']);
				    break;
				case 'activity':
				    $data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getActivityBetween($connect, $id, $data['from'], $data['num']);
				    break;
				case 'userActivity':
				    $data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getUserActivityBetween($connect, $id, $data['from'], $data['num']);
				    break;
	
				case 'points':
				    $data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getPoints($connect, $data['lat'], $data['lng'], $data['radius']);
				    break;
				case 'marker':
				    $data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    newMarker($connect, $data['lat'], $data['lng'], $data['anonim'], $data['description'], $data['id_user'], $data['type'], $data['paper'], $data['plastic'], $data['glass'], $data['metal'], $data['food_waste'], $data['electriccal']);
				    break;
				case 'markerImage':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    markerAddImage($connect, $data['id'], $data['image'], $data['type']);
					break;
	
				case 'image':
				    $data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    newImage($data['image'],$data['image_name']);
				    break;
				case 'addActivity':
				    $data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    addActivity($connect, $data['status'], $data['place_id'], $data['user_id']);
				    break;
				case 'addPlaceActivity':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    addPlace_Activity($connect, $data['plcae_id'], $data['activity_id']);
				    break;
				case 'getNearPlaces':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getPointsBetween($connect, $data['lat'], $data['lng'], $data['radius'], $data['from'], $data['num']);
				    break;
				case 'getUsers':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getUsers($connect, $data['exp'], $data['id']);
					break;
	
				case 'getFriends':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getFriends($connect, $data['id']);
				    break;
				case 'getFollows':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getFollow($connect, $data['id']);
				    break;
				case 'getFollowers':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getFollowers($connect, $data['id']);
				    break;
	
				case 'addFriends':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    addFriends($connect, $data['id'], $data['id_friend']);
					break;
				case 'followerToFriend':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    followerToFriend($connect, $data['id'], $data['id_friend']);
					break;
				case 'friendToFollower':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    friendToFollower($connect, $data['id'], $data['id_friend']);
					break;
				case "deleteFriends":
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    deleteFriends($connect, $data['id'], $data['id_friend']);
					break;
	
				case "getCoversation":
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getConversation($connect, $data['id'], $data['from'], $data['num'], $data['id_user']);
					break;
	
				case "newPinCode":
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    newPinCode($connect, $data['id']);
					break;
	
				case "addLikeToPost":
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    addLikeToPost($connect, $data['id_user'], $data['id_post']);
					break;
				case "removeLikeToPost":
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    removeLikeToPost($connect, $data['id_user'], $data['id_post']);
					break;
	
				case 'getPlace':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getPlace($connect, $data['id'], $data['lat'], $data['lng'], $data['user_id']);
					break;
	
				case 'plnnedClear':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    FirstStepCleanPlcace($connect, $data['id_user'], $data['id_place'], $data['date']);
					break;
				case 'clear':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    CleanPlcace($connect, $data['id_user'], $data['id_place']);
					break;
	
				case 'notClear':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    StillNotCleanPlcace($connect, $data['id_user'], $data['id_place']);
					break;
	
				case 'newProfileImage':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    ChangeProfileImage($connect, $data['id_user'], $data['image']);
				    break;
				case 'newLogin':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    ChangeLogin($connect, $data['id_user'], $data['login']);
				    break;
				case 'changePasswordGenPin':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    ChangePasswordGenPin($connect, $data['id_user']);
				    break;
				case 'changePasswordGenPinWithEmail':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    ChangePasswordGenPinWithEmail($connect, $data['email']);
				    break;
	
				case 'changePasswordVerificatePin':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    ChangePasswordVerificatePin($connect, $data['id_user'], $data['pin']);
				    break;
				case 'UserProfileData':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getUserData($connect, $data['id_main_user'], $data['id_user'], $data['from'], $data['num']);
				    break;
				case 'currentParticipants':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getCurrentParticipants($connect, $data['id_user'], $data["id_place"], $data["from"], $data["num"]);
				    break;
				case 'participants':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getParticipants($connect, $data['id_user'], $data["id_place"], $data["from"], $data["num"]);
				    break;
				case 'findParticipants':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    findParticipants($connect, $data['exp'], $data['id'], $data["id_place"]);
					break;
				case 'possibleParticipants':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    getPossibleParticipants($connect, $data['id_user'], $data["id_place"], $data["from"], $data["num"]);
				    break;
				case 'becomePossibleParticipants':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    becomePossibleParticipants($connect, $data["id_place"], $data["id_user"]);
				    break;
				case 'becomeCurrentParticipants':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    becomeCurrentParticipants($connect, $data["id_place"], $data["id_user"]);
				    break;
				case 'exitParticipants':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    exitParticipants($connect, $data["id_place"], $data["id_user"]);
				    break;
	
				case 'remeberPasswordCancel':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    RemeberPasswordCancel($connect, $data["id_user"]);
					break;
	
				case 'imageDeleteOld':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    ImageDeleteOld($connect, $data["id_user"]);
					break;
	
				case 'addPossibleParticipant':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				    AddPossibleParticipant($connect, $data["id_user"], $data["id_place"]);
					break;
	
				//Report блок
				case 'sendReport':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				   	SendReport($connect, $data["id_place"], $data["id_user"], $data["type"], $data["text"]);
					break;
				case 'sendImageReport':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				   	SendImageReport($connect, $data["id_report"], $data["url"]);
					break;
	
				case 'getRerorts':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				   	getRerorts($connect, $data["from"], $data["num"], $data["id"]);
					break;
	
				case 'approvedRerorts':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				   	approvedRerorts($connect, $data["id_report"], $data["id_user"]);
					break;
	
				case 'rejectedRerorts':
					$data = file_get_contents('php://input');
				    $data = json_decode($data, true);
				   	rejectedRerorts($connect, $data["id_report"], $data["id_user"]);
					break;
	
				default:
					http_response_code(404);
					break;
			}
			break;
		case 'PATCH':
			if($type === 'account')
			{
				if($id)
				{
					$data = file_get_contents('php://input');
					$data = json_decode($data, true);
					updateAccounts($connect, $id, $data[0]);
				}
			}
			break;
		case 'DELETE':
			if($type === 'account')
			{
				if($id)
				{
					deleteAccounts($connect, $id);
				}
			}
			break;
		default:
			http_response_code(404);
			break;
		}

}


