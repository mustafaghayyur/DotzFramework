<?php include_once('_header.php');?>

<h1>WYSIWYG Example:</h1>

<?php $form->open('test')->method('POST')->action($dotz->url . '/submit')->show();?>
	
	<div class="row">
		<?php $form->wysiwyg('wysiwyg')->label('Personal Comments:')->show();?>
	</div>
	<div class="row">
		<?php $form->button('submit')->value('Submit')->show();?>
	</div>

<?php $form->close()->show();?>

<?php include_once('_footer.php');?>