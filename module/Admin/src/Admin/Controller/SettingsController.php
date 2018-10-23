<?php
/**
 * SettingsController
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version     1.0.0
 */
namespace Admin\Controller;

use Admin\Form\HomeSoccerCupForm;
use Admin\Form\PlayerCardsConfigForm;
use Admin\Form\StaffGroupConfigForm;
use Admin\WebsiteConfig\WebsiteConfig;
use Admin\WebsiteConfig\TemplatePositionsConfig;
use Admin\Form\MenuConfigForm;
use ImagesManager\Controller\ManagerController;

class SettingsController extends AbstractObjectManagerAwareController
{
    /**
     * @var WebsiteConfig
     */
    protected $configWebsite;

    /**
     * @var TemplatePositionsConfig
     */
    protected $configTemplate;

    /**
     * @var MenuConfigForm
     */
    protected $formMenuConfig;

    /**
     * @var StaffGroupConfigForm
     */
    protected $formStaffGroupConfig;

    /**
     * @var PlayerCardsConfigForm
     */
    protected $playerCardsConfigForm;

    /**
     * @var HomeSoccerCupForm
     */
    protected $homeSoccerCupForm;

    /**
     * SettingsController constructor.
     *
     * @param WebsiteConfig           $configWebsite
     * @param TemplatePositionsConfig $configTemplate
     * @param MenuConfigForm          $formMenuConfig
     * @param StaffGroupConfigForm    $formStaffGroupConfig
     * @param PlayerCardsConfigForm   $playerCardsConfigForm
     * @param HomeSoccerCupForm       $homeSoccerCupForm
     */
    public function __construct(
        WebsiteConfig $configWebsite,
        TemplatePositionsConfig $configTemplate,
        MenuConfigForm $formMenuConfig,
        StaffGroupConfigForm $formStaffGroupConfig,
        PlayerCardsConfigForm $playerCardsConfigForm,
        HomeSoccerCupForm $homeSoccerCupForm
    ) {
        $this->configWebsite = $configWebsite;
        $this->configTemplate = $configTemplate;
        $this->formMenuConfig = $formMenuConfig;
        $this->formStaffGroupConfig = $formStaffGroupConfig;
        $this->playerCardsConfigForm = $playerCardsConfigForm;
        $this->homeSoccerCupForm = $homeSoccerCupForm;
    }

    /**
     * @return array|\Zend\Http\Response
     */
    public function menuAction()
    {
        $form = $this->formMenuConfig;

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost()->toArray());

