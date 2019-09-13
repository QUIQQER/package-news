<?php

$Config = QUI::getPackage('quiqqer/news')->getConfig();

// default
$enableDateAndCreator = true;
$showCreator          = true;
$showDate             = true;

$amountOfSiblings    = $Config->getValue('further_news', 'amount');
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


// Reverse since the sorting/ordering in previous siblings is reversed
$previousSiblings = \array_reverse($Site->previousSiblings($amountOfSiblings));
$nextSiblings     = $Site->nextSiblings($amountOfSiblings);

$Engine->assign([
    'enableDateAndCreator' => $enableDateAndCreator,
    'showCreator'          => $showCreator,
    'showDate'             => $showDate,
    'showFurtherNewsDate'  => $showFurtherNewsDate,
    'showFurtherNewsTime'  => $showFurtherNewsTime,
    'previousSiblings'     => $previousSiblings,
    'nextSiblings'         => $nextSiblings,
]);
