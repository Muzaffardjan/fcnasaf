<?php
/**
 * FC Nasaf official website
 *
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.each.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */

namespace Soccer\Controller;


use Admin\Controller\AbstractObjectManagerAwareController;
use Soccer\Entity\Comment;
use Soccer\Entity\Match;
use Soccer\Entity\MatchEvent;
use Soccer\Entity\MatchPeriod;
use Soccer\Form\CommentMatchEventForm;
use Soccer\Form\PeriodMatchEventForm;
use Soccer\Module;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class MatchEventsController extends AbstractObjectManagerAwareController
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var Match
     */
    protected $match;

    /**
     * @var ViewModel $view
     */
    private $view;

    public function onDispatch(MvcEvent $event)
    {
        /**
         * @var Match $match
         */
        if (
            !(
                $this->params('match')
                && ($match = $this->getObjectManager()->getRepository(Match::class)->find($this->params('match')))
             )
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->setMatch($match);
        parent::onDispatch($event);
    }

    /**
     * @return Match
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @param Match $match
     * @return MatchEventsController
     */
    public function setMatch(Match $match)
    {
        $this->match = $match;

        return $this;
    }

    public function getServiceManager()
    {
        if (null === $this->serviceManager) {
            $this->serviceManager = $this->getEvent()->getApplication()->getServiceManager();
        }

        return $this->serviceManager;
    }

    public function commentAction()
    {
        $form = $this->getServiceManager()->get(CommentMatchEventForm::class)->init();

        $started = $this->getMatch()->getStarted();

        if ($started && !$this->getMatch()->getEnded() && $this->getMatch()->getStatus() != Match::STATUS_FINISHED) {
            $now = new \DateTime();
            $form->get('minutes')->setValue(abs($now->getTimestamp() - $started->getTimestamp()) / 60);
            $form->get('seconds')->setValue(abs($now->getTimestamp() - $started->getTimestamp()) % 60);
        } else {
            $form->get('minutes')->setValue(0);
            $form->get('seconds')->setValue(0);
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $data       = $form->getData();
                $comment    = new Comment();
                $matchEvent = new MatchEvent();

                $comment->setLocale($data['locale'])
                    ->setText($data['text'])
                    ->setMatchEvent($matchEvent);

                $matchEvent
                    ->setMinutes($data['minutes'])
                    ->setSeconds($data['seconds'])
                    ->setExtra($data['extra'])
                    ->setComment($comment)
                    ->setMatch($this->getMatch());

                $this->getEvent()
                    ->getApplication()
                    ->getEventManager()
                    ->trigger(Module::EVENT_MATCH_EVENT_ADDED, $matchEvent);

                $this->getObjectManager()->persist($matchEvent);
                $this->getObjectManager()->persist($comment);

                return new JsonModel(['status' => true]);
            }
        }

        return $this->getView()->setVariable('form', $form);
    }

    public function periodAction()
    {
        $form = $this->getServiceManager()->get(PeriodMatchEventForm::class)->init();

        $started = $this->getMatch()->getStarted();

        if ($started && !$this->getMatch()->getEnded() && $this->getMatch()->getStatus() != Match::STATUS_FINISHED) {
            $now = new \DateTime();
            $form->get('minutes')->setValue(abs($now->getTimestamp() - $started->getTimestamp()) / 60);
            $form->get('seconds')->setValue(abs($now->getTimestamp() - $started->getTimestamp()) % 60);
        } else {
            $form->get('minutes')->setValue(0);
            $form->get('seconds')->setValue(0);
        }

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid()) {
                $data       = $form->getData();
                $period     = new MatchPeriod();
                $matchEvent = new MatchEvent();

                $period->setType($data['type']);

                switch ($period->getType()) {
                    case MatchPeriod::TYPE_START:
                        $this->getMatch()->setStarted(new \DateTime());
                        $this->getMatch()->setStatus(Match::STATUS_ONGOING);
                        break;
                    case MatchPeriod::TYPE_FINISH:
                        $this->getMatch()->setEnded(new \DateTime());
                        $this->getMatch()->setStatus(Match::STATUS_FINISHED);
                        break;
                }

                $matchEvent
                    ->setMinutes($data['minutes'])
                    ->setSeconds($data['seconds'])
                    ->setExtra($data['extra'])
                    ->setMatchPeriod($period)
                    ->setMatch($this->getMatch());

                $this->getEvent()
                    ->getApplication()
                    ->getEventManager()
                    ->trigger(Module::EVENT_MATCH_EVENT_ADDED, $matchEvent);

                $this->getObjectManager()->persist($matchEvent);
                $this->getObjectManager()->persist($period);

                return new JsonModel(['status' => true, 'type' => $period->getType()]);
            }
        }

        return $this->getView()->setVariable('form', $form);
    }

    public function deleteAction()
    {
        $eventsR = $this->getObjectManager()->getRepository(MatchEvent::class);

        if (!($this->getRequest()->getQuery('id') && ($event = $eventsR->find($this->getRequest()->getQuery('id'))))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->getObjectManager()->remove($event);
        $this->getObjectManager()->flush();

        return new JsonModel(['status' => true]);
    }

    private function getView()
    {
        if (null === $this->view) {
            $this->view = new ViewModel();

            $this->view->setTerminal(true);
        }

        return $this->view;
    }
}