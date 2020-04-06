<?php include_once('_header.php');?>

<h1>Form:</h1>

<?php if(!empty($message)) echo "<h3>Error: {$message}</h3>"; ?>

<?php $form->open('login')->method('POST')->action($dotz->url . '/user/signup')->show();?>
	
	
	<div class="row">
		<?php $form->textfield('email')->label('Email:')->show();?>
	</div>
	<div class="row">
		<?php $form->textfield('username')->label('User Name:')->show();?>
	</div>
	<div class="row">
		<?php $form->textfield('password')->label('Password:')->show();?>
	</div>
	
	<div class="row">
		<?php $form->checkbox('remember')->label('Remember me')->checked()->show();?>
	</div>

	<div class="row">
		<?php $form->button('submit')->value('Signup')->show();?>
	</div>

<?php $form->close()->show();?>

<?php include_once('_footer.php');?>