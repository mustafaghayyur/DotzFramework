<!DocType html />
<html>
	<head>
		<title>Form</title>
	</head>
	<body>
		<h1>Form:</h1>
		
		<?php $dotz->form->open('test','POST','/');?>

			<div>
				<?php $dotz->form->textfield('Name:', 'name');?>
			</div>
			<div>
				<?php $dotz->form->textfield('Email:', 'email');?>
			</div>
			<div>
				<?php $dotz->form->checkbox('Citizen?', 'citizen');?>
				<?php $dotz->form->checkbox('Need to Relocate?', 'relocate');?>
			</div>
			<div>Gender:</div>
			<div>
				<?php //$dotz->form->radiobutton('Male:', 'gender', 'male');?>
				<?php $dotz->form->radiobutton('gender')->value('male')->label('Male:')->show();?>
				<?php //$dotz->form->radiobutton('Female:', 'gender', 'female');?>
			</div>
			<div>
				<?php //$dotz->form->select('City:', 'city', $dotz->data['cities'], 'toronto');?>
				<?php $dotz->form->select('city')->label('City:')->options($dotz->data['cities'])->default('toronto')->show();?>
			</div>
			<div>
				<?php $dotz->form->textarea('Personal Comments:', 'message');?>
			</div>
			<div>
				<?php $dotz->form->button('submit', 'Send');?>
			</div>

		<?php $dotz->form->close();?>
	</body>
</html>
