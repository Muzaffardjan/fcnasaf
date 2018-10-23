<?php 
/**
 * Api call plugin for controllers to exchange data
 * 
 * @author Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version 1.0.0
 */
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Http\PhpEnvironment\Request;
use Zend\Session\Container as SessionContainer;
use Zend\Uri\UriFactory;
use Zend\Uri\Uri;

class ApiCall extends AbstractPlugin
{
    const STATE_NULL     = 0;
    const STATE_CALLED   = 1;
    const STATE_RETURNED = 2;
    const STATE_CANCELLED= -2;
    const STATE_END      = -1;

    const SIDE_UNKNOWN = 'unknown';
    const SIDE_CALLER  = 'caller';
    const SIDE_TARGET  = 'target';

    /**
     * @var string $sessionContainerName
     */
    private $sessionContainerName = 'ApiCall';

    /**
     * @var SessionContainer $session
     */
    protected $session;

    /**
     * Plugin state 
     *
     * @var int $state
     */
    protected $state;

    /**
     * Defines current controller's side is it caller or target 
     *
     * @var mixed   $side
     */
    protected $side;

    /**
     * @var Request $request
     */
    protected $request;

    /**
     * Construct
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->createSession();

        /**
         * if data exchange has started than define controller's side
         */
        if ($this->getState() == self::STATE_RETURNED) {
            $uri = [
                'current' => $request->getUri()->toString(),
                'caller'  => $this->getCallerUri()->toString(),
                'target'  => $this->getTargetUri()->toString(),
            ];

            if ($uri['current'] === $uri['caller']) {
                $this->side = self::SIDE_CALLER;
            } elseif ($uri['current'] === $uri['target']) {
                $this->side = self::SIDE_TARGET;
            } else {
                $this->side = self::SIDE_UNKNOWN;
            }
        } else {
            $this->side = self::SIDE_UNKNOWN;
        }
    }

    /**
     * Is app called
     *
     * @return bool
     */
    public function isCalled()
    {
        return $this->getState() === self::STATE_CALLED;
    }

    /**
     * Is api cancelled
     *
     * @return bool
     */
    public function isCancelled()
    {
        $flag = $this->getState() === self::STATE_CANCELLED;

        if ($flag) {
            $this->reset();
        }

        return $flag;
    }

    /**
     * Is returned
     *
     * @return bool
     */
    public function isReturned()
    {
        return $this->getState() === self::STATE_RETURNED;
    }

    /**
     * Is current controller caller side
     *
     * @return bool 
     */
    public function isCaller()
    {
        return $this->side === self::SIDE_CALLER;
    }

    /**
     * Is current controller target side
     *
     * @return bool 
     */
    public function isTarget()
    {
        return $this->side === self::SIDE_TARGET;
    }

    /**
     * Sets plugin state
     *
     * @param int $state
     */
    protected function setState($state)
    {
        $this->getSession()->offsetSet('_state', (int) $state);
    }

    /**
     * Gets state of plugin
     *
     * @return int 
     */
    public function getState()
    {
        if (!$this->getSession()->offsetExists('_state')) {
            return self::STATE_NULL;
        }

        return $this->getSession()->offsetGet('_state');
    }

    /**
     * Calls the app
     *
     * @param string $uri
     * @param mixed $param
     * @return Zend\Http\PhpEnvironment\Response
     */
    public function call($uri, $param)
    {
        // Change state
        $this->setState(self::STATE_CALLED);
        // remember caller
        $this->setCallerUri();
        $this->setTargetUrl($uri);
        $this->setParams($param);

        // Redirect to target
        return $this->getController()->redirect()->toUrl($uri);
    }

    /**
     * Redirects to caller side with given result data
     *
     * @param   mixed   $data
     * @return  Zend\Http\PhpEnvironment\Response
     */
    public function result($data = null, $redirect = true)
    {
        // Change state
        $this->setState(self::STATE_RETURNED);

        if (null !== $data) {
            $this->setResult($data);
        }

        if (!$redirect) {
            return $this->getCallerUri()->toString();
        }
        // Redirect to caller
        return $this->getController()->redirect()->toUrl(
            $this->getCallerUri()->toString()
        );
    }

    /**
     * Stops operation
     */
    public function cancel()
    {
        $this->reset();
        $this->setState(self::STATE_CANCELLED);
    }

    /**
     * Sets caller uri
     *
     * @return void
     */
    protected function setCallerUri()
    {
        // Write caller uri
        $this->getSession()->offsetSet(
            '_caller', 
            $this->request->getUri()
        );
    }

    /**
     * Gets caller uri
     *
     * @throws Exception\InvalidOperationStateException
     * @return Zend\Uri\Uri;
     */
    public function getCallerUri()
    {
        if ($this->getState() < self::STATE_CALLED) {
            throw new Exception\InvalidOperationStateException();
        }

        return $this->getSession()->offsetGet('_caller');
    }

    /**
     * Sets target app url
     *
     * @throws Exception\InvalidArgumentException
     * @param string $url
     */
    protected function setTargetUrl($url)
    {
        if (!is_string($url)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Url must be string \'%s\' given.', gettype($url))
            );
        }

        $this->getSession()->offsetSet(
            '_target', 
            UriFactory::factory($url)
        );
    }

    /**
     * Gets target app url
     *
     * @throws Exception\InvalidOperationStateException
     * @return string
     */
    public function getTargetUri()
    {
        if ($this->getState() < self::STATE_CALLED) {
            throw new Exception\InvalidOperationStateException();
        }

        return $this->getSession()->offsetGet('_target');
    }

    /**
     * Sets params
     *
     * @param array $params
     */
    protected function setParams(array $params)
    {  
        $this->getSession()->offsetSet('_params', $params);
    }

    /**
     * Adds parameter to call
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  ApiCall
     */
    public function addParam($name, $value)
    {
        $old        = $this->getParams();
        $old[$name] = $value;

        $this->setParams($old);

        return $this;
    }

    /**
     * Gets params 
     *
     * @throws Exception\InvalidOperationStateException
     * @return array
     */
    public function getParams($key = null)
    {
        if ($this->getState() < self::STATE_CALLED) {
            throw new Exception\InvalidOperationStateException();
        }

        $data = $this->getSession()->offsetGet('_params');

        if ($key !== null) {
            if (isset($data[$key])) {
                return $data[$key];
            }

            return null;
        }

        return $data;
    }

    /**
     * Sets result
     *
     * @param array $data
     */
    public function setResult(array $data) 
    {
        $this->getSession()->offsetSet('_result', $data);
    }

    /**
     * Adds new data to result
     *
     * @param   array   $data
     * @return  ApiCall 
     */
    public function addResultData($data)
    {
        $old    = $this->getSession()->offsetGet('_result');

        if (is_array($old)) {
            $result = array_merge_recursive($old, $data);
        } else {
            $result = $data;
        }
        
        // Set resulted array
        $this->setResult($result);

        return $this;
    }

    /**
     * Gets result 
     *
     * @throws Exception\InvalidOperationStateException
     * @return array
     */
    public function getResult()
    {
        if ($this->getState() < self::STATE_RETURNED) {
            throw new Exception\InvalidOperationStateException();
        }

        $data = $this->getSession()->offsetGet('_result');

        // Data exchange ended we can reset plugin
        $this->reset();

        return $data;
    }

    /**
     * Creates session container
     *
     * @return void
     */
    protected function createSession()
    {
        $this->session = new SessionContainer($this->sessionContainerName);
    }

    /**
     * Gets session container
     *
     * @return SessionContainer
     */
    protected function getSession()
    {
        return $this->session;
    }

    /**
     * Resets plugin
     */
    protected function reset()
    {
        // Clear session container
        $this->getSession()->getManager()
        ->getStorage()
        ->clear($this->sessionContainerName);
    }
}