<?php

/**
 * Declare "global" variables for PHPStan and IDEs
 *
 * @var \QUI\Interfaces\Projects\Site $Site
 * @var \QUI\Interfaces\Template\EngineInterface $Engine
 */

use QUI\Projects\Media\Utils as MediaUtils;

$Config = QUI::getPackage('quiqqer/news')->getConfig();

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
$MetaList->add('headline', $Site->getAttribute('title'));
$MetaList->add('datePublished', $Site->getAttribute('release_from'));
$MetaList->add('dateModified', $Site->getAttribute('e_date'));
$MetaList->add('mainEntityOfPage', $Site->getUrlRewritten());

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

if (\strpos($image, 'fa-') !== false) {
    $image = '';
}

if (MediaUtils::isMediaUrl($image)) {
    try {
        $Image = MediaUtils::getImageByUrl($image);
        $image = $Image->getSizeCacheUrl();
    } catch (QUI\Exception $Exception) {
    }
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
