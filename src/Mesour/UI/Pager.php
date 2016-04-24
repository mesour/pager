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
class Pager extends Mesour\Components\Control\AttributesControl implements Mesour\Pager\IPager
{

	const ITEMS = 'items';

	const ITEMS_A = 'items-a';

	const WRAPPER = 'wrapper';

	const MAIN = 'main';

	/**
	 * Array of items ID => string|array (statuses)
	 * @var array
	 */
	protected $items = [];

	/** @var Mesour\Components\Utils\Html */
	protected $ul;

	/** @var Mesour\Components\Utils\Html */
	protected $button;

	/** @var Mesour\Pager\Paginator */
	protected $paginator;

	/** @var Mesour\Components\Utils\Html */
	protected $snippet;

	/** @var Mesour\Components\Session\ISessionSection */
	private $privateSession;

	public $onRender = [];

	public $defaults = [
		self::MAIN => [
			'el' => 'ul',
			'attributes' => [
				'class' => 'pagination',
			],
		],
		self::ITEMS => [
			'el' => 'li',
		],
		self::ITEMS_A => [
			'el' => 'a',
		],
		self::WRAPPER => [
			'el' => 'nav',
			'attributes' => [],
		],
	];

	public function __construct($name = null, Mesour\Components\ComponentModel\IContainer $parent = null)
	{
		if (is_null($name)) {
			throw new Mesour\InvalidArgumentException('Component name is required.');
		}
		parent::__construct($name, $parent);

		$this->paginator = new Mesour\Pager\Paginator;
		$this->startPrivateSession();

		$this->setHtmlElement(
			Mesour\Components\Utils\Html::el(
				$this->getOption(self::WRAPPER, 'el'),
				$this->getOption(self::WRAPPER, 'attributes')
			)
		);
	}

	public function attached(Mesour\Components\ComponentModel\IContainer $parent)
	{
		parent::attached($parent);

		$this->snippet = $this->createSnippet();
		$this->startPrivateSession(true);
		return $this;
	}

	public function setCount($totalCount)
	{
		$this->paginator->setItemCount($totalCount);

		$page = $this->privateSession->get('page');

		if (!$page) {
			$this->privateSession->set('page', $page = 1);
		}
		if ($page > $this->paginator->getPageCount()) {
			$this->privateSession->set('page', $page = $this->paginator->getPageCount());
		}

		$this->paginator->setPage($page ? $page : 1);
	}

	public function handleSetPage($page = null)
	{
		$this->privateSession->set('page', $page);
		$this->paginator->setPage($page);
	}

	public function reset()
	{
		$this->privateSession->set('page', 1);
		$this->paginator->setPage(1);
	}

	/**
	 * @return Mesour\Pager\Paginator
	 */
	public function getPaginator()
	{
		return $this->paginator;
	}

	public function getWrapperPrototype()
	{
		return $this->getHtmlElement();
	}

	protected function createItemPrototype(array $attributes = [])
	{
		return Mesour\Components\Utils\Html::el($this->getOption(self::ITEMS, 'el'), $attributes);
	}

	protected function createItemAnchorPrototype(array $attributes = [])
	{
		return Mesour\Components\Utils\Html::el($this->getOption(self::ITEMS_A, 'el'), $attributes);
	}

	public function getControlPrototype()
	{
		$attributes = $this->getOption(self::MAIN, 'attributes');
		$attributes = array_merge(
			$attributes,
			['data-name' => $this->getName()]
		);
		return $this->ul
			? $this->ul
			: ($this->ul = Mesour\Components\Utils\Html::el($this->getOption(self::MAIN, 'el'), $attributes));
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

		for ($i = 1; $i <= $this->paginator->getPageCount(); $i++) {
			$this->addLink($ul, $i, $this->getPaginator()->getPage() != $i);
		}

		$this->addLink(
			$ul,
			$this->getPaginator()->getPage() + 1,
			!$this->paginator->isLast(),
			'disabled',
			'<span aria-hidden="true">&raquo;</span>'
		);

		$nav->add($ul);

		if ($navOnly) {
			return $nav;
		}

		$this->snippet->add($nav);

		return $this->snippet;
	}

	protected function addLink(
		Mesour\Components\Utils\Html $ul,
		$iteratorCount,
		$isEnabled = true,
		$activeClass = 'active',
		$activeValue = null
	) {
		$activeValue = is_null($activeValue) ? $iteratorCount : $activeValue;
		$itemArgs = [];
		if ($isEnabled) {
			$itemArgs = [
				'href' => $this->getApplication()->createLink(
					$this,
					'setPage',
					[
						'page' => $iteratorCount,
					]
				),
				'data-mesour' => 'ajax',
			];
		}
		$li = $this->createItemPrototype(
			[
				'class' => !$isEnabled ? $activeClass : '',
			]
		)->add($this->createItemAnchorPrototype($itemArgs)->setHtml($activeValue));
		$ul->add($li);
	}

	public function create($data = [])
	{
		parent::create();
		return $this->getForCreate();
	}

	public function __clone()
	{
		$this->ul = null;
		$this->paginator = clone $this->paginator;

		parent::__clone();
	}

	private function startPrivateSession($force = false)
	{
		if ($force || !$this->privateSession) {
			$this->privateSession = $this->getSession()->getSection($this->createLinkName());
		}
	}

}
