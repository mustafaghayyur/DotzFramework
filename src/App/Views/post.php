<?php include_once('_header.php');?>

<h1>POST variable filtering example</h1>
<h3>Message Contents:</h3>
<h4>Orginal message:</h4>
<div>
	<?php echo  $unfiltered;?>
</div>
<h4>Sanitized message:</h4>
<div>
	<?php echo  $sanitized;?>
</div>
<h4>Validated message:</h4>
<div>
	<?php echo  $validated;?>
</div>
	
<?php include_once('_footer.php');?>
