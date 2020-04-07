<?php include_once('_header.php');?>

<h1>Login:</h1>

<?php if(!empty($message)) echo "<h3>Error: {$message}</h3>"; ?>

<?php $form->open('login')->method('POST')->action($dotz->url . '/user/login')->show();?>
	
	<div class="row">
		<?php $form->textfield('username')->label('User Name:')->show();?>
	</div>
	<div class="row">
		<?php $form->password('password')->label('Password:')->show();?>
	</div>

	<div class="row">
		<?php $form->button('submit')->value('Login')->show();?>
	</div>

<?php $form->close()->show();?>

<?php include_once('_footer.php');?>