<?php
/**
 * @author    Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.each.uz)
 * @license   FOR EACH SOFT LTD. PUBLIC LICENSE.
 * @version   1.0.0
 */
namespace Soccer\Controller;

use Application\Controller\AbstractObjectManagerAwareController;
use Soccer\Entity\Match;

class MatchController extends AbstractObjectManagerAwareController
{
    public function indexAction()
    {
        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function reportAction()
    {
        $matchesR = $this->getObjectManager()->getRepository(Match::class);
        $match    = $this->params('id');

        /**
         * @var Match $match
         */
        if (!($match && ($match = $matchesR->find($match)))) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return [
            'match' => $match,
        ];
    }
}