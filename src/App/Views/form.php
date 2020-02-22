<!DocType html />
<html>
	<head>
		<title>Form</title>
	</head>
	<body>
		<h1>Form:</h1>
		
		<?php echo $dotz->form->open('test','POST','/');?>

			<div>
				<?php echo $dotz->form->input('name', 'Name:');?>
			</div>
			<div>
				<?php echo $dotz->form->input('email', 'Email:');?>
			</div>
			<div>
				<?php echo $dotz->form->input(
					'citizen', 
					'Citizen?', 
					[
						'type'=>'checkbox'
					]
				);?>
				<?php echo $dotz->form->input(
					'relocate', 
					'Need to Relocate?', 
					[
						'type'=>'checkbox'
					]
				);?>
			</div>
			<div>Gender:</div>
			<div>
				<?php echo $dotz->form->input(
					'gender', 
					'Male:', 
					[
						'value'=>'male',
						'type'=>'radio'
					]
				);?>
				<?php echo $dotz->form->input(
					'gender', 
					'Female:', 
					[
						'value'=>'female',
						'type'=>'radio'
					]
				);?>
			</div>
			<div>
				<?php echo $dotz->form->select(
					'city', 
					[ 
						'oakville'=>'Oakville', 
						'brampton'=>'Brampton', 
						'milton'=>'Milton', 
						'burlington'=>'Burlington', 
						'mississauga'=>'Mississauga', 
						'toronto'=>'Toronto' 
					], 
					'City:',
					[
						'default' => 'toronto'
					]
				);?>
			</div>
			<div>
				<?php echo $dotz->form->textarea(
					'message', 
					'I beleive I can make a real impact in this role by...'
				);?>
			</div>
			<div>
				<?php echo $dotz->form->input(
					'submit', 
					null, 
					[
						'value'=>'Send',
						'type'=>'submit'
					]
				);?>
			</div>

		<?php echo $dotz->form->close();?>
	</body>
</html>
