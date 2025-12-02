<?php

/**
 * Declare "global" variables for PHPStan and IDEs
 * @var QUI\Projects\Project $Project
 * @var \QUI\Interfaces\Projects\Site $Site
 * @var \QUI\Interfaces\Template\EngineInterface $Engine
 * @var QUI\Template $Template
 */

use QUI\Projects\Media\Image;
use QUI\Projects\Media\Utils as MediaUtils;


$a = $Project->getConfig('news.settings.entry.showTitle');
// default
$enableDateAndCreator = true;
$showCreator = false;
$showDate = false;

if ($Project->getConfig('news.settings.entry.showCreator')) {
    $showCreator = $Project->getConfig('news.settings.entry.showCreator');
}

if ($Project->getConfig('news.settings.entry.showDate')) {
    $showDate = $Project->getConfig('news.settings.entry.showDate');
}

switch ($Site->getAttribute('quiqqer.settings.news.entry.dateAndCreator')) {
    case 'showAll':
        $showCreator = true;
        $showDate = true;
        break;

    case 'showCreator':
        // hide date
        $showCreator = true;
        $showDate = false;
        break;

    case 'showDate':
        // hide author
        $showDate = true;
        $showCreator = false;
        break;

    case 'hide':
        // disable date and author
        $enableDateAndCreator = false;
        break;
}

if (!$showCreator && !$showDate) {
    $enableDateAndCreator = false;
}

/**
 * Meta
 */
$MetaList = new QUI\Controls\Utils\MetaList();
$MetaList->add('type', 'NewsArticle');
$MetaList->add('headline', $Site->getAttribute('title'));
$MetaList->add('description', $Site->getAttribute('short'));
$MetaList->add('datePublished', $Site->getAttribute('release_from'));
$MetaList->add('dateModified', $Site->getAttribute('e_date'));
$MetaList->add('mainEntityOfPage', $Site->getUrlRewrittenWithHost());

/**
 * Author
 */
$quiqqerUser = $Site->getAttribute('c_user');
$userName = null;
$userAvatar = null;

// guest author enabled?
if ($Site->getAttribute('quiqqer.settings.news.guestAuthor.enable')) {
    $guestUser = $Site->getAttribute('quiqqer.settings.news.guestAuthor.quiqqerUser');
    $guestName = $Site->getAttribute('quiqqer.settings.news.guestAuthor.name');
    $guestAvatar = $Site->getAttribute('quiqqer.settings.news.guestAuthor.avatar');

    if ($guestUser) {
        $quiqqerUser = $guestUser;
    } elseif ($guestName) {
        $userName = $guestName;
        $quiqqerUser = null;

        if ($guestAvatar) {
            $userAvatar = $guestAvatar;
        }
    }
}

if ($quiqqerUser) {
    try {
        $User = QUI::getUsers()->get($quiqqerUser);
        $MetaList->add('author', $User->getName());
        $Engine->assign('author', $User->getName());
    } catch (QUI\Exception $Exception) {
        QUI\System\Log::addInfo($Exception->getMessage(), [
            'project' => $Project->getName(),
            'lang' => $Project->getLang(),
            'site' => $Site->getId()
        ]);
        $Engine->assign('author', null);
    }
} else {
    $MetaList->add('author', $userName);
    $Engine->assign('author', $userName);
}

// publisher
$Publisher = new QUI\Controls\Utils\MetaList\Publisher();
$Publisher->importFromProject($Site->getProject());
$MetaList->add('publisher', $Publisher);

// image
$image = $Site->getAttribute('image_site');
$imageAbsolutePath = '';
$host = QUI::getRequest()->getHost();
$scheme = QUI::getRequest()->getScheme();

if (\strpos($image, 'fa-') !== false) {
    $image = '';
}

if (MediaUtils::isMediaUrl($image)) {
    try {
        $Image = MediaUtils::getImageByUrl($image);
        // structured data needs absolute urls for images
        $imageAbsolutePath = $scheme . '://' . $host . $Image->getSizeCacheUrl();
    } catch (QUI\Exception $Exception) {
    }
}

// use default
if (empty($imageAbsolutePath)) {
    try {
        $Placeholder = $Site->getProject()->getMedia()->getPlaceholderImage();

        if ($Placeholder) {
            // structured data needs absolute urls for images
            $imageAbsolutePath = $scheme . '://' . $host . $Placeholder->getSizeCacheUrl();
        }
    } catch (QUI\Exception $Exception) {
    }
}

if (!empty($imageAbsolutePath)) {
    $MetaList->add('image', $imageAbsolutePath);
}

/**
 * More news entries
 */
$amountOfSiblings = $Project->getConfig('news.settings.entry.more.amount');
$moreEntriesShowDate = $Project->getConfig('news.settings.entry.show_date');
$moreEntriesShowTime = $Project->getConfig('news.settings.entry.show_time');
// Reverse since the sorting/ordering in previous siblings is reversed
$previousSiblings = \array_reverse($Site->previousSiblings($amountOfSiblings));
$nextSiblings = $Site->nextSiblings($amountOfSiblings);

$Engine->assign([
    'enableDateAndCreator' => $enableDateAndCreator,
    'showCreator' => $showCreator,
    'showDate' => $showDate,
    'showFurtherNewsDate' => $moreEntriesShowDate,
    'showFurtherNewsTime' => $moreEntriesShowTime,
    'previousSiblings' => $previousSiblings,
    'nextSiblings' => $nextSiblings,
    'MetaList' => $MetaList,
    'showTitle' => $Project->getConfig('news.settings.entry.showTitle'),
    'showDescription' => $Project->getConfig('news.settings.entry.showDescription')
]);

// json schema
try {
    $Template->extendHeader($MetaList->getJsonLdSchema());
} catch (\QUI\Exception $e) {
    QUI\System\Log::addWarning($e->getMessage());
}
