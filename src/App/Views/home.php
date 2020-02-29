<!DocType html />
<html>
	<head>
		<title>[Dotz Framework] Welcome</title>		
		<link rel="stylesheet" type="text/css" href="<?php echo $dotz->viewsUrl;?>/assets/css/styles.css">
	</head>
	<body>
		<div class="page">
			<h1>Welcome to the Dotz Framework</h1>
			<p>Hello <?php echo $app['name'];?>.</p>

			<div class="menu">
				<a href="<?php echo $dotz->url;?>" class="item">Home</a>
				<a href="<?php echo $dotz->url;?>/pages/form" class="item">Form Example</a>
				<a href="<?php echo $dotz->url;?>/pages/queryone" class="item">MySQL Query example</a>
			</div>
		</div>
	</body>
</html>
