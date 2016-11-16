<?php

/**
 * News List
 */

$ChildrenList = new QUI\Controls\ChildrenList(array(
    'showTitle'      => false,
    'showContent'    => false,
    'showTime'       => $Site->getAttribute('quiqqer.settings.news.showTime'),
    'showCreator'    => $Site->getAttribute('quiqqer.settings.news.showCreator'),
    'Site'           => $Site,
    'where'          => array(
        'type' => 'quiqqer/news:types/news-entry'
    ),
    'limit'          => $Site->getAttribute('quiqqer.settings.news.max'),
    'itemtype'       => "http://schema.org/ItemList",
    'child-itemtype' => "http://schema.org/NewsArticle",
    'display'        => $Site->getAttribute('quiqqer.settings.news.template'),
    'parentInputList' => false
));

$Engine->assign(array(
    'ChildrenList' => $ChildrenList
));
