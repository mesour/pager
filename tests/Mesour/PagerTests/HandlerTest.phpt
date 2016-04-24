<?php

namespace Mesour\PagerTests;

use Mesour\DropDown\RandomString\CapturingRandomStringGenerator;
use Mesour\DropDown\RandomString\IRandomStringGenerator;
use Mesour\SelectionTests\MockRandomStrings\DefaultTestRandomString;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/BaseTestCase.php';

class HandlerTest extends BaseTestCase
{

	public function testDefault()
	{
		$application = $this->createApplication('pager-setPage', [
			'page' => 3,
		]);

		$pager = new \Mesour\UI\Pager('pager');

		$application->addComponent($pager);

		$pager->getPaginator()->setItemsPerPage(10);

		$pager->setCount(55);

		Assert::same(
			file_get_contents(__DIR__ . '/data/HandlerTestOutput.html'),
			(string) $pager->render(),
			'Output of pager render doest not match'
		);
	}

}

$test = new HandlerTest();
$test->run();
