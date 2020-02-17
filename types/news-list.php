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
    $MetaList->add('dateModified', $Site->getAttribute('e_date'));
    $MetaList->add('mainEntityOfPage', $Site->getUrlRewritten());

    // author
    $User = QUI::getUsers()->get($Site->getAttribute('c_user'));
    $MetaList->add('author', $User->getName());

    // publisher
    $Publisher = new QUI\Controls\Utils\MetaList\Publisher();
    $Publisher->importFromProject($Site->getProject());
    $MetaList->add('publisher', $Publisher);

    // image
    $image = $Site->getAttribute('image_site');

    if (\strpos($image, 'fa-') !== false) {
        $image = '';
    }

    if (MediaUtils::isMediaUrl($image)) {
        $Image = MediaUtils::getImageByUrl($image);
        $image = $Image->getSizeCacheUrl();
    }

    // use default
    if (empty($image)) {
        try {
            $Placeholder = $Site->getProject()->getMedia()->getPlaceholderImage();

            if ($Placeholder) {
                $image = $Placeholder->getSizeCacheUrl();
            }
        } catch (QUI\Exception $Exception) {
        }
    }

    $MetaList->add('image', $image);
});

$Engine->assign([
    'ChildrenList' => $ChildrenList
]);
