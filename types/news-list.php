<?php

/**
 * News List
 */

$ChildrenList = new QUI\Controls\ChildrenList(array(
    'showContent'    => false,
    'showTime'       => true,
    'showCreator'    => true,
    'Site'           => $Site,
    'where'          => array(
        'type' => 'quiqqer/news:types/news-entry'
    ),
    'limit'          => $Site->getAttribute('quiqqer.settings.news.max'),
    'itemtype'       => "http://schema.org/ItemList",
    'child-itemtype' => "http://schema.org/NewsArticle",
    'display'        => $Site->getAttribute('quiqqer.settings.news.template')
));

$Engine->assign(array(
    'ChildrenList' => $ChildrenList
));
