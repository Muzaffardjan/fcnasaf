<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Filter\FriendlyUri;
use Soccer\Entity\Club;
use Soccer\Entity\Season;
use Soccer\Entity\Stage;
use Soccer\Entity\Tournament;
use Soccer\Repository\StageRepository;
use Zend\Config\Config;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\Uri\Http;
use Zend\View\Model\ViewModel;
use Doctrine\ORM\EntityManager;
use Zend\Http\Client\Adapter\Exception\RuntimeException;

class IndexController extends AbstractController
{
    /**
     * @var EntityManager
     */
    protected $objectManager;

    protected $config;

    /**
     * IndexController constructor.
     * @param EntityManager $objectManager
     * @param Config $config
     */
    public function __construct(EntityManager $objectManager, $config)
    {
        $this->objectManager = $objectManager;
        $this->config = $config;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        /**
         * @var StageRepository $stageRepository
         */
        $stageRepository = $this->objectManager->getRepository(Stage::class);

        /**
         * @var array $tableLabel
         */
        $tableLabel = [];

        /**
         * @var array $tableBody
         */
        $tableBody = [];

        /**
         * @var Stage $stage
         */
        foreach ($stageRepository->findAllForTable() as $stage) {
            $currentLocale = $this->locale()->current();

            /**
             * @var array $template
             */
            $template = [
                6 => 1,
                2 => 4,
                3 => 3,
            ];

            switch ($currentLocale) {
                case 'uz-cyrl-uz':
                    $currentLocale = 'uz';
                    break;
                case 'uz-latn-uz':
                    $currentLocale = 'oz';
                    break;
                case 'ru-ru':
                    $currentLocale = 'ru';
                    break;
                case 'en-us':
                    $currentLocale = 'en';
                    break;
            }

            /**
             * @var Season $season
             */
            if ($stage->getParent()) {
                $season = $stage->getParent()->getSeason();
            } else {
                $season = $stage->getSeason();
            }

            /**
             * @var Tournament $tournament
             */
            $tournament = $season->getTournament();

            $tableLabel[] = sprintf(
                '%s %s',
                $tournament->getLabel($this->locale()->current()),
                $season->getLabel($this->locale()->current())
            );

            $caUri = new Http("https://championat.asia/".$currentLocale."/uzb/countries/c");
            $highlight = $this->config->get('home_championship_table')->toArray();

            /**
             * @var array $clubsHlight
             */
            $clubsHlight = [];

            /**
             * @var FriendlyUri $friendlyUri
             */
            $friendlyUri = new FriendlyUri();

            /**
             * @var Club $club
             */
            foreach ($season->getClubs() as $club) {
                if (in_array($club->getId(), $highlight)) {
                    foreach ($club->getName() as $name) {
                        $clubsHlight[] = $friendlyUri->filter($name);
                    }
                }
            }

            $caUri->setQuery([
                'template' => $template[$season->getId()],
                'id' => 1000001
            ]);

            /**
             * @var Request $request
             */
            $request = $this->getRequest();

            /**
             * @var Client $client
             */
            $client = new Client(
                $caUri,
                [
                    'timeout' => 30,
                    'adapter' => Client\Adapter\Curl::class,
                    'curloptions' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_RETURNTRANSFER => 1,
                    ],
                    'verify' => false,
                ]
            );

            $request->setUri($caUri);
            $client->setRequest($request);

            try {
                $chaResponse = $client->send()->getBody();

                /**
                 * @var \Zend\Dom\Query $domQuery
                 */
                $domQuery = new \Zend\Dom\Query($chaResponse);

                /**
                 * @var \DOMElement $result
                 */
                $result = $domQuery->execute('.stats-table')->offsetGet(0);

                /**
                 * @var \DOMDocument $newdoc
                 */
                $newdoc = new \DOMDocument();

                /**
                 * @var \DOMElement $childNode
                 */
                $result->firstChild->firstChild->removeChild($result->firstChild->firstChild->lastChild);
                $result->firstChild->firstChild->firstChild->textContent = '#';

                foreach ($result->firstChild->firstChild->childNodes as $th) {
                    $th->setAttribute('class', 'text-left');
                }

                foreach ($result->lastChild->childNodes as $childNode) {
                    $clubNameTd = $childNode->childNodes->item(1);

                    if (!$clubNameTd->firstChild instanceof \DOMText && $clubNameTd->firstChild->tagName == 'img') {
                        $clubNameTd->removeChild($clubNameTd->firstChild);
                        $clubNameTd->textContent = mb_substr($clubNameTd->textContent, 1);
                    }

                    if (in_array($friendlyUri->filter($clubNameTd->textContent), $clubsHlight)) {
                        $childNode->setAttribute('class', 'info');
                    }

                    if ($childNode->lastChild->firstChild->getAttribute('class') == 'tip') {
                        $childNode->removeChild($childNode->lastChild);
                    }
                }

                $result->setAttribute('class', 'table table-striped');

                $newdoc->appendChild($newdoc->importNode($result,TRUE));
                $tableBody[] = $newdoc->saveHTML();
            } catch (RuntimeException $exception) {
                var_dump(get_class_methods($exception)); exit;
            }
        }

        return new ViewModel([
            'tableLabel' => $tableLabel,
            'tableBody' => $tableBody,
        ]);
    }

    public function setLocaleAction()
    {
        return $this->redirect()->toRoute(
            'app/home',
            ['locale' => $this->locale()->current()]
        );
    }

    public function teamAction()
    {
        return [];
    }

    public function matchReportAction()
    {
        //throw new \Exception('Unknown exception occured');
        return [];
    }

    public function leagueAction()
    {
        //throw new \Exception('Unknown exception occured');
        return [];
    }
}
