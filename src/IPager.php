<?php
/**
 * Mesour Pager Component
 *
 * @license LGPL-3.0 and BSD-3-Clause
 * @copyright (c) 2015 Matous Nemec <matous.nemec@mesour.com>
 */

namespace Mesour\UI;

use Mesour\Components;
use Mesour\Pager\Paginator;

/**
 * @author mesour <matous.nemec@mesour.com>
 * @package Mesour Pager Component
 */
interface IPager extends Components\IComponent
{

    public function setCounts($total_count, $limit);

    /**
     * @return Paginator
     */
    public function getPaginator();

    public function reset();

    public function getForCreate();

    public function getWrapperPrototype();

    public function getControlPrototype();

    public function create($data = array());

}
