<?php

/**
 * News List
 */

$ChildrenList = new QUI\Controls\ChildrenList(array(
    'showTitle'       => false,
    'showContent'     => false,
    'showImages'      => $Site->getAttribute('quiqqer.settings.news.showImages'),
    'showShort'       => $Site->getAttribute('quiqqer.settings.news.showShort'),
    'showCreator'     => $Site->getAttribute('quiqqer.settings.news.showCreator'),
    'showDate'        => $Site->getAttribute('quiqqer.settings.news.showDate'),
    'showTime'        => $Site->getAttribute('quiqqer.settings.news.showTime'),
    'Site'            => $Site,
    'where'           => array(
        'type' => 'quiqqer/news:types/news-entry'
    ),
    'limit'           => $Site->getAttribute('quiqqer.settings.news.max'),
    'itemtype'        => "http://schema.org/ItemList",
    'child-itemtype'  => "http://schema.org/NewsArticle",
    'child-itemprop'  => "",
    'display'         => $Site->getAttribute('quiqqer.settings.news.template'),
    'parentInputList' => false
));

$Engine->assign(array(
    'ChildrenList' => $ChildrenList
));
