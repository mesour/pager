<?php

namespace Mesour\PagerTests;

use Mesour\DropDown\RandomString\CapturingRandomStringGenerator;
use Mesour\DropDown\RandomString\IRandomStringGenerator;
use Mesour\SelectionTests\MockRandomStrings\DefaultTestRandomString;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';
require_once __DIR__ . '/BaseTestCase.php';

class AdvancedTest extends BaseTestCase
{

	public function testDefault()
	{
		$application = new \Mesour\UI\Application();

		$application->setRequest($_REQUEST);

		$application->run();

		// pager

		$advancedPager = new \Mesour\UI\AdvancedPager('advanced_pager');

		$application->addComponent($advancedPager);

		$advancedPager->getPaginator()->setItemsPerPage(5);

		$advancedPager->setCount(105);

		Assert::same(
			file_get_contents(__DIR__ . '/data/AdvancedTestOutput.html'),
			(string) $advancedPager->render(),
			'Output of advanced pager render doest not match'
		);
	}

}

$test = new AdvancedTest();
$test->run();
