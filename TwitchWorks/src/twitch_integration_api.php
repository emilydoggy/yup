<?php

    /*
     * Copyright 2017-2021 HowToCompute. All Rights Reserved
     *
     * Endpoint /twitch_integration_api.
     *
     * Usage:
     * GET /twitch_integration_api, will allow the game to create a twitch login request, and track it's status to process the login.
     *
     * Returns nothing, but handles
     */

    require("private/database.php");
    require("private/helpers.php");

    if ($_GET['action'] == "START")
    {
        // Start action - will set up a mechanism that allows a game's user to log in/authorize the game.

        // Create a cryptographically secure key/token to identify/authenticate the game in later requests.
        $sessionKey = bin2hex(openssl_random_pseudo_bytes(16));
        $sessionToken = bin2hex(openssl_random_pseudo_bytes(32));

        // Create a database connection
        $conn = DatabaseConnect();

        // Use a prepared query to add this "request" (consisting of a token and key) into the database.
        if (!($stmt = $conn->prepare("INSERT INTO TwitchUsers(`Token`, `Key`) VALUES (?, ?)"))) {
            header("HTTP/1.1 500 Internal Server Error");
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }

        if (!$stmt->bind_param("ss", $sessionToken, $sessionKey)) {
            header("HTTP/1.1 500 Internal Server Error");
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        if (!$stmt->execute()) {
            header("HTTP/1.1 500 Internal Server Error");
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }

        // Successfully created a "request"! Pass it back to the game to open the user's web browser to actually log in/authorize.
        header("Content-Type: application/json");
        $response = array(
            'key' => $sessionKey,
            'token' => $sessionToken
        );
        die(json_encode($response));
    }
    else if ($_GET['action'] == "POLL")
    {
        // Did the user provide the key and token?
        if(!isset($_GET['key']) or !isset($_GET['token']))
        {
            die("Invalid Login Attempt - Token Or Key Not Set. Please Try Loggin In Again.");
        }

        $sessionKey = $_GET['key'];
        $sessionToken = $_GET['token'];

        // Create a database connection
        $conn = DatabaseConnect();

        // Use a prepared SQL query to retrieve aditional data from the database so we can check if the user has authorized already, and if required pass the data back to the game. This will also validate the key/token.
        if (!($stmt = $conn->prepare("SELECT `ID`, `Username`, `OAuthToken`, `RefreshToken` FROM TwitchUsers WHERE `Key` = ? AND `Token` = ?"))) {
            header("HTTP/1.1 500 Internal Server Error");
            header("Content-Type: application/json");
            die(json_encode(array('key' => $sessionKey,'success' => false)));
        }

        if (!$stmt->bind_param("ss", $sessionKey, $sessionToken)) {
            header("HTTP/1.1 500 Internal Server Error");
            header("Content-Type: application/json");
            die(json_encode(array('key' => $sessionKey,'success' => false)));
        }

        $result = $stmt->execute();
        $stmt_result = $stmt->get_result();

        if (!$result) {
            header("HTTP/1.1 500 Internal Server Error");
            header("Content-Type: application/json");
            die(json_encode(array('key' => $sessionKey,'success' => false)));
        }


        while($row = $stmt_result->fetch_assoc()) {
            // Has the user not completed OAuth yet?
            if($row['Username'] == null or $row['OAuthToken'] == null)
            {
                // Indeed - we still need to wait for the user, so return false.
                header("Content-Type: application/json");
                die(json_encode(array('key' => $sessionKey,'success' => false)));
            }

            // The user's info has set, so assume the user has successfully logged in and pass the data back!
            $response = array(
                'key' => $sessionKey,
                'success' => true,
                'username' => $row['Username'],
                'token' => $row['OAuthToken'],
                'refresh_token' => $row['RefreshToken']
		    );

		    // Now that we have/are going to return the credentials to the user, there's no real need to keep them in our DB. Remove them to avoid the security risk of a DB filled up with credentials and to simply save space.
		    // NOTE: Don't fail the request if this fails - it really shouldn't fail, but there's no real point in both not removing the thing and failing the request (both result in the row not being removed but still succeeding login is a lot nicer for the user)
		    if ($delete_stmt = $conn->prepare("DELETE FROM TwitchUsers WHERE `ID`= ?")) {
	            if ($delete_stmt->bind_param("i", $row['ID']))
				{
					$delete_stmt->execute();
				}
	        }

			header("Content-Type: application/json");
			die(json_encode($response));
		}

        // Some unknown error occured!
        header("Content-Type: application/json");
        die(json_encode(array('key' => $sessionKey,'success' => false)));
    }
    else if ($_GET['action'] == "REFRESH")
    {
        // This code will (attempt to) exchange a refresh token for an access token. Requires a refresh token to be passed in.
        if(!isset($_GET['refresh_token']))
        {
            die("Invalid Refresh Attempt - Token Not Set. Please Try Loggin In Again.");
        }

        $refreshToken = $_GET['refresh_token'];
        $CLIENT_ID = GetTwitchClientID();
        $CLIENT_SECRET = GetTwitchClientSecret();

        // Exchange the "refresh" token for a proper OAuth token we can use to actually do things
        $ch = curl_init("https://id.twitch.tv/oauth2/token");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        $fields = array(
                        'client_id' => $CLIENT_ID,
                        'client_secret' => $CLIENT_SECRET,
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $refreshToken
                        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $data = curl_exec($ch);

        if (!$json = json_decode($data, true))
        {
            http_response_code(403);
            die("Error! Refresh token appears to be invalid");
        }

        if (!isset($json['access_token']))
        {
            http_response_code(403);
            die("Error! Refresh token appears to be invalid");
        }

        $oauthToken = $json['access_token'];

        // Successfully obtained token!
        $response = array(
            'success' => true,
            'token' => $oauthToken
        );

        header("Content-Type: application/json");
        die(json_encode($response));
    }
    else
    {
        die("Error occured! Unsupported request ".$_GET['action']."!");
    }
?>
