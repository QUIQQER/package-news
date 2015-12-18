<?php

/**
 * This file contains \QUI\News\NewsList
 */

namespace QUI\News;

use QUI;

/**
 * News list helper class
 *
 * @author www.pcsg.de (Henning Leutz)
 */
class NewsList
{
    /**
     * event on child create
     *
     * @param Integer $newId
     * @param \QUI\Projects\Site\Edit $Parent
     */
    public static function onChildCreate($newId, $Parent)
    {
        if ($Parent->getAttribute('type') !== 'quiqqer/news:types/news-list') {
            return;
        }

        $Project = $Parent->getProject();
        $Site    = new QUI\Projects\Site\Edit($Project, $newId);

        $Site->setAttribute('nav_hide', 1);
        $Site->setAttribute('release_from', date('Y-m-d H:i:s'));
        $Site->setAttribute('type', 'quiqqer/news:types/news-entry');
        $Site->save();
    }
}
