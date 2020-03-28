<?php include_once('_header.php');?>

<h1>Filtering GET variables</h1>

<h4>Orginal index value:</h4>
<div>
	<?php echo  $unfiltered;?>
</div>
<h4>Filtered index value:</h4>
<div>
	<?php echo  $filtered;?>
</div>

<h4><a href="<?php echo $dotz->url;?>/get/secure?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3RcL1dEUm91dGVyTGlicmFyeSIsImlhdCI6MTU4Mzg3MTg5MiwiZXhwIjoxNTgzODcyNDkyfQ.cENuJPoh1VA-qCtdqK58OlwuYFqN3g1YAbIqUSfDoMc&index=<script>var t='hello'; document.write(t);</script>" class="item">Secure Get Filtering Example (valid JWT required)</a></h4>
<?php include_once('_footer.php');?>
