<?php

/**
 * News List
 */
/*
$start = 0;
$max   = $Site->getAttribute( 'quiqqer.settings.news.max' );

if ( !$max ) {
    $max = 5;
}

if ( isset( $_REQUEST['sheet'] ) ) {
    $start = ( (int)$_REQUEST['sheet'] - 1 ) * $max;
}

$count_children = $Site->getChildren(array(
    'count'	=> 'count',
    'where' => array(
        'type' => 'quiqqer/news:types/news-entry'
    )
));

if ( is_array( $count_children ) ) {
    $count_children = count( $count_children );
}

// sheets
$sheets = ceil( $count_children / $max );

$children = $Site->getChildren(array(
    'where' => array(
        'type' => 'quiqqer/news:types/news-entry'
    ),
    'limit' => $start .','. $max
));

$Engine->assign(array(
    'sheets'   => $sheets,
    'children' => $children
));
*/


$ChildrenList = new QUI\Controls\ChildrenList(array(
    'showContent' => false,
    'showTime'    => true,
    'showCreator' => true,

    'Site'  => $Site,
    'where' => array(
        'type' => 'quiqqer/news:types/news-entry'
    ),
    'limit' => $Site->getAttribute( 'quiqqer.settings.news.max' ),

    'itemtype'       => "http://schema.org/ItemList",
    'child-itemtype' => "http://schema.org/NewsArticle"
));

$Engine->assign(array(
    'ChildrenList' => $ChildrenList
));
