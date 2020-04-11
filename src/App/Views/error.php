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

			.trace{
				display: block;
				margin: 10px 5% 10px;
				padding: 10px 10px 20px;
				font-size: 14px;
				line-height: 100%;
			}

			.notices{
				padding: 20px;
				font-size: 18px;
				margin: 20px;
				border-top: 1px solid rgba(247, 210, 22,1);
				background-color: #666;
				color: #fff;
				font-family: 'Arial';
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
				
				<?php
				/**
				 * Don't remove this snippet of code.
				 */
				if(isset($html)){
					echo $html; die();
				}
				?>

				<h1>Error Occurred:</h1>
				
				<?php
				if(isset($updateError)){?>
					<p><strong>Update Error:</strong> <?php echo $updateError;?></p>
				<?php
				}
				
				if(isset($msg)){?>
					<p><strong>Message:</strong> <?php echo $msg;?></p>
				<?php 
				}

				if(isset($file)){?>
					<p><strong>In file:</strong> <?php echo $file;?></p>
				<?php
				}

				if(isset($line)){?>
					<p><strong>On line #:</strong> <?php echo $line;?></p>
				<?php
				}

				if(isset($trace)){?>
					<p><strong>Trace:</strong> below trace may be helpful...</p> 
					<div class="trace"><?php echo $trace;?></div>
				<?php
				}?>

				<?php
				if(isset($data)){?>
					<p><strong>Passed Data:</strong> <?php echo $data; }?></p>
			</div>
			<div class="menu">
				<a href="<?php echo $dotz->url;?>" class="item">Home</a>
			</div>
		</div>		
	</body>
</html>
