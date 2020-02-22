<!DocType html />
<html>
	<head>
		<title>Form</title>
	</head>
	<body>
		<h1>Form:</h1>
		
		<?php $dotz->form->open('test','POST','/');?>

			<div>
				<?php $dotz->form->textfield('name', 'Name:');?>
			</div>
			<div>
				<?php $dotz->form->textfield('email', 'Email:');?>
			</div>
			<div>
				<?php $dotz->form->checkbox('citizen', 'Citizen?');?>
				<?php $dotz->form->checkbox('relocate', 'Need to Relocate?');?>
			</div>
			<div>Gender:</div>
			<div>
				<?php $dotz->form->radiobutton('gender', 'male', 'Male:');?>
				<?php $dotz->form->radiobutton('gender', 'female', 'Female:');?>
			</div>
			<div>
				<?php $dotz->form->select('city', $dotz->data['cities'], 'City:', 'toronto');?>
			</div>
			<div>
				<?php $dotz->form->textarea('message', 'Personal Comments:');?>
			</div>
			<div>
				<?php $dotz->form->button('submit', 'Send');?>
			</div>

		<?php $dotz->form->close();?>
	</body>
</html>
