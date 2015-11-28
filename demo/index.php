<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="../docs/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="../docs/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="../docs/js/jquery.min.js"></script>
<script src="../docs/js/bootstrap.min.js"></script>
<script src="../vendor/mesour/components/public/mesour.components.js"></script>
<script src="../docs/js/main.js"></script>

<style>
    .select-checkbox,
    .main-checkbox{
        height: 22px;
        width: 25px;
    }
</style>

<?php

define('SRC_DIR', __DIR__ . '/../src/');

require_once __DIR__ . '/../vendor/autoload.php';

@mkdir(__DIR__ . '/log');

\Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT, __DIR__ . '/log');

require_once SRC_DIR . 'Mesour/Pager/Paginator.php';
require_once SRC_DIR . 'Mesour/UI/IPager.php';
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

    $pager = new \Mesour\UI\Pager('pager');

    $application->addComponent($pager);

    $pager->getPaginator()->setItemsPerPage(10);

    $pager->setCount(55);

    $pager->render();

    ?>
</div>

<hr>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>