            if ($form->isValid()) {
                $data = $form->getData();
                $navs = $this->configTemplate->get('navigations');

                foreach ($data as $name => $value) {
                    $menu = $navs->offsetGet($name)
                    ->offsetGet('menu');

                    if (is_array($value)) {
                        foreach ($value as $key => $item) {
                            $menu->offsetSet($key, $item ? (int) $item : null);
                        }
                    }
                }

                $this->configTemplate->save();

                $this->flashMessenger()
                ->addSuccessMessage(
                    $this->translate('Saved')
                );

                return $this->redirect()->refresh();
            }
        }

        return [
            'form' => $form,
        ];  
    }

    public function staffGroupAction()
    {
        $form       = $this->formStaffGroupConfig;
        $request    = $this->getRequest();
        $config     = $this->configTemplate->get();

        if (!$config->offsetExists('staff_group')) {
            $config->offsetSet('staff_group', []);
        }

        $staffGroupConfig = $config->offsetGet('staff_group');

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data   = $form->getData();

                if (isset($data['home_page']) && $data['home_page']) {
                    $staffGroupConfig->offsetSet(
                        'home_page',
                        array_merge(
                            $staffGroupConfig->offsetExists('home_page') ?
                                $staffGroupConfig->offsetGet('home_page')->toArray() :
                                [],
                            $data['home_page']
                        )
                    );
                }

                $this->configTemplate->save();

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate('Saved')
                    );

                return $this->redirect()->refresh();
            }
        } else {
            $data = [];

            if ($staffGroupConfig->offsetExists('home_page')) {
                $data['home_page'] = $staffGroupConfig->offsetGet('home_page')->toArray();
            }

            $form->setData($data);
        }

        return [
            'form' => $form,
        ];
    }

    public function playerCardsAction()
    {
        $form       = $this->playerCardsConfigForm;
        $request    = $this->getRequest();
        $config     = $this->configTemplate->get();

        if (!$config->offsetExists('player_cards')) {
            $config->offsetSet('player_cards', null);
        }

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data   = $form->getData();

                $config->offsetSet('player_cards', $data['club']);

                $this->configTemplate->save();

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate('Saved')
                    );

                return $this->redirect()->refresh();
            }
        } else {
            $data = [];

            if ($config->offsetExists('player_cards')) {
                $data['club'] = $config->offsetGet('player_cards');
            }

            $form->setData($data);
        }

        return [
            'form' => $form,
        ];
    }

    public function soccerCupAction()
    {
        $form       = $this->homeSoccerCupForm;
        $request    = $this->getRequest();
        $config     = $this->configTemplate->get();

        if (!$config->offsetExists('home_soccer_cup')) {
            $config->offsetSet('home_soccer_cup', null);
        }

        if ($request->getQuery('delete')) {
            $config->offsetSet('home_soccer_cup', null);
            $this->configTemplate->save();
            return $this->redirect()->refresh();
        }

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data   = $form->getData();

                $config->offsetSet('home_soccer_cup', $data['cup']);

                $this->configTemplate->save();

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate('Saved')
                    );

                return $this->redirect()->refresh();
            }
        } else {
            $data = [];

            if ($config->offsetExists('home_soccer_cup')) {
                $data['cup'] = $config->offsetGet('home_soccer_cup');
            }

            $form->setData($data);
        }

        return [
            'form' => $form,
        ];
    }

    public function homeChampionshipBackgroundConfigAction()
    {
        $config = $this->configTemplate->get();

        if ($this->getRequest()->getQuery('chi')) {
            if ($this->apiCall()->isCancelled() || $this->apiCall()->isReturned()) {
                if ($this->apiCall()->isReturned()) {
                    $result = $this->apiCall()->getResult();

                    if (isset($result['images'])) {
                        $images = [];

                        foreach ($result['images'] as $image) {
                            $images[] = $image['href'];
                        }

                        $config->offsetSet(
                            'home_championship_background',
                            $images
                        );

                        $this->configTemplate->save();
                    }
                }

                return $this->redirect()->toRoute(null, [], ['query' => []], true);
            }

            return $this->apiCall()
                ->call(
                    $this->url()
                    ->fromRoute(
                        'app/admin/images-manager',
                        [
                            'action' => 'index',
                        ],
                        true
                    ),
                    [
                        'mode' => ManagerController::LISTER_MODE_SELECT_IMAGES,
                    ]
                );
        }

        return [
            'config' => $config,
        ];
    }

    public function championshipTablesAction()
    {
        $form       = $this->playerCardsConfigForm;
        $request    = $this->getRequest();
        $config     = $this->configTemplate->get();

        $form->get('club')->setAttribute('multiple', true)
            ->setLabel($this->translate('Highlight clubs below'));

        if (!$config->offsetExists('home_championship_table')) {
            $config->offsetSet('home_championship_table', null);
        }

        if ($request->getQuery('delete')) {
            $config->offsetSet('home_championship_table', null);
            $this->configTemplate->save();
            return $this->redirect()->refresh();
        }

        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data   = $form->getData();

                $config->offsetSet('home_championship_table', $data['club']);

                $this->configTemplate->save();

                $this->flashMessenger()
                    ->addSuccessMessage(
                        $this->translate('Saved')
                    );

                return $this->redirect()->refresh();
            }
        } else {
            $data = [];

            if ($config->offsetExists('home_championship_table')) {
                $data['club'] = $config->offsetGet('home_championship_table');
            }

            $form->setData($data);
        }

        return [
            'form' => $form,
        ];
    }

    public function telegramAction()
    {
        /**
         * @todo this
         */
        exit;
    }
}