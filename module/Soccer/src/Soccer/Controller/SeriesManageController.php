<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Controller;

use Admin\Controller\AbstractObjectManagerAwareController;
use Soccer\Entity\Club;
use Soccer\Entity\Series;
use Soccer\Entity\Stage;
use Soccer\Form\SeriesForm;
use Soccer\Repository\SeriesRepository;
use Zend\Form\Form;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * SeriesManageController
 */
class SeriesManageController extends AbstractObjectManagerAwareController
{
    /**
     * @var Stage
     */
    protected $stage;

    /**
     * @var SeriesForm
     */
    protected $seriesForm;

    /**
     * SeriesManageController constructor.
     *
     * @param SeriesForm $seriesForm
     */
    public function __construct(SeriesForm $seriesForm)
    {
        $this->seriesForm = $seriesForm;
    }

    /**
     * @return Stage
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @param Stage $stage
     * @return self
     */
    public function setStage(Stage $stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function onDispatch(MvcEvent $event)
    {
        /**
         * @var Stage $stage
         */
        $stagesR = $this->getObjectManager()->getRepository(Stage::class);
        $stage   = $this->params('stage');

        if (!$stage
            || !($stage = $stagesR->find($stage))
            || $stage->getType() != Stage::TYPE_PLAY_OFF_SUB_STAGE
        ) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $this->setStage($stage);

        $this->seriesForm->setStage($this->getStage());

        parent::onDispatch($event);

        $event->getApplication()->getEventManager()
            ->attach(
                MvcEvent::EVENT_RENDER,
                [
                    $this,
                    'onRender',
                ]
            );
    }

    /**
     * Mvc 'Render' event listener
     * Assigns common vars for view model
     *
     * @param MvcEvent $event
     */
    public function onRender(MvcEvent $event)
    {
        /**
         * @var ViewModel $layout
         * @var ViewModel $childView
         */
        $layout = $event->getViewModel();

        foreach ($layout as $childView) {
            $childView->setVariable(
                'stage',
                $this->getStage()
            );
        }
    }

    public function indexAction()
    {
        /**
         * @var SeriesRepository $seriesR
         */
        $seriesR = $this->getObjectManager()
            ->getRepository(Series::class);
        $final = $seriesR->findFinalSeriesOf($this->getStage()->getParent());

        return [
            'final' => $final,
        ];
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $form    = $this->seriesForm;

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data   = $form->getData();
                $series = new Series();
                
                $series->setAlias($data['alias'])
                    ->setFinal($data['final'])
                    ->setStage($this->getStage());

                if ($data['first']) {
                    $series->setFirst($data['first']);
                }

                if ($data['second']) {
                    $series->setSecond($data['second']);
                }

                if ($data['firstPrevious']) {
                    $series->setFirstPrevious($data['firstPrevious']);
                }

                if ($data['secondPrevious']) {
                    $series->setSecondPrevious($data['secondPrevious']);
                }

                if ($data['winner']) {
                    $series->setWinner($data['winner']);
                }

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate(
                            "New series was added"
                        )
                    );

                $this->getObjectManager()->persist($series);

                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'index',
                        'id'     => null,
                    ],
                    true
                );
            }
        }

        return [
            'form' => $form,
        ];
    }

    public function editAction()
    {
        /**
         * @var Series $series
         */
        $request = $this->getRequest();
        $seriesR = $this->getObjectManager()->getRepository(Series::class);
        $series  = $this->params('id');
        $form    = $this->seriesForm;

        if (!($series && ($series = $seriesR->find($series)))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data   = $form->getData();

                $series->setAlias($data['alias'])
                    ->setFinal($data['final']);

                if ($data['first']) {
                    $series->setFirst($data['first']);
                }

                if ($data['second']) {
                    $series->setSecond($data['second']);
                }

                if ($data['firstPrevious']) {
                    $series->setFirstPrevious($data['firstPrevious']);
                }

                if ($data['secondPrevious']) {
                    $series->setSecondPrevious($data['secondPrevious']);
                }

                if ($data['winner']) {
                    $series->setWinner($data['winner']);
                }

                $this->flashMessenger()
                    ->addInfoMessage(
                        $this->translate('Changes saved')
                    );

                return $this->redirect()->toRoute(
                    null,
                    [
                        'action' => 'index',
                        'id'     => null,
                    ],
                    true
                );
            }
        } else {
            $form->setData(
                [
                    'first'          => $series->getFirst(),
                    'second'         => $series->getSecond(),
                    'firstPrevious'  => $series->getFirstPrevious(),
                    'secondPrevious' => $series->getSecondPrevious(),
                    'alias'          => $series->getAlias(),
                    'final'          => $series->isFinal(),
                    'winner'         => $series->getWinner(),
                ]
            );
        }

        return [
            'form'          => $form,
            'series'        => $series,
        ];
    }

    public function deleteAction()
    {
        /**
         * @var Series $series
         */
        $seriesR = $this->getObjectManager()->getRepository(Series::class);
        $series  = $this->params('id');

        if (!($series && ($series = $seriesR->find($series)))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        if ($this->getRequest()->getQuery('confirm')) {
            $this->getObjectManager()->remove($series);

            return $this->redirect()->toRoute(
                null,
                [
                    'action' => 'index',
                    'id'     => null,
                ],
                true
            );
        }

        return [
            'series' => $series,
        ];
    }
}