<!DocType html />
<html>
	<head>
		<title>[Dotz Framework] Form Example</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $dotz->url;?>/assets/css/styles.css">
	</head>
	<body>
		<h1>Form:</h1>
		
		<?php //$app->form->open('test','POST','/');?>
		<?php $app->form->open('test')->method('POST')->action('/')->show();?>

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
					->option('test', 'Test')
					->option('test2', 'Test Two')
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
	</body>
</html>
