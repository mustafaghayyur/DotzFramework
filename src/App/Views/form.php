<?php include_once('_header.php');?>

<h1>Form:</h1>

<?php $form->open('test')->method('POST')->action($dotz->url . '/submit')->show();?>
	
	<div class="row">
		<?php $form->textfield('name')->label('Name:')->show();?>
	</div>
	<div class="row">
		<?php $form->textfield('email')->label('Email:')->show();?>
	</div>
	
	<div class="row">
		<?php $form->checkbox('citizen')->label('Citizen?')->show();?>
	</div>
	<div class="row">
		<?php $form->checkbox('relocate')->label('Need to Relocate?')->checked()->show();?>
	</div>

	<div class="row">Gender:</div>
	<div class="row">
		<?php $form->radiobutton('gender')->label('Male:')->value('male')->checked()->show();?>
	</div>
	<div class="row">
		<?php $form->radiobutton('gender')->label('Female:')->value('female')->show();?>
	</div>

	<div class="row">
		<?php $form->select('city')
			->label('City:')
			->options($cities)
			->default('toronto')
			->show();?>
	</div>
	<div class="row">
		<?php $form->select('prov')
			->label('Province:')
			->option('ON', 'ON')
			->option('QC', 'QC')
			->default('QC')
			->show();?>
	</div>

	<div class="row">
		<?php $form->textarea('message')->label('Personal Comments:')->show();?>
	</div>
	
	<div class="row">
		<?php $form->button('submit')->value('Send')->show();?>
	</div>

<?php $form->close()->show();?>

<?php include_once('_footer.php');?>