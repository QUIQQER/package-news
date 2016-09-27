<?php

/**
 * This file contains QUI/News/Bricks/EntryList
 */

namespace QUI\News\Bricks;

use QUI;

/**
 * Class EntryList
 * @package QUI\News\Controls
 */
class NewsEntryList extends QUI\Control
{

    public function __construct($attributes = array())
    {
        // default options
        $this->setAttributes(array(
            'max'        => 5,
            'Project'    => false,
            'class'      => 'quiqqer-news-entrylist',
            'dateFormat' => '%d',
            'dayFormat'  => '%a'
        ));

        $this->addCSSFile(
            dirname(__FILE__) . '/NewsEntryList.css'
        );

        parent::__construct($attributes);
    }

    public function getBody()
    {
        $Engine  = QUI::getTemplateManager()->getEngine();
        $Project = $this->getProject();

        $children = $Project->getSites(array(
            'where' => array(
                'type' => 'quiqqer/news:types/news-entry-list'
            ),
            'limit' => (int)$this->getAttribute('max'),
            'order' => 'release_from DESC'
        ));

        $Engine->assign(array(
            'children' => $children,
            'this'     => $this
        ));

        return $Engine->fetch(dirname(__FILE__) . '/NewsEntryList.html');
    }
}
