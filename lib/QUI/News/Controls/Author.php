<?php

/**
 * This file contains \QUI\News\Controls\Author
 */

namespace QUI\News\Controls;

use QUI;
use QUI\Exception;

/**
 * Class Author
 *
 * @author  Dominik Chrzanowski
 * @package quiqqer/bricks
 */
class Author extends QUI\Control
{
    /**
     * constructor
     *
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        // default options
        $this->setAttributes([
            'class'    => 'quiqqer-news-control-author',
            'template' => 'largeImageTop' // template
        ]);

        $this->addCSSFile(dirname(__FILE__).'/Author.css');

        parent::__construct($attributes);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \QUI\Control::create()
     */
    public function getBody()
    {
        $Engine = QUI::getTemplateManager()->getEngine();
        $Site   = $this->getSite();

        if ($Site->getAttribute("type") !== 'quiqqer/news:types/news-entry') {
            return '';
        }

        switch ($this->getAttribute('template')) {
            case 'largeImageLeft':
                $html = '/Author.LargeImageLeft.html';
                break;

            case 'smallImageLeft':
                $html = '/Author.SmallImageLeft.html';
                break;

            case 'largeImageTop':
            default:
                $html = '/Author.LargeImageTop.html';
                break;
        }

        try {
            $authorData = $this->getAuthorData();
        } catch (Exception $Exception) {
            QUI\System\Log::addInfo($Exception->getMessage());

            $authorData = [
                'name'     => false,
                'imageUrl' => false
            ];
        }

        echo $authorData['name'] . ' ' . $authorData['imageUrl'];

        $Engine->assign([
            'this'           => $this,
            'authorName'     => $authorData['name'],
            'authorImageUrl' => $authorData['imageUrl'],
            'shortDesc'      => false // todo implement user short description
        ]);

        $Engine->assign('controlTemplate', $Engine->fetch(dirname(__FILE__).$html));

        return $Engine->fetch(dirname(__FILE__).'/Author.html');
    }

    /**
     * Get author data for news entry
     *
     * @return array|bool
     *  'name'     => string
     *  'imageUrl' => string
     *
     * @throws Exception
     * @throws QUI\Users\Exception
     */
    private function getAuthorData()
    {
        $Site = $this->getSite();

        if ($Site->getAttribute('quiqqer.settings.news.guestAuthor.enable')) {
            $data = $this->getGuestAuthorData();

            if ($data) {
                return $data;
            }
        }

        $User = QUI::getUsers()->get($Site->getAttribute('c_user'));
        $name = $User->getName();
        $url  = $User->getAvatar()->getAttribute("url");

        return [
            'name'     => $name,
            'imageUrl' => $url
        ];
    }


    /**
     * Get guest author data
     *
     * @return array|bool
     *  'name'     => string
     *  'imageUrl' => string
     *
     * @throws QUI\Exception
     * @throws QUI\Users\Exception
     */
    protected function getGuestAuthorData()
    {
        $Site = $this->getSite();

        if ($Site->getAttribute('quiqqer.settings.news.guestAuthor.quiqqerUser')) {
            $QuiqqerUser = QUI::getUsers()->get($Site->getAttribute('quiqqer.settings.news.guestAuthor.quiqqerUser'));

            try {
                return [
                    'name'     => $QuiqqerUser->getName(),
                    'imageUrl' => $QuiqqerUser->getAvatar()->getAttribute("url")
                ];
            } catch (Exception $Exception) {
                QUI\System\Log::addInfo($Exception->getMessage());
            }
        }

        $name = $Site->getAttribute('quiqqer.settings.news.guestAuthor.name');
        $src  = $Site->getAttribute('quiqqer.settings.news.guestAuthor.avatar');

        if (!$name) {
            return false;
        }

        return [
            'name'     => $name,
            'imageUrl' => $src
        ];
    }

    /**
     * @return mixed|QUI\Projects\Site
     *
     * @throws QUI\Exception
     */
    protected function getSite()
    {
        if ($this->getAttribute('Site')) {
            return $this->getAttribute('Site');
        }

        $Site = QUI::getRewrite()->getSite();

        $this->setAttribute('Site', $Site);

        return $Site;
    }
}
