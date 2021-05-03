<?php
    /*
     * Copyright 2017-2021 HowToCompute. All Rights Reserved
     */
    // Turn off error reporting due to security considerations. Here because all files include private/helpers.php
    error_reporting(0);

    function GetTwitchClientID()
    {
		// Allow for an environment variable override if provided
		if (getenv("TWITCH_CLIENT_ID"))
		{
			return getenv("TWITCH_CLIENT_ID");
		}

		// Use the configuration file if one is not provided
        $config = parse_ini_file("config.ini", true);
        return $config['twitch']['client_id'];
    }

    function GetTwitchClientSecret()
    {
		// Allow for an environment variable override if provided
		if (getenv("TWITCH_CLIENT_SECRET"))
		{
			return getenv("TWITCH_CLIENT_SECRET");
		}

		// Use the configuration file if one is not provided
        $config = parse_ini_file("config.ini", true);
        return $config['twitch']['client_secret'];
    }

    function GetTwitchRedirectURI()
    {
		// Allow for an environment variable override if provided
		if (getenv("TWITCH_REDIRECT_URI"))
		{
			return getenv("TWITCH_REDIRECT_URI");
		}

		// Use the configuration file if one is not provided
        $config = parse_ini_file("config.ini", true);
        return $config['twitch']['twitch_redirect_uri'];
    }
?>
