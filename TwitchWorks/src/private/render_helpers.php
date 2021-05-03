<?php
/*
 * Copyright 2017-2021 HowToCompute. All Rights Reserved
 */

// Helper function for rendering an error message.
function RenderFailure($error_message)
{
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.0.2/tailwind.min.css" rel="stylesheet">
  <title>Twitch Login Failure!</title>
</head>
<body class="bg-gradient-to-r from-red-400 to-purple-500">
	<div class="flex h-screen">
	  <div class="m-auto bg-gray-100 rounded-xl shadow-md flex flex-col p-6 max-w-md mx-auto">
		  <h1 class="flex-auto text-5xl mb-5 font-semibold">
	        Error!
	      </h1>

		  <p class="flex-auto mb-5">We were unfortunately unable to log you in to Twitch.</p>

		  <p class="flex-auto">Please consult the error message below for more details:</p>
		  <p class="flex-auto italic text-gray-600 font-mono text-sm"><?php echo($error_message); ?></p>
		</div>
	  </div>
</body>
</html>
<?php
}

	// Helper function for rendering a login success message with instructions to switch back to the game.
	function RenderSuccess($username)
	{
?>
	<!DOCTYPE html>
	<html>
	<head>
	  <meta charset="UTF-8" />
	  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	  <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.0.2/tailwind.min.css" rel="stylesheet">
	  <title>Successfully logged in to Twitch!</title>
	</head>
	<body class="bg-gradient-to-r from-green-400 to-purple-500">
		<div class="flex h-screen">
		  <div class="m-auto bg-gray-100 rounded-xl shadow-md flex flex-col p-6 max-w-md mx-auto">
			  <h1 class="flex-auto text-5xl mb-5 font-semibold">
		        Success!
		      </h1>

			  <p class="flex-auto">You successfully logged in to Twitch, <span class="font-bold"><?php echo($username); ?></span>!</p>
			  <p class="flex-auto mb-5">You may now switch back to the game and start playing!</p>

			  <p class="flex-auto italic text-gray-600 font-mono text-sm">NOTE: The game may take a few seconds to pick up the login and may require you to <span class="font-semibold">focus</span> on the game's window by clicking on it. Please be patient and retry if you are still facing the loading screen after more than 15 seconds.</p>
			</div>
		  </div>
	</body>
	</html>
	<?php
}
 ?>
