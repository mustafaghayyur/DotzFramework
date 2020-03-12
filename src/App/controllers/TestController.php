<?php 

use DotzFramework\Core\Controller;
use DotzFramework\Modules\Form\FilterText;

class TestController extends Controller{

	/**
	 * Home page
	 */
	public function index($test=''){

		$f = new FilterText();

		$text = <<<EOD
<!DocType html />
<html>
	<head>
		<title>[Dotz Framework] Get Example</title>
		<link rel="stylesheet" type="text/css" href="http://localhost/WDRouterLibrary/src/App/Views/assets/css/styles.css">

		
<script type="text/javascript">
var dotz = {"------------":"Full URL including HTTP protocol.-------","name":"Web Dotz","url":"http:\/\/localhost\/WDRouterLibrary"}
</script>
	</head>
	<body>
		<div class="page">
			<a href="http://www.web-dotz.com" target="_blank" class="logo"></a>
			<div class="content">


<h1>Example select queries output:</h1>
<p>If you are seeing this sentence in the browser; your database and doctrine are setup correctly!</p>
<p>This is the second query.</p>
<p>This is the third query.</p>
	

			</div>
			<div class="menu">
				<a href="http://localhost/WDRouterLibrary" class="item">Home</a>
				<a href="http://localhost/WDRouterLibrary/pages/form" class="item">Form Example</a>
				<a href="http://localhost/WDRouterLibrary/pages/queries" class="item">MySQL Query example</a>
				<a href="http://localhost/WDRouterLibrary/get/secure?jwt=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3RcL1dEUm91dGVyTGlicmFyeSIsImlhdCI6MTU4Mzg3MTg5MiwiZXhwIjoxNTgzODcyNDkyfQ.cENuJPoh1VA-qCtdqK58OlwuYFqN3g1YAbIqUSfDoMc&index=<script>var t='hello'; document.write(t);</script>" class="item">Get Filtering Example (JWT)</a>
				<a href="http://localhost/WDRouterLibrary/get?index=<script>var t='hello'; document.write(t);</script>" class="item">Get Filtering Example</a>
			</div>
		</div>
	</body>
</html>
<div class="profiler">Time: 0.078s<br/>Memory: 828.584kb</div>
EOD;

		$f->process($text);

		//$packet = [ 'msg' => 'Developed by Web Dotz' ];
		//$this->view->load('home', $packet);
	}

	

        
}