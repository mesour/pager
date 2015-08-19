<?php
/**
 * This file is part of the Mesour Pager (http://components.mesour.com/component/pager)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\UI;

use Mesour\Components;
use Mesour\Pager\Paginator;



/**
 * @author Matouš Němec <matous.nemec@mesour.com>
 */
interface IPager extends Components\IComponent
{

    public function setCount($count);

    /**
     * @return Paginator
     */
    public function getPaginator();

    public function reset();

    /**
     * @return mixed
     * @internal
     */
    public function getForCreate();

    public function getWrapperPrototype();

    public function getControlPrototype();

    public function create($data = array());

}
