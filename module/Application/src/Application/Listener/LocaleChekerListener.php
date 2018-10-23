<?php 
/**
 * Locale checker fires before dispatch
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Application\Listener;

use Zend\Mvc\Router\RouteMatch;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Mvc\MvcEvent;

class LocaleChekerListener extends AbstractListenerAggregate
{
    /**
     * @var array
     */
    protected $paramName = 'locale';

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 2);
    }

    public function onDispatch(MvcEvent $event)
    {
        $application      = $event->getApplication();
        $services         = $application->getServiceManager();
        $routeMatch       = $event->getRouteMatch();
        $translator       = $services->get('translator');

        // Just making sure that it will work in old versions
        $translatorConfig = $services->get('config');
        $translatorConfig = $translatorConfig['translator'];

        if (!isset($translatorConfig['locales'])) {
            throw new Exception\InvalidConfigException(
                'Translator config didn\'t have a locales key'
            );
        }

        // Locales that exists
        $locales = $translatorConfig['locales'];

        if ($routeMatch instanceof RouteMatch) {
            $routeParams = $routeMatch->getParams();

            if (isset($routeParams[$this->paramName])) {
                if (!isset($locales[$routeParams[$this->paramName]])) {
                    // Locale in route not found
                    // Giving 404 response
                    $event->setError($application::ERROR_EXCEPTION)->setParam(
                        'exception', 
                        new Exception\InvalidLocaleException()
                    );

                    $event->getApplication()
                    ->getEventManager()
                    ->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);

                    return;
                }
            }

            // Everything ok with locale
            if (isset($routeParams[$this->paramName])) {
                $translator->setLocale($routeParams[$this->paramName]);
            }
        }
    }
}