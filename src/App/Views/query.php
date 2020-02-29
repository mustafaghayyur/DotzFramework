<!DocType html />
<html>
	<head>
		<title>[Dotz Framework] Query Example</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $dotz->viewsUrl;?>/assets/css/styles.css">
	</head>
	<body>
		<div class="page">
			<h1>Example select query output:</h1>
			<p><?php echo $app['title'];?></p>

			
			<div class="menu">
				<a href="<?php echo $dotz->url;?>" class="item">Home</a>
				<a href="<?php echo $dotz->url;?>/pages/form" class="item">Form Example</a>
				<a href="<?php echo $dotz->url;?>/pages/queryone" class="item">MySQL Query example</a>
			</div>
		</div>
	</body>
</html>
