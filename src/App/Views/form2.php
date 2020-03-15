<?php include_once('_header.php');?>

<h1>Form:</h1>

<?php $form->open('test')->method('POST')->action($dotz->url . '/test/submit')->show();?>
	<div class="row">
		<?php $form->textarea('message')
			->label('Personal Comments:')
			->show();?>
	</div>
	<div class="row">
		<?php $form->button('submit')->value('Send')->show();?>
	</div>

<?php $form->close()->show();?>

<?php include_once('_footer.php');?>