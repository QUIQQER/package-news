<?php

if (isset($_REQUEST['sheet'])
    && \is_numeric($_REQUEST['sheet'])
    && (int)$_REQUEST['sheet'] > 1) {
    $Site->setAttribute('meta.robots', 'noindex,follow');
}

/**
 * News List
 */

$ChildrenList = new QUI\Controls\ChildrenList([
    'showTitle'       => false,
    'showContent'     => false,
    'showImages'      => $Site->getAttribute('quiqqer.settings.news.showImages'),
    'showHeader'      => $Site->getAttribute('quiqqer.settings.news.showHeader'),
    'showShort'       => $Site->getAttribute('quiqqer.settings.news.showShort'),
    'showCreator'     => $Site->getAttribute('quiqqer.settings.news.showCreator'),
    'showDate'        => $Site->getAttribute('quiqqer.settings.news.showDate'),
    'showTime'        => $Site->getAttribute('quiqqer.settings.news.showTime'),
    'Site'            => $Site,
    'where'           => [
        'type' => 'quiqqer/news:types/news-entry'
    ],
    'limit'           => $Site->getAttribute('quiqqer.settings.news.max'),
    'itemtype'        => "http://schema.org/ItemList",
    'child-itemtype'  => "http://schema.org/NewsArticle",
    'child-itemprop'  => "",
    'display'         => $Site->getAttribute('quiqqer.settings.news.template'),
    'parentInputList' => false
]);

$Engine->assign([
    'ChildrenList' => $ChildrenList
]);
