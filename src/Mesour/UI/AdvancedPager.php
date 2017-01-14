<?php
/**
 * This file is part of the Mesour Pager (http://components.mesour.com/component/pager)
 *
 * Copyright (c) 2015 Matouš Němec (http://mesour.com)
 *
 * For full licence and copyright please view the file licence.md in root of this project
 */

namespace Mesour\UI;

use Mesour;

/**
 * @author Matouš Němec (http://mesour.com)
 *
 * @method null onRender(Mesour\Pager\IPager $pager)
 * @method Mesour\Components\Control\IControl current()
 */
class AdvancedPager extends Pager implements Mesour\Pager\IPager
{

	const CONTAINER = 'container';

	const RIGHT_GROUP = 'rightGroup';

	protected $maxForNormal = 15;

	protected $edgePageCount = 3;

	protected $middlePageCount = 2;

	protected $createItemsInfo = true;

	/** @var Mesour\Components\Utils\Html */
	private $rightGroup;

	public function __construct($name = null, Mesour\Components\ComponentModel\IContainer $parent = null)
	{
		$this->defaults[self::CONTAINER] = [
			'el' => 'div',
			'attributes' => [
				'class' => 'col-xs-12 col-sm-%s col-lg-%s',
			],
		];
		$this->defaults[self::RIGHT_GROUP] = [
			'el' => 'div',
			'attributes' => [
				'class' => 'input-group',
			],
		];

		parent::__construct($name, $parent);
	}

	protected function createContainerPrototype($lgWidth, $smWidth)
	{
		$attributes = $this->getOption(self::CONTAINER, 'attributes');
		if (isset($attributes['class'])) {
			$attributes['class'] = sprintf($attributes['class'], $smWidth, $lgWidth);
		}
		return Mesour\Components\Utils\Html::el(
			$this->getOption(self::CONTAINER, 'el'),
			$attributes
		);
	}

	/**
	 * @return bool
	 */
	public function isCreateItemsInfo()
	{
		return $this->createItemsInfo;
	}

	/**
	 * @param bool $createItemsInfo
	 * @return static
	 */
	public function setCreateItemsInfo($createItemsInfo = true)
	{
		$this->createItemsInfo = $createItemsInfo;
		return $this;
	}

	public function getSwitcherButton()
	{
		if (!isset($this['button'])) {
			$this['button'] = $button = new Button();
			$button->setText('Go!')
				->setType('primary')
				->setAttribute('href', '#')
				->setAttribute('data-page-button', $this->createLinkName());
		}
		return $this['button'];
	}

	public function createRightGroupPrototype()
	{
		return $this->rightGroup
			? $this->rightGroup
			: ($this->rightGroup = Mesour\Components\Utils\Html::el(
				$this->getOption(self::RIGHT_GROUP, 'el'),
				$this->getOption(self::RIGHT_GROUP, 'attributes')
			));
	}

	/**
	 * @param bool|FALSE $navOnly
	 * @return Mesour\Components\Utils\Html|string
	 * @throws Mesour\InvalidStateException
	 * @throws Mesour\InvalidArgumentException
	 * @internal
	 */
	public function getForCreate($navOnly = false)
	{
		if ($this->paginator->getPageCount() <= $this->maxForNormal) {
			$nav = parent::getForCreate(true);
		} else {
			$nav = $this->createNav();
		}

		$container = $this->createContainerPrototype(1, 1);
		$container->add('&nbsp;');
		$this->snippet->add($container)
			->class('row');

		$container = $this->createContainerPrototype(9, 8);
		$container->add($nav);
		$this->snippet->add($container)
			->class('row');

		$rightGroup = $this->createRightGroupPrototype();
		$rightGroup->add(
			sprintf(
				'<input type="text" class="form-control" value="%s" data-page-input="1">',
				$this->paginator->getPage()
			)
		);
		$rightGroup->add(
			sprintf('<span class="input-group-addon">/ %s</span>', $this->paginator->getPageCount())
		);

		if ($this->isCreateItemsInfo()) {
			$this->createItemsInfo($rightGroup);
		}

		$groupButton = Mesour\Components\Utils\Html::el('span', ['class' => 'input-group-btn']);
		$groupButton->add($this->getSwitcherButton());
		$rightGroup->add($groupButton);

		$container = $this->createContainerPrototype(2, 3);
		$container->add($rightGroup);
		$this->snippet->add($container)
			->class('row');

		return $this->snippet;
	}

	protected function createItemsInfo(Mesour\Components\Utils\Html $rightGroup)
	{
		$first = ($this->paginator->getPage() - 1) * $this->paginator->getItemsPerPage();
		$last = $this->paginator->getPage() * $this->paginator->getItemsPerPage();
		$rightGroup->add(
			sprintf(
				'<span class="input-group-addon">%s - %s / %s</span>',
				$first === 0 ? 1 : $first,
				$last > $this->paginator->getItemCount() ? $this->paginator->getItemCount() : $last,
				$this->paginator->getItemCount()
			)
		);
	}

	protected function createNav()
	{
		$nav = $this->getWrapperPrototype();

		$ul = $this->getControlPrototype();
		$ul->addAttributes(['data-link' => $this->createLinkName()]);

		$this->onRender($this);

		if ($this->paginator->getPageCount() <= 1) {
			return '';
		}
		$this->addLink(
			$ul,
			$this->getPaginator()->getPage() - 1,
			!$this->paginator->isFirst(),
			'disabled',
			'<span aria-hidden="true">&laquo;</span>'
		);

		// left
		for ($i = 1; $i <= $this->edgePageCount; $i++) {
			$this->addLink($ul, $i, $this->getPaginator()->getPage() != $i);
		}

		// left separator
		if ($this->paginator->getPage() > ($this->edgePageCount + $this->middlePageCount + 1)) {
			$this->addLink(
				$ul,
				0,
				false,
				'disabled',
				'...'
			);
		}

		// middle
		$last = $i;
		for ($i = 0; $i <= $this->middlePageCount * 2; $i++) {
			$j = $this->paginator->getPage() - $this->middlePageCount + $i;
			if ($j - 1 <= $this->middlePageCount) {
				continue;
			}
			if ($j - 1 >= $this->paginator->getPageCount()) {
				break;
			}
			$last = $j;
			$this->addLink($ul, $j, $this->getPaginator()->getPage() != $j);
		}

		// right separator
		if ($this->paginator->getPage() < $this->paginator->getPageCount() - $this->edgePageCount - 2) {
			$this->addLink($ul, 0, false, 'disabled', '...');
		}

		// right
		if ($this->paginator->getPage() < $this->paginator->getPageCount() - $this->middlePageCount) {
			for ($i = $this->edgePageCount; $i > 0; $i--) {
				$j = $this->paginator->getPageCount() - $i;
				if ($j < $last) {
					continue;
				}
				$this->addLink($ul, $j + 1, $this->getPaginator()->getPage() != $j + 1);
			}
		}

		$this->addLink(
			$ul,
			$this->getPaginator()->getPage() + 1,
			!$this->paginator->isLast(),
			'disabled',
			'<span aria-hidden="true">&raquo;</span>'
		);

		$nav->add($ul);

		return $nav;
	}

}
