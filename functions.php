<?php


function getAccounts($connect, $id)
{
	echo $urlImage;
	$query="SELECT * FROM (SELECT  @curRank := @curRank + 1 AS rank, id, login,img_url, date_of_registretion, add_num, clear_num, score_points from user_info, (SELECT @curRank := 0) r ORDER BY score_points DESC) t WHERE t.id=$id";
	

	$result = $connect->query($query);
	
	while ($row = $result->fetch_row()) {
		$postList[] =  array(
			'id_user' => $row[1],
			'login' => $row[2],
			'img_url' => $row[3], 
			'datatime' => $row[4],
			'add_num'=>$row[5],
			'clear_num'=>$row[6],
			'score_points'=>$row[7],
			"rank"=>$row[0]
		);
	}
	if($postList){
		http_response_code(201);

		echo json_encode($postList);
	}

}

function addAccounts($connect, $data)
{
	$id = uniqid();

	$query = "INSERT INTO user_info( login, password_hash, img_url) VALUES ('".$data['login']."','".$data['password_hash']."','".$data['img_url']."')";
	;
	if(mysqli_query($connect, $query))
	{
		http_response_code(201);

		$res=[
		'status' => true,
		'user_id' => mysqli_insert_id($connect)];

		echo json_encode($res);
	}
}
	
function updateAccounts($connect, $id, $data){

	$query = "UPDATE user_info SET login='".$data['login']."', password_hash='".$data['password_hash']."',img_url='".$data['img_url']."' WHERE id = ".$id;
	echo $query;
	if(mysqli_query($connect, $query))
	{
		http_response_code(200);

		$res=[
		'status' => true,
		'message' => "Data updateted"];

		echo json_encode($res);
	}
	
}

function deleteAccounts($connect, $id)
{
	$query = "DELETE FROM user_info WHERE user_info.id =  ".$id;
	if(mysqli_query($connect, $query))
	{
		http_response_code(200);

		$res=[
		'status' => true,
		'message' => "Data deleted"];

		echo json_encode($res);
	};

}
function verificationKey($connect, $key)
{
	$query = "SELECT api_key FROM api_data WHERE api_key ='$key'";
    $result = $connect->query($query);

	return $result->num_rows==1;
}

function getActivity($connect, $id)
{
	$query="SELECT activity.id, user_info.img_url, user_info.login, activity.date_of_add, activity.image_url FROM user_info JOIN activity ON user_info.id=activity.id_user WHERE user_info.id = $id";

	$result = $connect->query($query);
	$list =array();
	$i=0;

	while ($row = $result->fetch_row()) {
	    http_response_code(201);
	    
		$likes_query ="SELECT COUNT(*) AS count FROM likes WHERE id_post = $row[0]";
		$likes_result = $connect->query($likes_query);
		$likes_count = $likes_result->fetch_row();
		
		$coments_query ="SELECT COUNT(*) AS count FROM comment WHERE id_post = $row[0]";
		$coments_result = $connect->query($coments_query);
		$coments_count = $coments_result->fetch_row();
		
		$res =[
		    'activity_id' => $row[0],
		    'user_image' => $row[1],
		    'user_login' => $row[2],
		    'date' => $row[3],
		    'text' => $row[4],
		    'img' => $row[5],
		    'count_likes' => $likes_count[0],
		    'count_coments' => $coments_count[0]
		    ];
		    $list[$i++]= $res;
	}

echo json_encode($list);
}

function getActivityBetween($connect, $id, $from, $num)
{
	$query="SELECT activity.type, user_info.login, place.text,activity.date_of_add, activity.id, user_info.id, place.id, place.planned_date FROM place JOIN activity ON place.id = activity.place_id JOIN user_info ON user_info.id=activity.id_user WHERE activity.id_user IN (SELECT id_friend FROM frends WHERE id_user = '$id') OR activity.id_user = '$id' AND place.anonim != true ORDER BY activity.date_of_add DESC LIMIT $num OFFSET $from";

	$result = $connect->query($query);
	$list =array();
	$i=0;

	while ($row = $result->fetch_row()) {
	    http_response_code(201);

	    $image_arr=[];
		$j=0;
		$query_img="SELECT image FROM place_image_before WHERE id_place = $row[6]";

		$result_img = $connect->query($query_img);

		while ($row_img = $result_img->fetch_row()) {
			$image[$j++]=$row_img[0];
		}

		$k=0;
		$query_participants="SELECT user_info.id, place_group.role, user_info.login, user_info.img_url FROM place_group JOIN user_info ON user_info.id = place_group.id_user WHERE place_group.id_place = $row[6]";

		$result_participants = $connect->query($query_participants);

		while ($row_participants = $result_participants->fetch_row()) {
			$participants[$k++] = $row_participants[3];
		}
	    		
		$res =[
			'id'=>$row[4],
			'place_id'=>$row[6],
		    'type'=> $row[0],
		    'login' => $row[1],
		    "user_id"=>$row[5],
		    'text'=> $row[2],
		    'date'=> $row[3],
		    'images'=>$image,
		    'planned_date'=>$row[7],
		    'participaints'=>$participants
		    ];
		    $list[$i++]= $res;
	}

echo json_encode($list);
}

function getUserActivityBetween($connect, $id, $from, $num)
{
	$query="SELECT place.status, user_info.login, place.text,activity.date_of_add, activity.id, user_info.id, place.id, place.planned_date FROM place JOIN activity ON place.id = activity.place_id JOIN user_info ON user_info.id=activity.id_user WHERE activity.id_user = '$id' AND place.anonim != true ORDER BY activity.date_of_add DESC LIMIT $num OFFSET $from";
	$result = $connect->query($query);
	$list =array();
	$i=0;

	while ($row = $result->fetch_row()) {
	    http_response_code(201);

	    $image_arr=[];
		$j=0;
		$query_img="SELECT image FROM place_image_before WHERE id_place = $row[6]";

		$result_img = $connect->query($query_img);

		while ($row_img = $result_img->fetch_row()) {
			$image[$j++]=$row_img[0];
		}

		$k=0;
		$query_participants="SELECT user_info.id, place_group.role, user_info.login, user_info.img_url FROM place_group JOIN user_info ON user_info.id = place_group.id_user WHERE place_group.id_place = $row[6]";

		$result_participants = $connect->query($query_participants);

		while ($row_participants = $result_participants->fetch_row()) {
			$participants[$k++] = $row_participants[3];
		}
	    		
		$res =[
			'id'=>$row[4],
			'place_id'=>$row[6],
		    'type'=> $row[0],
		    'login' => $row[1],
		    "user_id"=>$row[5],
		    'text'=> $row[2],
		    'date'=> $row[3],
		    'images'=>$image,
		    'planned_date'=>$row[7],
		    'participaints'=>$participants
		    ];
		    $list[$i++]= $res;
	}

echo json_encode($list);
}

