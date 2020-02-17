<!DocType html />
<html>
	<head>
		<title>Form</title>
	</head>
	<body>
		<h1>Form:</h1>
		
		<?php echo $dotz->form->open(['attr'=>[
						'name'=>'test_form', 
						'id'=>'test', 
						'method'=>'POST', 
						'action'=>'index.php'
					]]);?>

			<div>
				<?php echo $dotz->form->input(['label'=>'Name',
					'attr'=>[
						'name'=>'name',
						'class'=>'nameField'
					]]);?>
			</div>
			<div>
				<?php echo $dotz->form->input(['label'=>'Email',
					'attr'=>[
						'name'=>'email',
						'class'=>'emailField'
					]]);?>
			</div>
			<div>
				<?php echo $dotz->form->input(['label'=>'Disabled',
					'attr'=>[
						'name'=>'disabled',
						'class'=>'needsField',
						'type'=>'checkbox',
						'value'=>'disabled'
					]]);?>
				<?php echo $dotz->form->input(['label'=>'Indeginous',
					'attr'=>[
						'name'=>'indeginous',
						'class'=>'needsField',
						'type'=>'checkbox',
						'value'=>'indeginous',
						'checked'=>'checked'
					]]);?>
				<?php echo $dotz->form->input(['label'=>'Minority',
					'attr'=>[
						'name'=>'minority',
						'class'=>'needsField',
						'type'=>'checkbox',
						'value'=>'minority'
					]]);?>
			</div>
			<div>
				<?php echo $dotz->form->input(['label'=>'Male',
					'attr'=>[
						'name'=>'gender',
						'class'=>'emailField',
						'type'=>'radio',
						'value'=>'male',
						'checked'=>'checked'
					]]);?>
				<?php echo $dotz->form->input(['label'=>'Female',
					'attr'=>[
						'name'=>'gender',
						'class'=>'emailField',
						'type'=>'radio',
						'value'=>'female'
					]]);?>
			</div>
			<div>
				<?php echo $dotz->form->select(['label'=>'City',
					'attr'=>[
						'name'=>'city',
						'class'=>'cityField'
					],
					'options'=>[
						'mississauga'=>[
							'attr'=>[ 'value'=>'Mississauga' ],
							'displayText'=>'Mississauga'
						],
						'toronto'=>[
							'attr'=>[ 'value'=>'Toronto' ],
							'displayText'=>'Toronto'
						]
					]]);?>
			</div>
			<div>
				<?php echo $dotz->form->textarea(['label'=>'Message',
					'text'=>'Hello world',
					'attr' => [
						'name'=>'message',
						'id'=>'messageField',
						'rows'=>'4',
						'col'=>'3'
					]], false);?>
			</div>
			<div>
				<?php echo $dotz->form->input(['attr'=>[
						'name'=>'submit',
						'class'=>'submitBtn',
						'type'=>'submit',
						'value'=>'Submit'
					]], false);?>
			</div>

		<?php echo $dotz->form->close();?>
	</body>
</html>
