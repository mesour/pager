<?php

namespace Mesour\PagerTests;

use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/BaseTestCase.php';

class DefaultTest extends BaseTestCase
{

	public function testDefault()
	{
		$application = new \Mesour\UI\Application();

		$application->setRequest($_REQUEST);

		$application->run();

		// pager

		$pager = new \Mesour\UI\Pager('pager', $application);

		$pager->getPaginator()->setItemsPerPage(10);

		$pager->setCount(55);

		Assert::same(
			file_get_contents(__DIR__ . '/data/DefaultTestOutput.html'),
			(string) $pager->render(),
			'Output of pager render doest not match'
		);
	}

}

$test = new DefaultTest();
$test->run();