function getMainData($connect, $id)
{
	if($id)
	{
		$query="SELECT id, login, password_hash, img_url, email, date_of_registretion FROM user_info  WHERE id=$id";
		$result = $connect->query($query);
		while ($row = $result->fetch_row()) {
		$userData[] =  array(
			'user_id' => $row[0],
			'login' => $row[1],
			'password_hash' => $row[2],
			'img_url' => $row[3],
			'email' => $row[4],
			'date_of_registretion' => $row[5] );
		}
		$query = "SELECT activity.id, user_info.img_url, user_info.login, activity.text, activity.image_url, activity.date_of_add FROM user_info JOIN activity ON user_info.id = activity.id_user WHERE user_info.id=".$id;
	
	$result = $connect->query($query);

	while ( $row = $result->fetch_row()) {
		$activity[] =  array(
			'activity_id' => $row[0],
			'user_image' => $row[1],
			'user_login' => $row[2],
			'activity_image' => $row[3],
			'activity_text' => $row[4],
			'activity_datatime' => $row[5], );
	}
	$postList[]= array('userData' => $userData,
					   'activity' =>$activity
	 );
	echo json_encode($postList);
	}
}

function insertEmail ($connect, $email)
{
	$key = generateKey();
	$query = "SELECT verefication_key FROM user_info WHERE verefication_key ='$key'";
	$result = $connect->query($query);

	if($result->num_rows==0)
	{
		$query = "SELECT email FROM user_info WHERE email ='$email'";
		$result = $connect->query($query);
		if($result->num_rows==0)
		{

			$query ="INSERT INTO user_info(email, verefication_key) VALUES ('$email', '$key')";
			$result = $connect->query($query);
			if($result)
			{
				$id = $connect->insert_id;
				$to      = $email;
				$subject = 'Добро пожаловать в Ecoify';
				$message = '<!DOCTYPE html >
							<html lang="ru">
							<head>
							  <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
							</head>
							<body> 
							<table style ="max-width: 600px; background-color: #FEFEFE; font-family: Verdana;">
								<tr>
									<td style = "color: #0BB996; font-size: 60px; font-weight: lighter; text-align:center; margin-left: 64px; margin-right: 64px">
											<img src="http://f0523531.xsph.ru/images/app_icon.png" alt="logo" />
											Ecoify
									</td>
								</tr>
								<tr>
									<td height="32px">
									</td>
								</tr>
								<tr>
									<td style="text-align:center; color: #0BB996;  font-size: 50px; margin-left: 64px; margin-right: 64px">
										Добро пожаловать в Ecoify
									</td>
								</tr>
								<tr>
									<td height="32px">
									</td>
								</tr>
								<tr>
									<td style="text-align:center; color: #0BB996;  font-size: 25px; font-weight: lighter; margin-left: 64px; margin-right: 64px">
										Чтобы подтвердить действие в приложение введите следующий код:
									</td>
								</tr>
								<tr>
									<td height="16px">
									</td>
								</tr>
								<tr>
									<td style="text-align:center; color: #0BB996;  font-size: 50px; font-weight: lighter;  margin-left: 64px; margin-right: 64px">
											'.$key.'
									</td>
								</tr>
								<tr>
									<td height="64px">
									</td>
								</tr>
								<tr style="background-color: #226642">
									<td style="text-align:center; color: white; font-size: 20px; font-weight: lighter; padding-left: 64px; padding-right: 64px; padding-top: 16px; padding-bottom: 16px">
											На данное сообщение не нужно отвечать.<br><br>
											Если есть вопросы, вы можете написать на почту <a style="font-weight: bold; text-decoration: none; color: white;" href = "mailto: teamsupport@f0523531.xsph.ru">teamsupport@f0523531.xsph.ru</a>
									</td>
								</tr>
							</table>
							</body>
							</html>';
				$headers = "From: teamsupport@f0523531.xsph.ru\r\n";
				$headers .= "Reply-To: teamsupport@f0523531.xsph.ru\r\n";
				$headers .= "CC: teamsupport@f0523531.xsph.ru\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=utf-8\r\n";;

				mail($to, $subject, $message, $headers);

				$query_id = "SELECT id FROM user_info WHERE email ='$email'";
				$result_id = $connect->query($query_id);
				if($result_id->num_rows==1)
				{
					http_response_code(201);

					 $res=[
					'status' => true,
					'user_id' => $id];

					echo json_encode($res);
				}
				
			}
		}
		else
		{
		    $res=[
				'status' => false,
				'error' => 'Такой email уже существует'];

				echo json_encode($res);
		}
	}
	
}

function newPinCode ($connect, $id){
	$key = generateKey();

	$query = "SELECT verefication_key FROM user_info WHERE verefication_key ='$key'";
	$result = $connect->query($query);

	if($result->num_rows==0)
	{
		$query ="UPDATE user_info SET verefication_key = '$key' WHERE id = $id";
			$result = $connect->query($query);
			if($result)
			{
				$query="SELECT email FROM user_info WHERE id = '$id'";

					$result = $connect->query($query);
					$list =array();
					$i=0;

					while ($row = $result->fetch_row()) {
						$to      = $row[0];
						$subject = 'Добро пожаловать в Ecoify';
						$message = '<!DOCTYPE html >
							<html lang="ru">
							<head>
							  <meta http-equiv="Content-Type" content="text/html" charset="utf-8" />
							</head>
							<body> 
							<table style ="max-width: 600px; background-color: #FEFEFE; font-family: Verdana;">
								<tr>
									<td style = "color: #0BB996; font-size: 60px; font-weight: lighter; text-align:center; margin-left: 64px; margin-right: 64px">
											<img src="http://f0523531.xsph.ru/images/app_icon.png" alt="logo" />
											Ecoify
									</td>
								</tr>
								<tr>
									<td height="32px">
									</td>
								</tr>
								<tr>
									<td style="text-align:center; color: #0BB996;  font-size: 50px; margin-left: 64px; margin-right: 64px">
										Добро пожаловать в Ecoify
									</td>
								</tr>
								<tr>
									<td height="32px">
									</td>
								</tr>
								<tr>
									<td style="text-align:center; color: #0BB996;  font-size: 25px; font-weight: lighter; margin-left: 64px; margin-right: 64px">
										Чтобы подтвердить действие в приложение введите следующий код:
									</td>
								</tr>
								<tr>
									<td height="16px">
									</td>
								</tr>
								<tr>
									<td style="text-align:center; color: #0BB996;  font-size: 50px; font-weight: lighter;  margin-left: 64px; margin-right: 64px">
											'.$key.'
									</td>
								</tr>
								<tr>
									<td height="64px">
									</td>
								</tr>
								<tr style="background-color: #226642">
									<td style="text-align:center; color: white; font-size: 20px; font-weight: lighter; padding-left: 64px; padding-right: 64px; padding-top: 16px; padding-bottom: 16px">
											На данное сообщение не нужно отвечать.<br><br>
											Если есть вопросы, вы можете написать на почту <a style="font-weight: bold; text-decoration: none; color: white;" href = "mailto: teamsupport@f0523531.xsph.ru">teamsupport@f0523531.xsph.ru</a>
									</td>
								</tr>
							</table>
							</body>
							</html>';
						$headers = "From: teamsupport@f0523531.xsph.ru\r\n";
						$headers .= "Reply-To: teamsupport@f0523531.xsph.ru\r\n";
						$headers .= "CC: teamsupport@f0523531.xsph.ru\r\n";
						$headers .= "MIME-Version: 1.0\r\n";
						$headers .= "Content-Type: text/html; charset=utf-8\r\n";;

						mail($to, $subject, $message, $headers);
					}

				$res=[
					'status' => true
				];

					echo json_encode($res);
			}
			else
			{
			    $res=[
					'status' => false,
					'eror' => 'error'];

					echo json_encode($res);
		}
	}
}

