<?php

use QUI\Projects\Media\Utils as MediaUtils;

if (isset($_REQUEST['sheet'])
    && \is_numeric($_REQUEST['sheet'])
    && (int)$_REQUEST['sheet'] > 1

    || isset($_REQUEST['limit'])
) {
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

$ChildrenList->addEvent('onMetaList', function (
    QUI\Controls\ChildrenList $ChildrenList,
    QUI\Interfaces\Projects\Site $Site,
    QUI\Controls\Utils\MetaList $MetaList
) {
    $MetaList->add('headline', $Site->getAttribute('title'));
    $MetaList->add('datePublished', $Site->getAttribute('release_from'));

    // author
    $User = QUI::getUsers()->get($Site->getAttribute('c_user'));
    $MetaList->add('author', $User->getName());

    // publisher
    $Project       = $Site->getProject();
    $publisher     = $Project->getConfig('publisher');
    $publisherType = $Project->getConfig('publisher_type');

    if (empty($publisher)) {
        $publisher = $User->getName();
    }

    $publisher = \htmlspecialchars($publisher);
    $itemType  = 'https://schema.org/Organization';

    if ($publisherType === 'person') {
        $itemType = 'https://schema.org/Person';
    }

    $MetaList->add('publisher', [
        'nodeName'  => 'div',
        'itemscope' => '',
        'itemtype'  => $itemType,
        'html'      => '<meta itemprop="name" content="'.$publisher.'">'
    ]);

    // image
    $image = $Site->getAttribute('image_site');

    if (\strpos($image, 'fa-') !== false) {
        $image = '';
    }

    if (MediaUtils::isMediaUrl($image)) {
        $Image = MediaUtils::getImageByUrl($image);
        $image = $Image->getSizeCacheUrl();
    }

    $MetaList->add('image', $image);
});

$Engine->assign([
    'ChildrenList' => $ChildrenList
]);
