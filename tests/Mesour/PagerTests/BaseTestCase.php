<?php

namespace Mesour\PagerTests;

use Tester\TestCase;

class BaseTestCase extends TestCase
{

	protected function createApplication($handler = null, array $request = [])
	{
		$application = new \Mesour\UI\Application('mesourApp');

		if ($handler) {
			list($componentName, $handlerName) = explode('-', $handler);

			$newRequest = [];
			foreach ($request as $key => $value) {
				$newRequest['m_mesourApp-' . $componentName . '-' . $key] = $value;
			}
			$request = $newRequest;

			$request['m_do'] = 'mesourApp-' . $handler;
		}

		$application->setRequest($request);
		$application->run();

		return $application;
	}

}