function generateKey ()
{
	return rand(10000, 99999);
} 

function verificatePin($connect, $id, $pin)
{
    $query = "SELECT verefication_key FROM user_info WHERE id = $id";
    $result = $connect->query($query);
    if($result->num_rows>0)
    {
        $row = $result->fetch_row();
        if($row[0]==$pin)
        {
            $result = $connect->query($query);
            if($result)
            {
            	http_response_code(201);

				$res=[
				'status' => true];

				echo json_encode($res);
            }
        }
        else{
    	$res=[
    		'status' => false,
    		'error'=> "Неправильный код"
			];

			echo json_encode($res);
    	}
    }
}

function insertLogin($connect, $id, $login)
{
	$query="SELECT login FROM user_info WHERE login = '$login' ";
	$result = $connect->query($query);
	if($result->num_rows==0){
		$query_1 ="UPDATE user_info SET login = '$login' WHERE id = $id";
		$result_1 = $connect->query($query_1);
	
	    if($result_1)
	    {
	        http_response_code(201);

			$res=[
			'status' => true];

			echo json_encode($res);
	    }

	}
	else{

			$res=[
			'status' => false,
			'error' => 'Такой логин уже занят'
			];

			echo json_encode($res);
	}
	
}

function insertPassword($connect, $id, $password)
{
	$query="SELECT password_hash FROM user_info WHERE id =$id ";

	$result = $connect->query($query);
	if($result->num_rows==0)
    {
		$query_passwd ="UPDATE user_info SET password_hash = '$password' WHERE id = '$id';
		UPDATE user_info SET verification='1', verefication_key='' WHERE id = $id;";
		$result_passwd = $connect->multi_query($query_passwd);

	    if($result_passwd)
	    {
	        http_response_code(201);

			$res=[
			'status' => true];

			echo json_encode($res);
	    }

    }
    else{
    	while ($row = $result->fetch_row()) {
    		if($row[0]==$password){

					$res=[
					'status' => false,
					'error'=>"Новый пароль и старый пароль не должены совпадать"];

					echo json_encode($res);

    		}
    		else{
    			$query ="UPDATE user_info SET password_hash = '$password' WHERE id = $id;
				UPDATE user_info SET verification='1', verefication_key='' WHERE id = $id;";
				$result = $connect->multi_query($query);

			    if($result)
			    {
			        http_response_code(201);

					$res=[
					'status' => true];

					echo json_encode($res);
			    }
    		}
    	}

    }
}

function sugnIn ($connect, $email)
{
	$query = "SELECT id, password_hash FROM user_info WHERE email='$email'";


    $result = $connect->query($query);
    if($result->num_rows>0)
    {
    	$row = $result->fetch_row();

    	http_response_code(201);

    	$res[] =  array(
    		'id' => $row[0],
			'password_hash' => $row[1]
    	);

    }

    echo json_encode($res);
}

function getTest ($connect)
{
    $query = "SELECT login FROM user_info";
    $result = $connect->query($query);
    if($result->num_rows>0)
    {
        $res[] =  array(	);
    	while($row = $result->fetch_row())
    	{

    	}
    
    }
    echo json_encode($res);
}

