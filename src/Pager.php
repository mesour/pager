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
class Pager extends Control implements IPager
{

    const ITEMS = 'items',
        ITEMS_A = 'items-a',
        WRAPPER = 'wrapper',
        MAIN = 'main';

    /**
     * Array of items ID => string|array (statuses)
     * @var array
     */
    protected $items = array();

    protected $option = array();

    /**
     * @var Components\Html
     */
    protected $ul;

    /**
     * @var Components\Html
     */
    protected $wrapper;

    /**
     * @var Components\Html
     */
    protected $button;

    /**
     * @var Paginator
     */
    protected $paginator;

    /**
     * @var Components\Html
     */
    protected $snippet;

    /**
     * @var Components\Session\ISessionSection
     */
    private $privateSession;

    public $onRender = array();

    static public $defaults = array(
        self::MAIN => array(
            'el' => 'ul',
            'attributes' => array(
                'class' => 'pagination',
            ),
        ),
        self::ITEMS => array(
            'el' => 'li',
        ),
        self::ITEMS_A => array(
            'el' => 'a',
        ),
        self::WRAPPER => array(
            'el' => 'nav',
            'attributes' => array(),
        )
    );

    public function __construct($name = NULL, Components\IContainer $parent = NULL)
    {
        if (is_null($name)) {
            throw new Components\InvalidArgumentException('Component name is required.');
        }
        parent::__construct($name, $parent);
        $this->option = self::$defaults;
        $this->paginator = new Paginator;
        $this->privateSession = $this->getSession()->getSection($this->createLinkName());
    }

    public function attached(Components\IContainer $parent)
    {
        parent::attached($parent);
        $this->snippet = $this->createSnippet();
    }

    public function setCounts($total_count, $limit)
    {
        $this->paginator->setItemCount($total_count);
        $this->paginator->setItemsPerPage($limit);

        $page = $this->privateSession->get('page');

        if (!$page) {
            $this->privateSession->set('page', $page = 1);
        }
        if ($page > $this->paginator->getPageCount()) {
            $this->privateSession->set('page', $page = $this->paginator->getPageCount());
        }

        $this->paginator->setPage($page ? $page : 1);
    }

    public function handleSetPage($page = NULL)
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
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    public function getWrapperPrototype()
    {
        return $this->wrapper ? $this->wrapper : ($this->wrapper = Components\Html::el($this->option[self::WRAPPER]['el'], $this->option[self::WRAPPER]['attributes']));
    }

    protected function getItemPrototype(array $attributes = array())
    {
        return Components\Html::el($this->option[self::ITEMS]['el'], $attributes);
    }

    protected function getItemAnchorPrototype(array $attributes = array())
    {
        return Components\Html::el($this->option[self::ITEMS_A]['el'], $attributes);
    }

    public function getControlPrototype()
    {
        $attributes = $this->option[self::MAIN]['attributes'];
        $attributes = array_merge($attributes, array(
            'data-name' => $this->getName(),
        ));
        return $this->ul ? $this->ul : ($this->ul = Components\Html::el($this->option[self::MAIN]['el'], $attributes));
    }

    /**
     * @return Components\Html|string
     * @throws Components\BadStateException
     * @throws Components\InvalidArgumentException
     * @internal
     */
    public function getForCreate()
    {
        $nav = $this->getWrapperPrototype();

        $ul = $this->getControlPrototype();
        $ul->addAttributes(array('data-link' => $this->createLinkName()));

        $this->onRender($this);

        if ($this->paginator->getPageCount() <= 1) {
            return '';
        }

        $first_args = array();
        if (!$this->paginator->isFirst()) {
            $first_args = array(
                'href' => $this->getApplication()->createLink($this, 'setPage', array(
                    'page' => 0
                )),
                'data-mesour' => 'ajax',
            );
        }
        $li = $this->getItemPrototype(array(
            'class' => $this->paginator->isFirst() ? 'disabled' : '',
        ))->add($this->getItemAnchorPrototype($first_args)->setHtml('<span aria-hidden="true">&laquo;</span>'));
        $ul->add($li);

        for ($i = 1; $i <= $this->paginator->getPageCount(); $i++) {
            $item_args = array();
            if ($this->paginator->getPage() != $i) {
                $item_args = array(
                    'href' => $this->getApplication()->createLink($this, 'setPage', array(
                        'page' => $i
                    )),
                    'data-mesour' => 'ajax',
                );
            }
            $li = $this->getItemPrototype(array(
                'class' => $this->paginator->getPage() == $i ? 'active' : ''
            ))->add($this->getItemAnchorPrototype($item_args)->setText($i));
            $ul->add($li);
        }

        $last_args = array();
        if (!$this->paginator->isLast()) {
            $last_args = array(
                'href' => $this->getApplication()->createLink($this, 'setPage', array(
                    'page' => $this->paginator->getPageCount()
                )),
                'data-mesour' => 'ajax',
            );
        }
        $li = $this->getItemPrototype(array(
            'class' => $this->paginator->isLast() ? 'disabled' : ''
        ))->add($this->getItemAnchorPrototype($last_args)->setHtml('<span aria-hidden="true">&raquo;</span>'));
        $ul->add($li);

        $nav->add($ul);

        $this->snippet->add($nav);

        return $this->snippet;
    }

    public function create($data = array())
    {
        parent::create();
        return $this->getForCreate();
    }

}
