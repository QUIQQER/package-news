<?php

/**
 * Declare "global" variables for PHPStan and IDEs
 *
 * @var \QUI\Interfaces\Projects\Site $Site
 * @var \QUI\Interfaces\Template\EngineInterface $Engine
 */

/** @var QUI\Template $Template */


use QUI\Projects\Media\Utils as MediaUtils;

$Config = QUI::getPackage('quiqqer/news')->getConfig();

if (! $Config instanceof \QUI\Config) {
    throw new \QUI\Exception('Could not load quiqqer/news config');
}

// default
$enableDateAndCreator = true;
$showCreator = true;
$showDate = true;

$amountOfSiblings = $Config->getValue('further_news', 'amount');
$showFurtherNewsDate = $Config->getValue('further_news', 'show_date');
$showFurtherNewsTime = $Config->getValue('further_news', 'show_time');

switch ($Site->getAttribute('quiqqer.settings.news.entry.dateAndCreator')) {
    case 'showCreator':
        $showDate = false; // hide date
        break;
    case 'showDate':
        $showCreator = false; // hide author
        break;
    case 'hide':
        $enableDateAndCreator = false; // disable date and author
        break;
}

// Meta
$MetaList = new QUI\Controls\Utils\MetaList();
$MetaList->add('type', 'NewsArticle');
$MetaList->add('headline', $Site->getAttribute('title'));
$MetaList->add('description', $Site->getAttribute('short'));
$MetaList->add('datePublished', $Site->getAttribute('release_from'));
$MetaList->add('dateModified', $Site->getAttribute('e_date'));
$MetaList->add('mainEntityOfPage', $Site->getUrlRewrittenWithHost());

try {
    // author
    $User = QUI::getUsers()->get($Site->getAttribute('c_user'));
    $MetaList->add('author', $User->getName());
    $author = $User->getName();
} catch (QUI\Exception $Exception) {
    QUI\System\Log::writeException($Exception);
    $author = null;
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

// Reverse since the sorting/ordering in previous siblings is reversed
$previousSiblings = \array_reverse($Site->previousSiblings($amountOfSiblings));
$nextSiblings = $Site->nextSiblings($amountOfSiblings);

$Engine->assign([
    'enableDateAndCreator' => $enableDateAndCreator,
    'showCreator' => $showCreator,
    'showDate' => $showDate,
    'showFurtherNewsDate' => $showFurtherNewsDate,
    'showFurtherNewsTime' => $showFurtherNewsTime,
    'previousSiblings' => $previousSiblings,
    'nextSiblings' => $nextSiblings,
    'MetaList' => $MetaList,
    'author' => $author
]);

// json schema
try {
    $Template->extendHeader($MetaList->getJsonLdSchema());
} catch (\QUI\Exception $e) {
    QUI\System\Log::addWarning($e->getMessage());
}
