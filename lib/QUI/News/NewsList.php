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
     * @param integer $newId
     * @param \QUI\Projects\Site\Edit $Parent
     * @throws QUi\Exception
     */
    public static function onChildCreate($newId, $Parent): void
    {
        if ($Parent->getAttribute('type') !== 'quiqqer/news:types/news-list') {
            return;
        }

        $Project = $Parent->getProject();
        $Site = new QUI\Projects\Site\Edit($Project, $newId);

        $Site->setAttribute('nav_hide', 1);
        $Site->setAttribute('type', 'quiqqer/news:types/news-entry');
        $Site->save();
    }

    /**
     * @param QUI\Projects\Site\Edit $Site
     */
    public static function onSiteSaveBefore($Site): void
    {
        if ($Site->getAttribute('type') !== 'quiqqer/news:types/news-list') {
            return;
        }

        if ($Site->getAttribute('order') === false || $Site->getAttribute('order') === '') {
            $Site->setAttribute('order_type', 'release_from DESC');
        }
    }
}
