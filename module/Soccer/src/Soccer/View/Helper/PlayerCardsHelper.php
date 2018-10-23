<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\View\Helper;

use Admin\WebsiteConfig\TemplatePositionsConfig;
use Application\ObjectManagerAwareInterface;
use Application\ObjectManagerAwareTrait;
use Soccer\Entity\Club;
use Soccer\Entity\Player;
use Zend\View\Helper\AbstractHelper;

/**
 * PlayerCards
 */
class PlayerCardsHelper extends AbstractHelper implements ObjectManagerAwareInterface
{
    use ObjectManagerAwareTrait;

    /**
     * @var TemplatePositionsConfig
     */
    protected $templatePositionsConfig;

    /**
     * @var string
     */
    protected $partial;

    /**
     * PlayerCards constructor.
     *
     * @param TemplatePositionsConfig $templatePositionsConfig
     */
    public function __construct(TemplatePositionsConfig $templatePositionsConfig)
    {
        $this->templatePositionsConfig = $templatePositionsConfig;
    }

    /**
     * @return TemplatePositionsConfig
     */
    public function getTemplatePositionsConfig()
    {
        return $this->templatePositionsConfig;
    }

    /**
     * @param TemplatePositionsConfig $templatePositionsConfig
     * @return self
     */
    public function setTemplatePositionsConfig(TemplatePositionsConfig $templatePositionsConfig)
    {
        $this->templatePositionsConfig = $templatePositionsConfig;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartial()
    {
        return $this->partial;
    }

    /**
     * @param string $partial
     * @return self
     */
    public function setPartial($partial)
    {
        $this->partial = $partial;

        return $this;
    }

    public function __invoke($partial = null)
    {
        if (null !== $partial) {
            $this->setPartial($partial);
        }

        return $this->render();
    }

    public function render()
    {
        return $this->getView()->partial(
            $this->getPartial(),
            [
                'players' => $this->getObjectManager()
                    ->getRepository(Player::class)
                    ->findPlayersWithImageCards($this->getTemplatePositionsConfig()->get()->offsetGet('player_cards')),
                'club'    => $this->getTemplatePositionsConfig()->get()->offsetGet('player_cards'),
            ]
        );
    }
}