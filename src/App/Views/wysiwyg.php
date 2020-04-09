<?php include_once('_header.php');?>

<h1>WYSIWYG Example:</h1>
<h2>When you submit the form, <br/>the filtered HTML will be outputted <br/>in a text box on the next page.</h2>

<?php $form->open('test')->method('POST')->action($dotz->url . '/filter/submit')->show();?>
	
	<div class="row">
		<?php $form->editor('text')
				->label('Add rich content below:')
				->text('You can enter any formatted text here.')
				->show();?>
	</div>
	<div class="row">
		<?php $form->button('submit')->value('Submit')->show();?>
	</div>

<?php $form->close()->show();?>

<h4><a href="<?php echo $dotz->url;?>/filter" class="item">Try filtering raw HTML here</a></h4>


<h4><a href="<?php echo $dotz->url;?>/filter/fileio" class="item">Have some good file input/output fun!</a></h4>

<?php include_once('_footer.php');?>