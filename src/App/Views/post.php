<!DocType html />
<html>
	<head>
		<title>[Dotz Framework] Post Example</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $dotz->viewsUrl;?>/assets/css/styles.css">
	</head>
	<body>
		<div class="page">
			<h1>POST variable filtering example</h1>
			<h3>Message Contents:</h3>
			<h4>Orginal message:</h4>
			<div>
				<?php echo  $app['original'];?>
			</div>
			<h4>Filtered message:</h4>
			<div>
				<?php echo  $app['filtered'];?>
			</div>

			<div class="menu">
				<a href="<?php echo $dotz->url;?>" class="item">Home</a>
				<a href="<?php echo $dotz->url;?>/pages/form" class="item">Form Example</a>
				<a href="<?php echo $dotz->url;?>/pages/queries" class="item">MySQL Query example</a>
				<a href="<?php echo $dotz->url;?>/get?index=<script>var t='hello'; document.write(t);</script>" class="item">Get Filtering Example</a>
			</div>
		</div>
	</body>
</html>
