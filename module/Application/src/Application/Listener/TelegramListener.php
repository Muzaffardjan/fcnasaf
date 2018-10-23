<?php
/**
 * FC Nasaf official website
 *
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.each.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */

namespace Application\Listener;


use Application\Social\Telegram\Bot;
use Articles\Entity\Article;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;

class TelegramListener extends AbstractListenerAggregate
{
    /**
     * @var Bot
     */
    private $bot;

    /**
     * TelegramListener constructor.
     *
     * @param Bot $bot
     */
    public function __construct(Bot $bot)
    {
        $this->bot = $bot;
    }

    /**
     * @inheritDoc
     */
    public function attach(EventManagerInterface $events)
    {
        $events->attach(Article::EVENT_ONADD, [$this, 'onArticleAdd']);
        $events->attach(\Soccer\Module::EVENT_MATCH_EVENT_ADDED, [$this, 'onMatchEventAdded']);
    }

    public function onArticleAdd(Event $event)
    {
        $this->bot->sendMessage($event->getParam('uri'));
    }

    public function onMatchEventAdded(Event $event)
    {
        //$this->bot->sendMessage($event->getParam('message'));
    }
}