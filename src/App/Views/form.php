<!DocType html />
<html>
	<head>
		<title>[Dotz Framework] Form Example</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $dotz->viewsUrl;?>/assets/css/styles.css">
	</head>
	<body>
		<div class="page">
			<h1>Form:</h1>
			
			<?php $app->form->open('test')->method('POST')->action($dotz->url . '/submit')->show();?>

				<div>
					<?php $app->form->textfield('name')->label('Name:')->show();?>
				</div>
				<div>
					<?php $app->form->textfield('email')->label('Email:')->show();?>
				</div>
				<div>
					<?php $app->form->checkbox('citizen')->label('Citizen?')->show();?>
					<?php $app->form->checkbox('relocate')->label('Need to Relocate?')->show();?>
				</div>
				<div>Gender:</div>
				<div>
					<?php $app->form->radiobutton('gender')->label('Male:')->value('male')->show();?>
					<?php $app->form->radiobutton('gender')->label('Female:')->value('female')->show();?>
				</div>
				<div>
					<?php $app->form->select('city')->label('City:')->options($app->data['cities'])->default('toronto')->show();?>
				</div>
				<div>
					<?php $app->form->select('test')
						->label('Test:')
						->option('test2', 'Test Two')
						->option('test', 'Test')
						->default('test2')
						->show();?>
				</div>
				<div>
					<?php $app->form->textarea('message')->label('Personal Comments:')->show();?>
				</div>
				<div>
					<?php $app->form->button('submit')->value('Send')->show();?>
				</div>

			<?php $app->form->close()->show();?>


			<div class="menu">
				<a href="<?php echo $dotz->url;?>" class="item">Home</a>
				<a href="<?php echo $dotz->url;?>/pages/form" class="item">Form Example</a>
				<a href="<?php echo $dotz->url;?>/pages/queries" class="item">MySQL Query example</a>
				<a href="<?php echo $dotz->url;?>/get?index=<script>var t='hello'; document.write(t);</script>" class="item">Get Filtering Example</a>
			</div>
		</div>
	</body>
</html>
