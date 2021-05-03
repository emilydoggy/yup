<!-- Copyright 2017-2021 HowToCompute. All Rights Reserved -->

<?php
    function ShowForm($error = "NO_ERROR")
    {
        // Don't actually show the form as we successfully connected - but still abstract it in to this "view" function.
        if ($error == "SUCCESS")
        {
            ?>
                <html>
                    <head>
                        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
                    </head>
                    <body>
                        <div class="alert alert-success">
                          <strong>Success!</strong> We Successfully Set Up TwitchWorks.
                        </div>
                    </body>
                </html>
            <?php
        }
        else
        {
            ?>
            <html>
                <head>
                    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
                </head>
                <body>
                    <?php
                    if ($error != "NO_ERROR")
                    {
                        ?>
                        <div class="alert alert-danger">
                          <strong>Error!</strong> <?php echo $error ?>
                        </div>
                        <?php
                    }
                    ?>
                    <form class="form-horizontal" action="setup.php" method="post">
                        <fieldset>

                        <!-- Form Name -->
                        <legend>Database Setup</legend>

                        <!-- Text input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="DatabaseHost">Host</label>
                          <div class="col-md-4">
                          <input id="DatabaseHost" name="DatabaseHost" type="text" placeholder="Database Host" class="form-control input-md" required="">

                          </div>
                        </div>


                        <!-- Text input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="DatabaseUsername">Username</label>
                          <div class="col-md-4">
                          <input id="DatabaseUsername" name="DatabaseUsername" type="text" placeholder="Username" class="form-control input-md" required="">

                          </div>
                        </div>

                        <!-- Password input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="DatabasePassword">Password</label>
                          <div class="col-md-4">
                            <input id="DatabasePassword" name="DatabasePassword" type="password" placeholder="Password" class="form-control input-md">

                          </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="DatabaseName">Database</label>
                          <div class="col-md-4">
                          <input id="DatabaseName" name="DatabaseName" type="text" placeholder="Database Name" class="form-control input-md" required="">

                          </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="TwitchClientID">Twitch Client ID</label>
                          <div class="col-md-4">
                          <input id="TwitchClientID" name="TwitchClientID" type="text" placeholder="Twitch Client ID (eg. axjhfp777tflhy0yjb5sftsil)" class="form-control input-md" required="">
                          </div>
                        </div>

                        <!-- Password input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="TwitchClientSecret">Twitch Client Secret</label>
                          <div class="col-md-4">
                          <input id="TwitchClientSecret" name="TwitchClientSecret" type="password" placeholder="Twitch Client Secret (eg. nyo51xcdrerl8z9m56w9w6wg)" class="form-control input-md" required="">
                          </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="TwitchRedirectURI">Twitch Redirect URI</label>
                          <div class="col-md-4">
                          <input id="TwitchRedirectURI" name="TwitchRedirectURI" type="text" placeholder="Twitch Redirect URI (eg. https://mygame.io/TwitchWorks/twitch_callback.php)" class="form-control input-md" required="">
                          </div>
                        </div>

                        <!-- Button -->
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="submit"></label>
                          <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">Save</button>
                          </div>
                        </div>

                        </fieldset>
                    </form>
                </body>
            </html>
            <?php
        }
    }
?>

<?php
    if($_SERVER['REQUEST_METHOD'] == 'GET')
    {
        ShowForm();
    }
    else
    {
        echo "submit";
        function write_ini_file($assoc_arr, $path, $has_sections = FALSE)
        {
        	$content = "";
        	if ($has_sections)
        	{
        		foreach($assoc_arr as $key => $elem)
        		{
        			$content.= "[" . $key . "]\n";
        			foreach($elem as $key2 => $elem2)
        			{
        				if (is_array($elem2))
        				{
        					for ($i = 0; $i < count($elem2); $i++)
        					{
        						$content.= $key2 . "[] = \"" . $elem2[$i] . "\"\n";
        					}
        				}
        				else
        				if ($elem2 == "") $content.= $key2 . " = \n";
        				else $content.= $key2 . " = \"" . $elem2 . "\"\n";
        			}
        		}
        	}
        	else
        	{
        		foreach($assoc_arr as $key => $elem)
        		{
        			if (is_array($elem))
        			{
        				for ($i = 0; $i < count($elem); $i++)
        				{
        					$content.= $key . "[] = \"" . $elem[$i] . "\"\n";
        				}
        			}
        			else
        			if ($elem == "") $content.= $key . " = \n";
        			else $content.= $key . " = \"" . $elem . "\"\n";
        		}
        	}

        	if (!$handle = fopen($path, 'w'))
        	{
        		return false;
        	}

        	$success = fwrite($handle, $content);
        	fclose($handle);
        	return $success;
        }


        if (!isset($_POST['DatabaseHost']) || !isset($_POST['DatabaseUsername']) || !isset($_POST['DatabasePassword']) || !isset($_POST['DatabaseName']) || !isset($_POST['TwitchClientID']) || !isset($_POST['TwitchClientSecret']) || !isset($_POST['TwitchRedirectURI']))
        {
            ShowForm("Please Fill Out All Of The Fields.");
            die();
        }

        $config = array(
                'database' => array(
                    'username' => $_POST['DatabaseUsername'],
                    'password' => $_POST['DatabasePassword'],
                    'dbname' => $_POST['DatabaseName'],
                    'tprefix' => "",
                    'dbhost' => $_POST['DatabaseHost']
                ),
                'twitch' => array(
                    'client_id' => $_POST['TwitchClientID'],
                    'client_secret' => $_POST['TwitchClientSecret'],
                    'twitch_redirect_uri' => $_POST['TwitchRedirectURI']
                ));

        $db = new mysqli($config['database']['dbhost'], $config['database']['username'], $config['database']['password']);

        if($db->connect_errno > 0){
            ShowForm("Unable To Connect To The Specified Database. Please Verify Your Credentials And/Or Try Again.  (ERROR: ".$db->connect_error.")");
            die();
        }

        if (!$db->query("CREATE DATABASE IF NOT EXISTS ".$config['database']['dbname'].";"))
		{
			echo("<br /><br />WARNING - Unable to create the ".$config['database']['dbname']." database. Does your account have the required permissions? (ERROR: ".$db->error.")<br /><br />");
			// Unable to create DB, but it might still exist, so continue for now
			//die();
		}

        if (!$db->select_db($config['database']['dbname']))
		{
			ShowForm("Unable to select/USE the ".$config['database']['dbname']." database. Does your account have the required permissions? (ERROR: ".$db->error.")");
            die();
		}

        $create_table_result = $db->query("CREATE TABLE IF NOT EXISTS TwitchUsers (
		    `ID` int NOT NULL AUTO_INCREMENT,
		    `Token` varchar(255) NOT NULL,
		    `Key` varchar(255) NOT NULL,
		    `Username` varchar(255),
		    `OAuthToken` varchar(255),
		    `RefreshToken` varchar(255),
		    PRIMARY KEY(`ID`),
		    UNIQUE (`Token`)
		);");

		if (!$create_table_result)
		{
			ShowForm("Unable to create the 'TwitchUsers' table in your ".$config['database']['dbname']." database. Does your account have the required permissions? (ERROR: ".$db->error.")");
            die();
		}

        write_ini_file($config, './private/config.ini', true);

        ShowForm("SUCCESS");

        echo("<h5>Configuration:</h5>");
        var_dump($config);
    }
?>
