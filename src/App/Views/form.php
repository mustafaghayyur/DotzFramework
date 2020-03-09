<?php include_once('_header.php');?>

<h1>Form:</h1>

<?php $form->open('test')->method('POST')->action($dotz->url . '/submit')->show();?>
	
	<?php $form->hidden('jwt')->value($jwt)->show();?>

	<div>
		<?php $form->textfield('name')->label('Name:')->show();?>
	</div>
	<div>
		<?php $form->textfield('email')->label('Email:')->show();?>
	</div>
	<div>
		<?php $form->checkbox('citizen')->label('Citizen?')->show();?>
		<?php $form->checkbox('relocate')->label('Need to Relocate?')->show();?>
	</div>
	<div>Gender:</div>
	<div>
		<?php $form->radiobutton('gender')->label('Male:')->value('male')->show();?>
		<?php $form->radiobutton('gender')->label('Female:')->value('female')->show();?>
	</div>
	<div>
		<?php $form->select('city')
			->label('City:')
			->options($data['cities'])
			->default('toronto')
			->show();?>
	</div>
	<div>
		<?php $form->select('prov')
			->label('Province:')
			->option('ON', 'ON')
			->option('QC', 'QC')
			->default('QC')
			->show();?>
	</div>
	<div>
		<?php $form->textarea('message')->label('Personal Comments:')->show();?>
	</div>
	<div>
		<?php $form->button('submit')->value('Send')->show();?>
	</div>

<?php $form->close()->show();?>

<?php include_once('_footer.php');?>