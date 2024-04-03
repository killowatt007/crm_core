<?php 
	// phpinfo();
	$dateKey = date('YmdHis');

	if (isset($_GET['_test_']))
	{
		include $_SERVER['DOCUMENT_ROOT'] .'/test.php';
		exit;
	}

	if (isset($_GET['info']))
	{
		include $_SERVER['DOCUMENT_ROOT'] .'/info.php';
		exit;
	}

	$app = ($_SERVER['REMOTE_ADDR'] == '91.237.182.241') ? '_app' : 'app';
	$app = 'app';
?>
<!doctype html>
<html>
	<head>
    <title>Burnsoft</title>
    <meta name="description" content="Burnsoft">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <meta name="theme-color" content="#948b6e">
    <link rel="icon" type="image/png" href="/pwa/images/icons/icon-128x128.png" />
    <link rel="apple-touch-icon" href="/pwa/images/icons/icon-192x192.png">
    <meta name="apple-mobile-web-app-status-bar" content="#948b6e">
    <link rel="manifest" href="/pwa/manifest.json">


		<link href="/client/style/bootstrap/bootstrap.css?<?php echo $dateKey;?>" rel="stylesheet" type="text/css">

		<link href="/client/style/fontawesome-pro/css/all.css?<?php echo $dateKey;?>" rel="stylesheet" type="text/css">
		<link href="/client/style/fontawesome-pro/css/font-awesome.min.css?<?php echo $dateKey;?>" rel="stylesheet" type="text/css">
		
		<link href="/client/style/select2/select2.css?<?php echo $dateKey;?>" rel="stylesheet" type="text/css">
		<script src="/client/libraries/jquery.js?<?php echo $dateKey;?>" type="text/javascript"></script>

		<script src="/client/libraries/mootools.js?<?php echo $dateKey;?>" type="text/javascript"></script>
		<script src="/client/libraries/require.js?<?php echo $dateKey;?>" type="text/javascript"></script>

		<link href="/client/style/jquery/jquery-ui.min.css?<?php echo $dateKey;?>" rel="stylesheet" type="text/css">
		<link href="/client/style/daterangepicker.css?<?php echo $dateKey;?>" rel="stylesheet" type="text/css">

		<link href="/client/style/bs/temp.css?<?php echo $dateKey;?>" rel="stylesheet" type="text/css">

		<script src="https://api-maps.yandex.ru/2.1/?apikey=2b0c1d4f-8671-49f6-83c3-970240a253e8&lang=ru_RU" type="text/javascript"></script>
	</head>
	<body>
		<div id="app"></div>
		<script>
			var App,
					get
					$ = jQuery; $

			(function () 
			{
				let pathRoot = "client"

				window.onerror = function(fullMsg, url, line, column, stack) 
				{
					if (!window.isfirsterror)
					{
						App.syntaxClientError(fullMsg, url, line)
						window.isfirsterror = true
					}
				}

				require.config(
				{
					"baseUrl"					: "/",
					"paths": {
						"entity"				: pathRoot+"/libraries/bs/entity",
						"mmodule"				: pathRoot+"/libraries/bs/module",
						"view"					: pathRoot+"/libraries/bs/view",
						"plugin"				: pathRoot+"/libraries/bs/event/plugin",

						"modules"				: pathRoot+"/modules",
						"components"		: pathRoot+"/components",
						"plugins"				: pathRoot+"/plugins",
						"spaces"				: pathRoot+"/spaces",
						"lib"						: pathRoot+"/libraries",

						"ace"						: pathRoot+"/libraries/ace",

						"jquery"				: pathRoot+"/libraries/jquery",
						'moment'				: pathRoot+"/libraries/moment.min"
					},
					"waitSeconds"	: 30,
					"urlArgs"			: "bust=" + (new Date()).getTime()
				})

				require(["lib/bs/<?php echo $app;?>"], function(_app) 
				{
					get = function(variable, def, key) 
					{ 
						if (key)
						{
							if (typeof key == 'string')
								key = key.split('.')

							$.each(key, function(i, key)
							{
				      	if (variable !== undefined)
					      	variable = (variable[key]!== undefined) ? variable[key] : undefined
							})
						}

						return variable !== undefined ? variable : (def !== undefined ? def : null) 
					}

					getfu = function(val)
					{
						if (typeof val === 'boolean')
							val = val ? 1 : 0
						else if (val === null || val === undefined)
							val = 0

						return val
					}

					log = function(data)
					{
						console.log(data)
					}

					App = new _app()
					App.run()
				})
			}())
		</script>
		<script src="/pwa/app.js"></script>
	</body>
</html>