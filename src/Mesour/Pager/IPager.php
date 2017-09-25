<?php
/**
 * This file is part of the Mesour Pager (http://components.mesour.com/component/pager)
 *
 * Copyright (c) 2017 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\Pager;

use Mesour;

/**
 * @author Matouš Němec (http://mesour.com)
 */
interface IPager extends Mesour\Components\Control\IAttributesControl
{

	public function setCount($count);

	/**
	 * @return Paginator
	 */
	public function getPaginator();

	public function reset();

	/**
	 * @param bool|FALSE $navOnly
	 * @return mixed
	 * @internal
	 */
	public function getForCreate($navOnly = false);

	public function getWrapperPrototype();

	public function getControlPrototype();

}
