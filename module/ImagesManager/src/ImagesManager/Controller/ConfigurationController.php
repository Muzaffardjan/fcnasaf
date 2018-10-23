<?php 
/**
 * Configuration controller of images
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace ImagesManager\Controller;

use Admin\Controller\AbstractController;
use ImagesManager\Form\Configuration as ConfigurationForm;
use ImagesManager\Configuration\Config as ImagesManagerConfig;

class ConfigurationController extends AbstractController
{
    /**
     * Array of forms that will be used for this controller 
     *
     * @var array $forms
     */
    protected $forms = [];

    /**
     * Array of configs that will be used for this controller
     *
     * @var array $configs
     */
    protected $configs = [];

    public function __construct(
        ConfigurationForm $formConfig, 
        ImagesManagerConfig $managerConfig, 
        array $moduleConfig
    ) {
        $this->forms[ConfigurationForm::class] = $formConfig;
        $this->configs[ImagesManagerConfig::class] = $managerConfig;
        $this->configs['module'] = $moduleConfig;
    }

    public function indexAction()
    {       
        $form          = $this->forms[ConfigurationForm::class];
        $request       = $this->getRequest();
        $managerConfig = $this->configs[ImagesManagerConfig::class];

        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);

            if ($form->isValid()) {
                $data = $form->getData();

                if (is_file($this->configs['module']['config']['watermark_path'])) {
                    unset($data['watermark']);

                    foreach ($data as $key => $value) {
                        $managerConfig->{$key} = $value;
                    }

                    return $this->redirect()->toRoute(
                        null, 
                        [
                            'locale' => $this->locale()->current()
                        ]
                    );
                } elseif ($data['watermark']) {
                    $form->get('watermark')->setMessages(
                        [
                            $this->translate('You haven\'t upload any watermark yet!')
                        ]
                    );
                }
            }
        } else if ($configArray = $managerConfig->toArray()) {
            $form->setData($configArray);
        }
        
        return [
            'form' => $form
        ];
    }
}