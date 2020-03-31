<?php include_once('_header.php');?>

<style type="text/css">
	.testForm .textTextField{
		width: 550px;
		height: 600px;
	}
</style>

<h1>FilterText Example:</h1>
<h3>FilterText is the WYSIWYG text filter we have created. It should take out malicious code from any input you supply it; leaving behind only safe code/text. Feel free to test some scenarios below.</h3>
<p></p>

<?php $form->open('test')
			->method('POST')->action($dotz->url . '/filter/submit')
			->show();?>

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