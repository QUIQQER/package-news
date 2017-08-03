<?php

// default
$enableDateAndCreator = true;
$showCreator          = true;
$showDate             = true;

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

$Engine->assign(array(
    'enableDateAndCreator' => $enableDateAndCreator,
    'showCreator'          => $showCreator,
    'showDate'             => $showDate
));
