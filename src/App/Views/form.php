<!DocType html />
<html>
	<head>
		<title>Form</title>
	</head>
	<body>
		<h1>Form:</h1>
		
		<?php //$dotz->form->open('test','POST','/');?>
		<?php $dotz->form->open('test')->method('POST')->action('/')->show();?>

			<div>
				<?php $dotz->form->textfield('name')->label('Name:')->show();?>
			</div>
			<div>
				<?php $dotz->form->textfield('email')->label('Email:')->show();?>
			</div>
			<div>
				<?php $dotz->form->checkbox('citizen')->label('Citizen?')->show();?>
				<?php $dotz->form->checkbox('relocate')->label('Need to Relocate?')->show();?>
			</div>
			<div>Gender:</div>
			<div>
				<?php $dotz->form->radiobutton('gender')->label('Male:')->value('male')->show();?>
				<?php $dotz->form->radiobutton('gender')->label('Female:')->value('female')->show();?>
			</div>
			<div>
				<?php $dotz->form->select('city')->label('City:')->options($dotz->data['cities'])->default('toronto')->show();?>
			</div>
			<div>
				<?php $dotz->form->textarea('message')->label('Personal Comments:')->show();?>
			</div>
			<div>
				<?php $dotz->form->button('submit')->value('Send')->show();?>
			</div>

		<?php $dotz->form->close()->show();?>
	</body>
</html>
