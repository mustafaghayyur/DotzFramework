<?php include_once('_header.php');?>

<style type="text/css">
	.testForm .textTextField{
		width: 550px;
		height: 600px;
	}
</style>

<h1>FilterText Example:</h1>

<?php $form->open('test')
			->method('POST')->action($dotz->url . '/filter/submit')
			->show();?>

<div class="wysiwyg">
	<div id="b">b</div>
</div>
<div class="row">
	<?php $form->textarea('text')
		->label('Enter text in this textbox, and see the filtered result:')
		->text($text)
		->show();?>
</div>

<div class="row">
	<?php $form->button('submit')->value('Filter')->show();?>
</div>

<?php $form->close()->show();?>

<?php include_once('_footer.php');?>