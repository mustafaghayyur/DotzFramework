<!DocType html />
<html>
	<head>
		<title><?php echo $dotz->appName; ?> - ERROR</title>
		<style type="text/css">
			
			html,
			body{
				background-color: #999;
				width: 100%;
				height: 100%;
				padding: 0;
				margin: 0;
			}

			.page{
				display: block;
				width: 90%;
				margin: 20px auto;
				padding: 25px;
				background-color: #fff;
				border: #666 solid 1px;
			}

			.content{
				color: #333;
				font-family: 'Arial';
				font-size: 25px;
				line-height: 130%;
			}

			h1, h2, h3, h4, h5, h6 {
				font-family: 'Arial';
				line-height: 130%;
				margin: 0 5% 32px;
				font-weight: 600;
			}

			p{
				margin: 10px 5% 10px;
				padding: 10px 10px 20px;
				border-bottom: 1px dashed #666;
			}

			.menu {
				display: block;
				margin: 55px 5% 0px 5%;
				background-color: #999;
				padding: 7px;
			}

			.menu .item {
				color: #fff;
				font-weight: bold;
				margin: 0 10px 0;
				padding: 5px;
				display: inline-block;
				text-decoration: none;
			}

		</style>
	</head>
	<body>
		<div class="page">

			<div class="content">

				<h1>Error Occurred:</h1>
				
				<?php
				if(isset($updateError)){?>
					<p><strong>Update Error:</strong> <?php echo $updateError;?></p>
				<?php
				}
				
				if(isset($msg)){?>
					<p><strong>Message:</strong> <?php echo $msg;?>.</p>
					<p><strong>In file:</strong> <?php echo $file;?>.</p>
					<p><strong>On line #:</strong> <?php echo $line;?>.</p>
				<?php
				}?>

				

			</div>
			<div class="menu">
				<a href="<?php echo $dotz->url;?>" class="item">Home</a>
			</div>
		</div>		
	</body>
</html>