function deleteEmail($connect, $id){
    $query = "DELETE FROM user_info WHERE id='$id'";
    
    $result = $connect->query($query);

    if($result)
    {
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
    else
    {
        http_response_code(403);
    }
    
}

function getPoints($connect, $lat, $lng, $radius)
{
    $query="SELECT id, lat, lng, text, id_user FROM place WHERE (111111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS($lat)) * COS(RADIANS(lat)) * COS(RADIANS($lng - lng)) + SIN(RADIANS($lat)) * SIN(RADIANS(lat))))))<$radius AND status ='added' ";

	$result = $connect->query($query);
	$list =array();
	$i=0;

	while ($row = $result->fetch_row()) {
	        
	    http_response_code(201);
	    
		$res =[
		    'id' => $row[0],
		    'lat' => $row[1],
		    'lng' => $row[2],
		    'text' => $row[3],
		    'id_user' => $row[4],
		    ];
		    $list[$i++]= $res;
	}
    if($list)
        echo json_encode($list);
}

function getPointsBetween($connect, $lat, $lng, $radius, $from, $num)
{
	$query="SELECT id, lat, lng, text, id_user, (SELECT place_image_before.image FROM place_image_before WHERE place_image_before.id_place = place.id LIMIT 1 ) AS image , (111111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS($lat)) * COS(RADIANS(lat)) * COS(RADIANS($lng - lng)) + SIN(RADIANS($lat)) * SIN(RADIANS(lat)))))) As distance, status FROM place WHERE (111111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS($lat)) * COS(RADIANS(lat)) * COS(RADIANS($lng - lng)) + SIN(RADIANS($lat)) * SIN(RADIANS(lat)))))) <= $radius ORDER BY (111111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS($lat)) * COS(RADIANS(lat)) * COS(RADIANS($lng - lng)) + SIN(RADIANS($lat)) * SIN(RADIANS(lat)))))) LIMIT $num OFFSET $from";

	$result = $connect->query($query);
	$list =array();
	$i=0;

	while ($row = $result->fetch_row()) {
	    if(pow($row[1]-$lat, 2) + pow($row[2]-$lng, 2) <= pow($radius, 2))
	    {
	        
	    http_response_code(201);
	    
		$res =[
		    'id' => $row[0],
		    'lat' => $row[1],
		    'lng' => $row[2],
		    'text' => $row[3],
		    'img_url' => $row[5],
		    'id_user' => $row[4],
		    'distance' => $row[6],
		    'status' => $row[7]
		    ];
		    $list[$i++]= $res;
	    }
	}
    if($list)
        echo json_encode($list);
}

function bool_to_tinyint($bool)
{
	return $bool? 1: 0;
}

function newMarker($connect, $lat, $lng, $anonim, $description, $id_user, $type, $paper, $plastic, $glass, $metal, $food_waste, $electriccal){


    $query="INSERT INTO place( lat, lng, text, id_user, type, anonim, paper, plastic, glass, metal, food_waste, electrical) VALUES ($lat, $lng, '$description', $id_user, $type, $anonim, $paper, $plastic, $glass, $metal, $food_waste, $electriccal)";
    $result = $connect->query($query);
    if($result)
    {
    	$id = mysqli_insert_id($connect);
    	$query_2="UPDATE user_info SET add_num = add_num+1, score_points = score_points +10 WHERE id = '$id_user';
    	INSERT INTO place_history(id_place, id_user, action) VALUES ($id, '$id_user', 'add'); ";
    	$result_2 = $connect->multi_query($query_2);
    	 if($result_2)
    	 {
				http_response_code(201);

				$res=[
				'status' => true,
				'id'=> $id
				];
    	 }
		echo json_encode($res);
    }
    else
    {
        //http_response_code(403);
    }
}

function markerAddImage($connect, $id, $image, $type)
{
	if($type == "before")
		$query="INSERT INTO place_image_before(id_place, image) VALUES ($id, '$image')";
	else
		$query="INSERT INTO place_image_after(id_place, img_url) VALUES ($id, '$image')";

	$result = $connect->query($query);

	if($result)
    {
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function newImage($image, $exp){
	$percent = 0.5;
	$file_name = uniqid();
    $realImage = base64_decode($image);
    $path =  "images/".$file_name.".jpg";
    $path_small =  "images/small/".$file_name.".jpg";
    file_put_contents($path, $realImag);
    $bin = base64_decode($image);
    $im = imageCreateFromString($bin);
    if (!$im) {
        http_response_code(403);
    }
    imagejpeg($im, $path);


    list($width, $height) = getimagesize($path);
    $newwidth = $width * $percent;
	$newheight = $height * $percent;
    $thumb = imagecreatetruecolor($newwidth, $newheight);
    $source = imagecreatefromjpeg($path);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    imagejpeg($thumb, $path_small);

    http_response_code(201);
	$res=[
	'status' => true,
	'image' => $file_name.".jpg"];
	echo json_encode($res);
}

function getPlace($connect, $id, $lat, $lng, $id_user)
{
    $query="SELECT place.status, place.lat, place.lng ,user_info.img_url, user_info.login, place.date, place.text, place.paper, place.plastic, place.glass, place.metal, place.food_waste, place.electrical, place.planned_date, (111111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS($lat)) * COS(RADIANS(lat)) * COS(RADIANS($lng - lng)) + SIN(RADIANS($lat)) * SIN(RADIANS(lat)))))) AS distance, place.date_change, place.type  FROM place JOIN user_info ON place.id_user = user_info.id WHERE place.id = $id";

	$result = $connect->query($query);

	while ($row = $result->fetch_row()) {

		$image_arr=[];
		$near_palce=[];
		$participants=[];

		$i=0;
		$query_img="SELECT image FROM place_image_before WHERE id_place = $id";

		$result_img = $connect->query($query_img);

		while ($row_img = $result_img->fetch_row()) {
			$image[$i++]=$row_img[0];
		}

		$i=0;
		$query_img_after="SELECT img_url FROM place_image_after WHERE id_place =  $id";

		$result_img_after = $connect->query($query_img_after);

		while ($row_img_after = $result_img_after->fetch_row()) {
			$image_after[$i++]=$row_img_after[0];
		}

		$query_img_after="SELECT COUNT(img_url) FROM place_image_after WHERE id_place =  $id";

		$result_img_after = $connect->query($query_img_after);

		while ($row_img_after = $result_img_after->fetch_row()) {
			$image_count = $row_img_after[0];
		}

		$empty_images = 8 - $image_count;
		$query_participants="SELECT user_info.id, place_group.role, user_info.login, user_info.img_url FROM place_group JOIN user_info ON user_info.id = place_group.id_user WHERE place_group.id_place = $id";

		$result_participants = $connect->query($query_participants);

		while ($row_participants = $result_participants->fetch_row()) {
			$participants[] = array(
				'id_user' => $row_participants[0],
				'role' => $row_participants[1],
				'login' => $row_participants[2],
				'user_image' => $row_participants[3]);
		}

		$query_nearby="SELECT place.id, place.text, (SELECT place_image_before.image FROM place_image_before WHERE place_image_before.id_place = place.id LIMIT 1) AS image, (111111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS($row[1])) * COS(RADIANS(lat)) * COS(RADIANS($row[2] - lng)) + SIN(RADIANS($row[1])) * SIN(RADIANS(lat)))))) AS distance, status FROM place WHERE (111111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS($row[1])) * COS(RADIANS(lat)) * COS(RADIANS($row[2] - lng)) + SIN(RADIANS($row[1])) * SIN(RADIANS(lat))))))<1000 AND place.id!=$id ORDER BY (111111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS($row[1])) * COS(RADIANS(lat)) * COS(RADIANS($row[2] - lng)) + SIN(RADIANS($row[1])) * SIN(RADIANS(lat)))))) LIMIT 5";
	
		$result_nearby = $connect->query($query_nearby);

		while ($row_nearby = $result_nearby->fetch_row()) {
			$place[] = array(
				'id' => $row_nearby[0],
				'text' => $row_nearby[1],
				'image' => $row_nearby[2],
				'distance'=> $row_nearby[3],
				'status'=> $row_nearby[4]
			);
		}

		$isParticipant = false;
		$query_friend ="SELECT * FROM place_group WHERE id_user = '$id_user' AND id_place = $id";
		$result_friend = $connect->query($query_friend);
		if($result_friend->num_rows>0)
		{
			$isParticipant = true;
		}

		$history;

		$query_history = "SELECT place_history.id_user, user_info.login, place_history.action, place_history.date_of_action FROM place_history JOIN user_info ON place_history.id_user = user_info.id WHERE id_place=$id ORDER BY date_of_action DESC";

		$result_history = $connect->query($query_history);

		while ($row_history = $result_history->fetch_row()) {
			$history[] = array(
				'id'=> $row_history[0],
				'login' => $row_history[1],
				'action' => $row_history[2],
				'date_of_action' => $row_history[3],
				'isMe' => $row_history[0] == $id_user? true: false
			);
		}


		//print_r($participants);
		//print_r($near_palce);	
		$postList[] =  array(
			'status' => $row[0],
			'lat' => $row[1],
			'lng' => $row[2],
			'user_image' => $row[3],
			'user_login' => $row[4],
			'date' => $row[5],
			'image_before' => $image,
			'image_after'=> $image_after,
			'description' => $row[6],
			'distance' => $row[14],
			'nearby'=> $place,
			'type_of_waste'=> array($row[7], $row[8], $row[9], $row[10],$row[11],$row[12]),
			'planned_date'=> $row[13],
			'participaints'=>$participants,
			'role' => $participants[array_search($id_user, array_column($participants, 'id_user'))]['role'],
			"image_last"=> $empty_images,
			"isParticipant"=> $isParticipant,
			"date_change"=> $row[15],
			"history"=> $history,
			"type" => $row[16]
			);
			http_response_code(201);
	}
	echo json_encode($postList);
}

function addActivity($connect, $type, $place_id, $user_id)
{
	 $query="INSERT INTO activity(type, place_id, id_user) VALUES ('$type', $place_id, '$user_id')";
    
    $result = $connect->query($query);

    if($result)
    {
        http_response_code(201);


		$res=[
		'status' => true,
		'activity_id' => $connect->insert_id];

		echo json_encode($res);
    }
    else
    {
        http_response_code(403);
    }

}

function addPlace_Activity($connect, $plcae_id, $activity_id)
{
	 $query="INSERT INTO plcae_activity (id_post, id_place) VALUES ('$activity_id', '$plcae_id');";
    
    $result = $connect->query($query);

    if($result)
    {
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
    else
    {
    	
       http_response_code(403);
    }

}

function getNearPoints($connect, $lat, $lng, $radius, $from, $num)
{
	$query ="SELECT place.lat, place.lng ,user_info.img_url, user_info.login, place.date, place.img_url, place.text FROM place JOIN user_info ON place.id_user = user_info.id WHERE POw(place.lat - $lat, 2) + POW(place.lng - $lng, 2) <= POW($radius, 2) AND DATE(date_of_add) > (NOW() - INTERVAL 7 DAY) ORDER BY POW(POw(place.lat - $lat, 2) + POW(place.lng - $lng, 2),0.5) DESC LIMIT $num OFFSET $from";

	$result = $connect->query($query);
	$list =array();
	$i=0;

	while ($row = $result->fetch_row()) {
	    if(pow($row[1]-$lat, 2) + pow($row[2]-$lng, 2) <= pow($radius, 2))
	    {
	        
	    
	    
		$res =[
		    'id' => $row[0],
		    'lat' => $row[1],
		    'lng' => $row[2],
		    'text' => $row[3],
		    'img_url' => $row[4],
		    'id_user' => $row[5]
		    ];
		    $list[$i++]= $res;
	    }
	}
    if($list)
    {
    	http_response_code(201);
        echo json_encode($list);
    }
}

function getMessages($connect, $id_user){
	$query ="SELECT conversation.id FROM conversation JOIN participaints ON conversation.id = participaints.conversation_id WHERE users_id =$id_user";

	$result = $connect->query($query);
	$messages =array();
	$j=0;

	while ($row = $result->fetch_row()) {
		$query_message ="SELECT DISTINCT user_info.id, user_info.img_url, user_info.login, message.message, message.date_send, message.date_read FROM participaints
			JOIN message ON message.user_id = participaints.users_id
			JOIN conversation ON conversation.id = participaints.conversation_id
			JOIN user_info On user_info.id = participaints.users_id
			WHERE message.conversation_id=$row[0] LIMIT 20";
		$result_message = $connect->query($query_message);
		$conversation =array();
		$i=0;
		$prev_isMe= false;
		$messageType= 0;
		while ($row_message = $result_message->fetch_row())
		{
			if($row_message[0]==$id_user)
			{
				$isMe = true;
			}
			else
			{
				$isMe = false;
			}
			if($messageType == 0)
			{
				$messageType = 1;
			}
			else
			{
				if($isMePriv!=$isMe){

					$messageType = 2;
				}
				else{
					$messageType = 1;
				}
			}
			$res =[
		    'isMe' =>  $isMe,
		    'profileImg'=> $row_message[1],
		    'profileLogin'=> $row_message[2],
		    'message'=> $row_message[3],
		    'messageType'=> $messageType,
		    'time'=> $row_message[4],
		    'is_seen'=> NULL
		    ];
		    
		    $isMePriv = $isMe;

		    $conversation[$i++]=$res;
		}	
		$messages[$j++]=[
			'conversation_id' => $row[0],
			'conversation' => $conversation
		];
	}
	if($messages){
		http_response_code(201);
        echo json_encode($messages);
	}
}

function getUsers ($connect, $exp, $id){
	$query ="SELECT DISTINCT id, img_url, login FROM user_info WHERE login REGEXP '$exp' AND user_info.id != '$id' AND verification = 1";
	$result = $connect->query($query);
	$users =array();
	$j =0;
	while ($row = $result->fetch_row())
	{
		$isFriend = false;
		$query_friend ="SELECT * FROM frends WHERE frends.id_user = $id AND frends.id_friend='$row[0]'";
		$result_friend = $connect->query($query_friend);
		if($result_friend->num_rows>0)
		{
			$isFriend = true;
		}
		$users[$j++]=[
			'id'=> $row[0],
			'login' => $row[2],
			'imag_url' => $row[1],
			'isFriend' => $isFriend
		];
	}
	if($users){
		http_response_code(201);
        echo json_encode($users);
	}
}

function getFriends ($connect, $id){
	$query="SELECT user_info.id, user_info.img_url, user_info.login FROM frends JOIN user_info ON frends.id_friend = user_info.id WHERE frends.id_user = '$id' AND frends.status ='friend'";
	$result = $connect->query($query);
	$users =array();
	$j =0;
	while ($row = $result->fetch_row())
	{
		$users[$j++]=[
			'id'=> $row[0],
			'login' => $row[2],
			'imag_url' => $row[1]
		];
	}
	if($users){
		http_response_code(201);
        echo json_encode($users);
	}
}

function getFollow ($connect, $id){
	$query="SELECT user_info.id, user_info.img_url, user_info.login FROM frends JOIN user_info ON frends.id_friend = user_info.id WHERE frends.id_user = '$id' AND frends.status ='follow'";
	$result = $connect->query($query);
	$users =array();
	$j =0;
	while ($row = $result->fetch_row())
	{
		$users[$j++]=[
			'id'=> $row[0],
			'login' => $row[2],
			'imag_url' => $row[1]
		];
	}
	if($users){
		http_response_code(201);
        echo json_encode($users);
	}
}

function getFollowers ($connect, $id){
	$query="SELECT user_info.id, user_info.img_url, user_info.login FROM frends JOIN user_info ON frends.id_friend = user_info.id WHERE frends.id_user = '$id' AND frends.status ='follower'";
	$result = $connect->query($query);
	$users =array();
	$j =0;
	while ($row = $result->fetch_row())
	{
		$users[$j++]=[
			'id'=> $row[0],
			'login' => $row[2],
			'imag_url' => $row[1]
		];
	}
	if($users){
		http_response_code(201);
        echo json_encode($users);
	}
}


function addFriends ($connect, $id, $id_friend){
	$query="INSERT INTO frends(id_user, id_friend, status) VALUES ('$id', '$id_friend', 'follow'), ('$id_friend', '$id', 'follower')";
	$result = $connect->query($query);

    if($result)
    { 
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
    else
    {
        //http_response_code(403);
    }

}
function followerToFriend($connect, $id, $id_friend){
	$query="UPDATE frends SET status='friend' WHERE id_user = '$id' AND id_friend = '$id_friend';
			UPDATE frends SET status='friend' WHERE id_user = '$id' AND id_friend = '$id_friend';
	";
	$result = $connect->multi_query($query);

    if($result)
    { 
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
    else
    {
        //http_response_code(403);
    }
}
function friendToFollower($connect, $id, $id_friend){
	$query="UPDATE frends SET status='follow' WHERE id_user = '$id' AND id_friend = '$id_friend';
			UPDATE frends SET status='follower' WHERE id_user = '$id' AND id_friend = '$id_friend';
	";
	$result = $connect->multi_query($query);

    if($result)
    { 
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
    else
    {
        //http_response_code(403);
    }
}

function deleteFriends($connect, $id, $id_friend){
    $query = "DELETE FROM frends WHERE id_user ='$id' AND id_friend ='$id_friend';";
    $query .= "DELETE FROM frends WHERE id_user ='$id_friend' AND id_friend ='$id';";
	$result = $connect->multi_query($query);
    if($result)
    {
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
    else
    {
        //http_response_code(403);
    }
}
///SELECT id, lat, lng, text, img_url, id_user FROM place WHERE POW(POW(lat-53.456710 ,2)+POW(lng-56.043968 ,2),0.5)<=10000 ORDER BY POW(POW(lat-53.456710 ,2)+POW(lng-56.043968 ,2),0.5)  LIMIT 20 OFFSET 20////

function getConversation($connect, $id, $from, $num, $id_user){
	$query ="SELECT DISTINCT user_info.id, user_info.img_url, user_info.login, message.message, message.date_send, message.date_read FROM participaints
			JOIN message ON message.user_id = participaints.users_id
			JOIN conversation ON conversation.id = participaints.conversation_id
			JOIN user_info On user_info.id = participaints.users_id
			WHERE message.conversation_id=$id LIMIT $num OFFSET $from";
	$result = $connect->query($query);
	$messages =array();
	$j =0;
	while ($row = $result->fetch_row())
	{
		if($row[0]==$id_user)
			{
				$isMe = true;
			}
			else
			{
				$isMe = false;
			}
			if($messageType == 0)
			{
				$messageType = 1;
			}
			else
			{
				if($isMePriv!=$isMe){

					$messageType = 2;
				}
				else{
					$messageType = 1;
				}
			}
			$res =[
		    'isMe' =>  $isMe,
		    'profileImg'=>$row[1],
		    'profileLogin'=> $row[2],
		    'message'=> $row[3],
		    'messageType'=> $messageType,
		    'time'=> $row[4],
		    'is_seen'=> NULL
		    ];
		    
		    $isMePriv = $isMe;

		    $messages[$j++]=$res;
	}
	if($messages){
		http_response_code(201);
        echo json_encode($messages);
	}
}

function addLikeToPost($connect, $id_user, $id_post){
	$query="INSERT INTO likes(id_post, id_user) VALUES ($id_post, '$id_user');
	";
	$result = $connect->query($query);

    if($result)
    { 
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function removeLikeToPost($connect, $id_user, $id_post){
	$query="DELETE FROM likes WHERE id_post = $id_post AND id_user = '$id_user'";
	$result = $connect->query($query);

    if($result)
    { 
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function FirstStepCleanPlcace($connect, $id_user, $id_place, $date){
	$query ="UPDATE place SET status='planned', planned_date='$date', planned_type='open' WHERE id = $id_place;
    	INSERT INTO place_history(id_place, id_user, action) VALUES ($id_place, '$id_user', 'planned');";

	$result = $connect->multi_query($query);
	 if($result)
    { 
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function CleanPlcace($connect, $id_user, $id_place){
	$query ="UPDATE place SET status='cleared' WHERE id = $id_place;
		INSERT INTO place_group(id_user, id_place, role) VALUES ('$id_user', $id_place, 'admin');
    	INSERT INTO place_history(id_place, id_user, action) VALUES ($id_place, '$id_user', 'clear');";

	$result = $connect->multi_query($query);
	if($result)
    { 
   
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function StillNotCleanPlcace($connect, $id_user, $id_place){
	$query ="UPDATE place SET status='added' WHERE id = $id_place";

	$result = $connect->query($query);

	$query_2 ="INSERT INTO place_history(id_place, id_user, action) VALUES ($id_place, '$id_user', 'notclean');";

	$result_2 = $connect->query($query_2);
	 if($result && $result_2)
    {  
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function ChangeProfileImage($connect, $id_user, $url_image){
	$query = "UPDATE user_info SET img_url='$url_image' WHERE id = '$id_user'";
	$result = $connect->query($query);
	 if($result)
    { 
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function ChangeLogin($connect, $id_user, $login){
	$query = "UPDATE user_info SET login='$login' WHERE id = '$id_user'";
	$result = $connect->query($query);
	 if($result)
    { 
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function ChangePasswordGenPin($connect, $id_user){
	$pin = generateKey();
	$query = "UPDATE user_info SET verefication_key='$pin' WHERE id = '$id_user'";
	$result = $connect->query($query);
	 if($result)
    { 
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function ChangePasswordGenPinWithEmail($connect, $email){
	$pin = generateKey();
	$id_user ;
	$query ="SELECT id FROM user_info WHERE email = '$email'";

	$result = $connect->query($query);
	$messages =array();
	$j =0;
	while ($row = $result->fetch_row())
	{
		$id_user=$row[0];
	}

	$query = "UPDATE user_info SET verefication_key='$pin' WHERE id = '$id_user'";
	$result = $connect->query($query);
	 if($result)
    { 
        http_response_code(201);

		$res=[
			'id' => $id_user,
			'status' => true];

		echo json_encode($res);
    }
}

function ChangePasswordVerificatePin($connect, $id, $pin)
{
    $query = "SELECT verefication_key FROM user_info WHERE id = '$id'";
    $result = $connect->query($query);
    if($result->num_rows>0)
    {
        $row = $result->fetch_row();
        if($row[0]==$pin)
        {
            if($result)
            {
            	$query_temp = "UPDATE user_info SET verefication_key='' WHERE id = '$id_user'";
				$result_temp = $connect->query($query_temp);
				 if($result_temp)
			    {
			    http_response_code(201);

				$res=[
				'status' => true];

				echo json_encode($res); 
			    }
            }
        }
    }
}

function getUserData($connect, $main_id_user, $id_user, $from, $num){
	$query_user ="SELECT id, login,img_url, date_of_registretion FROM user_info WHERE id='$id_user'";

	$result_user = $connect->query($query_user);

	while ($row_user = $result_user->fetch_row()) {
		$postList[] =  array(
			'id_user' => $row_user[0],
			'login' => $row_user[1],
			'img_url' => $row_user[2], 
			'datatime' => $row_user[3],);
	}

	$query="SELECT activity.type, user_info.login, place.text,activity.date_of_add, activity.id, user_info.id, place.id, place.planned_date FROM place JOIN activity ON place.id = activity.place_id JOIN user_info ON user_info.id=activity.id_user WHERE  activity.id_user = '$id_user' ORDER BY activity.date_of_add DESC LIMIT $num OFFSET $from";
	$result = $connect->query($query);
	$list =array();
	$i=0;

	while ($row = $result->fetch_row()) {

	    $image_arr=[];
		$j=0;
		$query_img="SELECT image FROM place_image_before WHERE id_place = $row[6]";

		$result_img = $connect->query($query_img);

		while ($row_img = $result_img->fetch_row()) {
			$image[$j++]=$row_img[0];
		}

		$k=0;
		$query_participants="SELECT user_info.id, place_group.role, user_info.login, user_info.img_url FROM place_group JOIN user_info ON user_info.id = place_group.id_user WHERE place_group.id_place = $row[6]";

		$result_participants = $connect->query($query_participants);

		while ($row_participants = $result_participants->fetch_row()) {
			$participants[$k++] = $row_participants[3];
		}
	    		
		$res =[
			'id'=>$row[4],
			'place_id'=>$row[6],
		    'type'=> $row[0],
		    'login' => $row[1],
		    "user_id"=>$row[5],
		    'text'=> $row[2],
		    'date'=> $row[3],
		    'images'=>$image,
		    'planned_date'=>$row[7],
		    'participaints'=>$participants
		    ];
		    $list[$i++]= $res;
	}

	$isFirned = false;

	$query_isFriend ="SELECT * FROM frends WHERE id_user='$main_id_user' AND id_friend='$id_user'";

	$result_isFriend = $connect->query($query_isFriend);
	if($result_isFriend->num_rows>0){
		$isFirned = true;
	}

	if($postList!=null || $list !==null || $isFriend !=null)
	{
		http_response_code(201);
		echo json_encode(
	 array(
	 	'user_date' => $postList, 
	 	'actions' => $list,
	 	"is_friend" => $isFirned
	 )
	);
	}
	
}
function getParticipants($connect, $id_user, $id_place, $from, $num){
	 $query = "SELECT user_info.id, place_group.role, user_info.login, user_info.img_url FROM place_group JOIN user_info ON place_group.id_user = user_info.id WHERE place_group.id_place = $id_place";

	 $result = $connect->query($query);

	 while ($row = $result->fetch_row()) {
	 	$isFriend = false;
		$query_friend ="SELECT * FROM frends WHERE frends.id_user = $id_user, AND frends.id_friend='$row[0]'";
		$result_friend = $connect->query($query_friend);
		if($result_friend->num_rows>0)
		{
			$isFriend = true;
		}
		$isMe = false;
		if($id_user==$row[0]){
			$isMe = true;
		}
	 	$postList[] =  array(
			'id' => $row[0],
			'role' => $row[1],
			'login' => $row[2], 
			'user_image' => $row[3],
			'isFriend'=> $isFriend,
			'isMe'=> $isMe
		);
	 }
	 if($postList)
	 {
	 	http_response_code(201);
	 	echo json_encode($postList); 
	 }
}

function getCurrentParticipants($connect, $id_user, $id_place, $from, $num){
	 $query = "SELECT user_info.id, place_group.role, user_info.login, user_info.img_url FROM place_group JOIN user_info ON place_group.id_user = user_info.id WHERE place_group.id_place = $id_place AND status ='current'";

	 $result = $connect->query($query);

	 while ($row = $result->fetch_row()) {
	 	$isFriend = false;
		$query_friend ="SELECT * FROM frends WHERE frends.id_user = $id_user, AND frends.id_friend='$row[0]'";
		$result_friend = $connect->query($query_friend);
		if($result_friend->num_rows>0)
		{
			$isFriend = true;
		}
		$isMe = false;
		if($id_user==$row[0]){
			$isMe = true;
		}
	 	$postList[] =  array(
			'id' => $row[0],
			'role' => $row[1],
			'login' => $row[2], 
			'user_image' => $row[3],
			'isFriend'=> $isFriend,
			'isMe'=> $isMe
		);
	 }
	 if($postList)
	 {
	 	http_response_code(201);
	 	echo json_encode($postList); 
	 }
}

function getPossibleParticipants($connect, $id_user, $id_place, $from, $num){
	 $query = "SELECT user_info.id, place_group.role, user_info.login, user_info.img_url, place_group.approved FROM place_group JOIN user_info ON place_group.id_user = user_info.id WHERE place_group.id_place = $id_place AND status = 'possible'";

	 $result = $connect->query($query);

	 while ($row = $result->fetch_row()) {
	 	$isFriend = false;
		$query_friend ="SELECT * FROM frends WHERE frends.id_user = $id_user, AND frends.id_friend='$row[0]'";
		$result_friend = $connect->query($query_friend);
		if($result_friend->num_rows>0)
		{
			$isFriend = true;
		}
		$isMe = false;
		if($id_user==$row[0]){
			$isMe = true;
		}
		$approved = false;
		if($row[4]=="1"){
			$approved = true;
		}
	 	$postList[] =  array(
			'id' => $row[0],
			'role' => $row[1],
			'login' => $row[2], 
			'user_image' => $row[3],
			'isFriend'=> $isFriend,
			'isMe'=> $isMe,
			'approved'=>$approved
		);
	 }
	 if($postList)
	 {
	 	http_response_code(201);
	 	echo json_encode($postList); 
	 }
}

function becomePossibleParticipants($connect, $id_place, $id_user){
	$query = "INSERT INTO place_group(id_user, id_place, role, status, approved) VALUES ('$id_user', $id_place, 'regular', 'possible', 1)";

	$result = $connect->query($query);
	 if($result)
    { 
    	http_response_code(201);

		$res=[
			'status' => true];

		echo json_encode($res);
    }
}

function becomeCurrentParticipants($connect, $id_place, $id_user){
	$query = "INSERT INTO place_group(id_user, id_place, role, status, approved) VALUES ('$id_user', $id_place, 'regular', 'current', 1)";

	$result = $connect->query($query);
	 if($result)
    { 
    	http_response_code(201);

		$res=[
			'status' => true];

		echo json_encode($res);
    }
}


function exitParticipants($connect, $id_place, $id_user){
	$query = "DELETE FROM place_group WHERE id_user = '$id_user' AND id_place=$id_place";

	$result = $connect->query($query);
	 if($result)
    { 
    	http_response_code(201);

		$res=[
			'status' => true];

		echo json_encode($res);
    }
}

function RemeberPasswordCancel($connect, $id)
{
	$query ="UPDATE user_info SET verefication_key='' WHERE id = '$id';";
	$result = $connect->query($query);

    if($result)
    {
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function ImageDeleteOld($connect, $id){
	$query = "SELECT img_url FROM user_info WHERE id = '$id'";
    $result = $connect->query($query);
    while ($row = $result->fetch_row()) {
    	if($row[0]!="profile_image_placeholder.png"){
    		//echo $row[0];
    		//echo "images/$row[0]";
    		unlink("images/$row[0]");
    		unlink("images/small/$row[0]");
        	http_response_code(201);
    
    		$res=[
    			'status' => true];
    
    		echo json_encode($res);
        }
    }
}

function findParticipants($connect, $exp, $id, $place_id){
	$query ="SELECT DISTINCT id, img_url, login FROM user_info WHERE id = '$exp'  OR (login REGEXP '$exp') AND (user_info.id != '$id') AND verification = 1 AND id NOT IN (SELECT id FROM place_group WHERE place_group.id_place=$place_id) ";
	$result = $connect->query($query);
	$users =array();
	$j =0;
	while ($row = $result->fetch_row())
	{
		$isFriend = false;
		$isParticipants = false;
		$query_friend ="SELECT * FROM frends WHERE frends.id_user = $id AND frends.id_friend='$row[0]'";
		$result_friend = $connect->query($query_friend);
		if($result_friend->num_rows>0)
		{
			$isFriend = true;
		}
		$users[$j++]=[
			'id'=> $row[0],
			'login' => $row[2],
			'user_image' => $row[1],
			'isFriend' => $isFriend,
		];
	}
	if($users){
		http_response_code(201);
        echo json_encode($users);
	}
}

function AddPossibleParticipant($connect, $id, $place_id){
	$query ="INSERT INTO place_group(id_user, id_place, role, status) VALUES ('$id', $place_id, 'regular', 'possible');";
	$result = $connect->query($query);

    if($result)
    {
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function SendReport($connect, $place_id, $id_user, $type, $text){
	$query ="INSERT INTO report_place(id_place, type, report_text, status) VALUES ($place_id, $type, '$text', 'send');";

	$result = $connect->query($query);

	$query_2 ="INSERT INTO report_participaints(id_report, id_user) VALUES (".$connect->insert_id.", $id_user);";
	$result_2 = $connect->query($query_2);

    if($result && $result_2)
    {
        http_response_code(201);

		$res=[
		'status' => true,
		'id'=> mysqli_insert_id($connect)];

		echo json_encode($res);
    }
}

function SendImageReport($connect, $id_report, $url){
	$query ="INSERT INTO report_image(id_report, url) VALUES ($id_report, '$url');";

	$result = $connect->query($query);

    if($result)
    {
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function getRerorts($connect, $from, $num, $id){
	$image = [];
	$image_after = [];
	$image_img_report = [];
	$image_text_report = [];
	$image_type_report = [];
	$history = [];
	$res = [];
	$j = 0;

	$query = "SELECT report_place.id, report_place.id_place, place.text, place.lat, place.lng, report_place.date_add, report_place.date_change, place.date, place.date_change, (SELECT COUNT(*) FROM report_participaints WHERE id_report=report_place.id) AS participantsCount FROM report_place JOIN place ON report_place.id_place = place.id ORDER BY (SELECT COUNT(*) FROM report_participaints WHERE id_report=report_place.id) DESC, report_place.date_change DESC LIMIT $num OFFSET $from";

	$result = $connect->query($query);

	while ($row = $result->fetch_row())
	{
		$i=0;

		$query_img="SELECT image FROM place_image_before WHERE id_place = $row[1]";

		$result_img = $connect->query($query_img);

		while ($row_img = $result_img->fetch_row()) {
			$image[$i++]=$row_img[0];
		}

		$i=0;
		$query_img_after="SELECT img_url FROM place_image_after WHERE id_place =  $row[1]";

		$result_img_after = $connect->query($query_img_after);

		while ($row_img_after = $result_img_after->fetch_row()) {
			$image_after[$i++]=$row_img_after[0];
		}	

		$i=0;
		$query_img_report="SELECT url FROM report_image WHERE id_report =  $row[1]";

		$result_img_report = $connect->query($query_img_after);

		while ($row_img_report = $result_img_report->fetch_row()) {
			$image_img_report[$i++]=$row_img_report[0];
		}

		$i=0;
		$query_img_report="SELECT report_text.text FROM report_text WHERE id_report =  $row[0]";

		$result_text_report = $connect->query($query_img_report);

		while ($row_text_report = $result_text_report->fetch_row()) {
			$image_text_report[$i++]=$row_text_report[0];
		}

		$i=0;
		$query_type_report="SELECT type FROM report_type WHERE id_report =  $row[0]";

		$result_type_report = $connect->query($query_type_report);

		while ($row_type_report = $result_type_report->fetch_row()) {
			$image_type_report[$i++]=$row_type_report[0];
		}

		$query_history = "SELECT user_info.id, user_info.login, report_participaints.date FROM report_participaints JOIN report_place ON report_place.id=report_participaints.id_report JOIN user_info ON report_participaints.id_user = user_info.id WHERE report_place.id=1 ORDER BY report_participaints.date DESC";

		$result_history = $connect->query($query_history);

		while ($row_history = $result_history->fetch_row()) {
			$history[] = array(
				'id'=> $row_history[0],
				'login' => $row_history[1],
				'action' => 'add',
				'date_of_action' => $row_history[2],
				'isMe' => $row_history[0] == $id? true: false
			);
		}

		$res[$j++] = [
			'id_report' => $row[0],
			'id_place' => $row[1],
			'images_before' => $image,
			'images_after' => $image_after,
			'description' => $row[2],
			'lat' => $row[3],
			'lng' => $row[4],
			'report_date_add' => $row[5],
			'report_date_update' => $row[6],
			'history' => $history,
			'place_date_change'=> $row[8],
			'place_date_add' => $row[7],
			'reasons' => $image_type_report,
			'coments' => $image_text_report,
			'participantsCount' => $row[9]
		];
	}

	if($res){
		http_response_code(201);
		echo json_encode($res);
	}
}

function rejectedRerorts($connect, $id_report, $id_moder){
	$query ="UPDATE report_place SET id_moder='$id_moder', status ='rejected'  WHERE id =$id_report";


	$result = $connect->query($query);

    if($result)
    {
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}

function approvedRerorts($connect, $id_report, $id_moder){
	$query ="UPDATE report_place SET id_moder='$id_moder', status ='approved'  WHERE id =$id_report";

	echo $query;

	$result = $connect->query($query);

    if($result)
    {
        http_response_code(201);

		$res=[
		'status' => true];

		echo json_encode($res);
    }
}
