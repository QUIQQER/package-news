<?php

/**
 * News List
 */

$max   = 5;
$start = 0;

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
