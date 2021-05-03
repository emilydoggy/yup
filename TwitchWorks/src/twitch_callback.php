<?php

    /*
     * Copyright 2017-2021 HowToCompute. All Rights Reserved
     *
     * Endpoint /twitch_callback.
     *
     * Usage:
     * GET /twitch_callback, will check the key is valid and then redirect the user to twitch to log in and to authorize the app.
     *
     * Returns nothing, but processes a login request.
     */

    require("private/database.php");
    require("private/helpers.php");
	require("private/render_helpers.php");

    $sessionKey = $_GET['state'];
    $oauthToken = "";

    $CLIENT_ID = GetTwitchClientID();

    $CLIENT_SECRET = GetTwitchClientSecret();
    $REDIRECT_URI = GetTwitchRedirectURI();

    // Exchange the "temporary" token for a proper OAuth token we can use to actually do things
    $ch = curl_init("https://id.twitch.tv/oauth2/token");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    $fields = array(
                    'client_id' => $CLIENT_ID,
                    'client_secret' => $CLIENT_SECRET,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $REDIRECT_URI,
                    'code' => $_GET['code']
                    );

    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    $data = curl_exec($ch);

    $json = json_decode($data, true);
    $oauthToken = $json['access_token'];
    $refreshToken = $json['refresh_token'];
    $info = curl_getinfo($ch);

    // Retrieve information about the user whom the token belongs to.
    $user_info_opts = [
    "http" => [
    "method" => "GET",
    "header" => "Authorization: Bearer ".$oauthToken."\r\nClient-ID: ".$CLIENT_ID."\r\nAccept: application/json"
    ]
    ];

    $user_info_context = stream_context_create($user_info_opts);

    if(!$json = json_decode(file_get_contents('https://api.twitch.tv/helix/users', false, $user_info_context), true))
    {
		RenderFailure("error! Unable to fetch user info.");
        die();
    }

	if (!isset($json['data']) || count($json['data']) <= 0)
	{
		RenderFailure("error! Unable to fetch user info (no data).");
        die();
	}
	$user_data = $json['data'][0];

    $username = $user_data['login'];
    $displayName = $user_data['display_name'];

	// Use the email if set, and keep it at blank if it has not been set.
    $email = "";
	if (isset($user_data['email']))
	{
		$email = $user_data['email'];
	}

    /*
     The below code will update the user's entry in the database so the game can use it's token/verification code to retrieve the user's OAuth token
     */

    // Connect to the database
    $conn = DatabaseConnect();

    if (!($stmt = $conn->prepare("UPDATE `TwitchUsers` SET `Username` = ?, `OAuthToken` = ?, `RefreshToken` = ? WHERE `Key` = ?"))) {
        header("HTTP/1.1 500 Internal Server Error");
        RenderFailure("Prepare failed: (" . $conn->errno . ") " . $conn->error);
		die();
    }

    if (!$stmt->bind_param("ssss", $username, $oauthToken, $refreshToken, $sessionKey)) {
        header("HTTP/1.1 500 Internal Server Error");
        RenderFailure("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
		die();
    }

    // Execute the prepared query, and fetch the returned data
    $result = $stmt->execute();
    $stmt_result = $stmt->get_result();

    if (!$result) {
        header("HTTP/1.1 500 Internal Server Error");
        RenderFailure("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
		die();
    }
	else {
		RenderSuccess($username);
		die();
	}
?>
