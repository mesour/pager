<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
	  integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

<?php

define('SRC_DIR', __DIR__ . '/../src/');

require_once __DIR__ . '/../vendor/autoload.php';

@mkdir(__DIR__ . '/log');

\Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT, __DIR__ . '/log');

require_once SRC_DIR . 'Mesour/Pager/Paginator.php';
require_once SRC_DIR . 'Mesour/Pager/IPager.php';
require_once SRC_DIR . 'Mesour/UI/AdvancedPager.php';
require_once SRC_DIR . 'Mesour/UI/Pager.php';

?>

<hr>

<div class="container">
	<h2>Basic functionality</h2>

	<hr>

	<?php

	$application = new \Mesour\UI\Application();

	$application->setRequest($_REQUEST);

	$application->run();

	// pager

	$pager = new \Mesour\UI\Pager('pager', $application);

	$pager->getPaginator()->setItemsPerPage(10);

	$pager->setCount(55);

	$pagerHtml = $pager->create();

	// advanced

	$advanced = new \Mesour\UI\AdvancedPager('advanced_pager', $application);

	$advanced->getPaginator()->setItemsPerPage(5);

	$advanced->setCount(105);

	$advancedHtml = $advanced->create();

	// rendering

	echo $pagerHtml;

	echo '<hr>';

	echo $advancedHtml;

	?>
</div>

<hr>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
		integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
		crossorigin="anonymous"></script>

<script src="../node_modules/mesour-pager/dist/mesour.pager.min.js"></script>