<?php

	//You need to change this with your RealtimeDB url and add 'collection_name.json' to the end
	//In my case, my collection name is 'messages'
	//the resulting url is given below
    $URL = "https://cs306-project-c7893-default-rtdb.firebaseio.com/Chats.json";

    function get_messages() { 
        global $URL;
        $ch = curl_init();
        curl_setopt_array($ch, [ CURLOPT_URL => $URL,
                                CURLOPT_POST => FALSE, // It will be a get request 
                                CURLOPT_RETURNTRANSFER => true,
								CURLOPT_SSL_VERIFYPEER => false, ]);
        $response = curl_exec($ch); 
        curl_close($ch);
        return json_decode($response, true); 
    }




	function send_msg($name, $msg, $time) { 
        global $URL;
        $ch = curl_init();
        $msg_json = new stdClass();
        $msg_json->msg = $msg;
        $msg_json->name = $name;
        $msg_json->time = date("H.i");
        $encoded_json_obj = json_encode($msg_json); 
        curl_setopt_array($ch, array(
									CURLOPT_URL => $URL,
                                    CURLOPT_POST => TRUE,
                                    CURLOPT_RETURNTRANSFER => TRUE,
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json' ),
                                    CURLOPT_POSTFIELDS => $encoded_json_obj ));
		$result = curl_exec($ch);
		curl_close($ch);
        
    }
	if (isset($_POST['usermsg'])) {
		$name = "User";
        $msg = $_POST['usermsg'];
		$time = date("H.i");
        send_msg($name ,$msg, $time );

    }
	$msg_res_json = get_messages();


	
?>


<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="styles.css">
</head>

<div class="menu">
<div class="back"><i class="fa fa-chevron-left" > <a class="back" href="welcome.php"></a></i></div>
<div class="name">Support</div>
<div class="last"><?=date("H.i")?></div>
</div>
<ol class="chat">
<?php
	if($msg_res_json != null)
	{
	$keys = array_keys($msg_res_json);

	for ($i = 0; $i < count($keys); $i++){
		$chat_msg = $msg_res_json[$keys[$i]];
		$name = $chat_msg['name'];
		$msg = $chat_msg['msg'];
		$time = $chat_msg['time'];
		if ($name == 'admin') {
			$from = 'other';
		} else {
			$from = 'self';
		}
		echo  '
		<li class="'.$from.'">
		<div class="avatar">
				<img src="https://i.imgur.com/DY6gND0.png" draggable="false"/>
			</div>
				<div class="msg">
					<p><b>'.$name.'</b></p>
					<p>'.$msg.'</p>
					<time>'.$time.'</time>
				</div>
			</li>';
		}
	}
	
	
?>
</ol>
<form name="form" action = "client_chat.php" method="POST">
    <input name="usermsg" class="textarea" type="text" placeholder="Type here!"/>
    <input id= "submitbtn" type="submit" style="display: none" />
</form>