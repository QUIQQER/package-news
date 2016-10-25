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
            'max'         => 5,
            'Project'     => false,
            'class'       => 'quiqqer-news-entrylist',
            'dateFormat'  => '%d-%m-%Y',
            'dayFormat'   => '%a',
            'showShort'   => true,
            'showCreator' => true,
            'showDate'    => true,
            'showTime'    => false,
            'showImages'  => true,
            'template'    => 'standard'
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


        $max = $this->getAttribute('news.max');

        $children = $Project->getSites(array(
            'where' => array(
                'type' => 'quiqqer/news:types/news-entry'
            ),
            'limit' => (int)$max,
            'order' => 'release_from DESC'
        ));

        $Engine->assign(array(
            'children' => $children,
            'this'     => $this
        ));

        switch ($this->getAttribute('news.template')) {
            case 'simpleArticleList':
                $this->addCSSFile(dirname(__FILE__) . '/NewsEntryList.SimpleArticleList.css');

                return $Engine->fetch(dirname(__FILE__) . '/NewsEntryList.SimpleArticleList.html');
            case 'advancedArticleList':
                $this->addCSSFile(dirname(__FILE__) . '/NewsEntryList.AdvancedArticleList.css');

                return $Engine->fetch(dirname(__FILE__) . '/NewsEntryList.AdvancedArticleList.html');
            case 'border':
                $this->addCSSFile(dirname(__FILE__) . '/NewsEntryList.Border.css');

                return $Engine->fetch(dirname(__FILE__) . '/NewsEntryList.Border.html');
            case 'standard':
            default:
                $this->addCSSFile(dirname(__FILE__) . '/NewsEntryList.Standard.css');

                return $Engine->fetch(dirname(__FILE__) . '/NewsEntryList.Standard.html');
        }
    }
}
