<?php

    // Copyright 2017-2021 HowToCompute. All Rights Reserved

    /*
     * Endpoint /twitch_callback.
     *
     * Usage:
     * GET /twitch_callback, automatically called from twitch
     *
     * Returns nothing, but processes a login request.
     */

    require("private/database.php");
    require("private/helpers.php");
	require("private/render_helpers.php");

    // Did the user provide the key? We only need the key here (that way the
	// token won't show up in the browser history -> )
	// NOTE: TwitchWorks might still be sending the token: this is for
	// backwards-compatibility. This will be removed at some point in the future
	// once the majority of users have had a change to update their backend.
    if(!isset($_GET['key']))
    {
        RenderFailure("Invalid Login Attempt - Key Not Set. Please Try Loggin In Again.");
		die();
    }

    $sessionKey = $_GET['key'];

    $CLIENT_ID = GetTwitchClientID();
    $REDIRECT_URI = GetTwitchRedirectURI();

    $conn = DatabaseConnect();

    // Prepare a prepared query (to mitigate SQL injections) that will check if there are any results returned for the user's token (was it a login that was asked for, or is it a malicious attempt?).
    if (!($stmt = $conn->prepare("SELECT * FROM `TwitchUsers` WHERE `Key` = ?"))) {
        header("HTTP/1.1 500 Internal Server Error");
		RenderFailure("Prepare failed: (" . $conn->errno . ") " . $conn->error);
		die();
    }

    if (!$stmt->bind_param("s", $sessionKey)) {
        header("HTTP/1.1 500 Internal Server Error");
        RenderFailure("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
		die();
    }

    // Attempt to execute the prepared query
    $result = $stmt->execute();
    $stmt_result = $stmt->get_result();

    // Did the prepared query successfully execute?
    if (!$result) {
        header("HTTP/1.1 500 Internal Server Error");
        RenderFailure("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
		die();
    }

    // Not 1 result? Something must have gone wrong, or this was an incorrect login attempt.
    if ($stmt->num_rows >= "1") {
        RenderFailure("Invalid Login Attempt - Please Try Again");
		die();
    }

    # Redirect the user to the twitch authorization "page" so they can log in/authorize the app, and pass in the state to keep track of the request. - NOTE: This is now using the new authentication API/scopes.
    header("Location: https://id.twitch.tv/oauth2/authorize?response_type=code&scope=user:read:email+chat:read+chat:edit+channel:moderate+whispers:edit&client_id=".$CLIENT_ID."&redirect_uri=".$REDIRECT_URI."&state=".$sessionKey);
    echo("<h1>Redirecting you to Twitch...</h1>");
	die();
?>
