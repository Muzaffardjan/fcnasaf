<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2017 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\View\Helper;

use Application\ObjectManagerAwareInterface;
use Application\ObjectManagerAwareTrait;
use Soccer\Entity\Club;
use Soccer\Entity\Season;
use Soccer\Entity\Series;
use Soccer\Entity\Stage;
use Soccer\Exception\InvalidArgumentException;
use Soccer\Exception\RuntimeException;
use Soccer\Repository\SeriesRepository;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Renderer\PhpRenderer;

/**
 * SoccerCupBracket
 */
class SoccerCupBracket extends AbstractHelper implements ObjectManagerAwareInterface
{
    use ObjectManagerAwareTrait;

    // categories
    const CATEGORY_UNKNOWN  = 'unknown';
    const CATEGORY_CHAMPION = 'champion';

    // directions
    const DIRECTION_LEFT    = 'left';
    const DIRECTION_RIGHT   = 'right';

    const KEY_CHAMPION = 'champion';

    /**
     * @var Stage
     */
    protected $stage;

    /**
     * @var string
     */
    protected $width  = '100%';

    /**
     * @var string
     */
    protected $height = '500px';

    /**
     * @var array
     */
    protected $options = [];

    public function render($id = 'play-off-bracket')
    {
        /**
         * @var PhpRenderer $renderer
         */
        $renderer = $this->getView();

        $renderer->inlineScript()
            ->appendFile(
                $renderer->basePath('js/go.js')
            )
            ->appendFile(
                $renderer->basePath('js/cup-bracket.js')
            )
            ->appendScript(
                sprintf(
                    "
                        DrawSoccerBracket(
                            // data
                            %s,
                            // element
                            '%s',
                            // texts
                            %s,
                            // options
                            %s
                        );
                    ",
                    $this->getView()->json($this->getDataModel()),
                    $id,
                    $this->getView()->json(
                        [
                            'semiFinal'     => mb_strtoupper($renderer->translate('Semifinal')),
                            'quarterFinal'  => mb_strtoupper($renderer->translate('Quarterfinal')),
                            'mainTitle'     => mb_strtoupper(
                                sprintf(
                                    "%s %s %s",
                                    $this->getStage()->getSeason()->getTournament()->getLabel($this->getView()->locale()->current()),
                                    $this->getStage()->getSeason()->getLabel($this->getView()->locale()->current()),
                                    $this->getStage()->getSeason()->getType() == Season::TYPE_CHAMPIONSHIP ? $this->getStage()->getLabel($this->getView()->locale()->current()) : ''
                                )
                            )
                        ]
                    ),
                    $this->getView()->json($this->getOptions())
                )
            );

        return sprintf(
            '<div id="%s" style="width: %s; height: %s;"></div>',
            $id,
            $this->getWidth(),
            $this->getHeight()
        );
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
     * @return static
     * @throws RuntimeException
     */
    public function setStage($stage)
    {
        $stageR = $this->getObjectManager()->getRepository(Stage::class);

        if (!($stage instanceof Stage)) {
            if (!is_int($stage) && !($stage = $stageR->find($stage))) {
                throw new RuntimeException("Invalid soccer cup configuration given");
            }
        }

        if ($stage->getType() != Stage::TYPE_PLAY_OFF) {
            throw new InvalidArgumentException(
                sprintf(
                    "Stage type must be %s, stage with %s type given.",
                    $stage->getType()
                )
            );
        }

        $this->stage = $stage;

        return $this;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param string $width
     * @return self
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param string $height
     * @return self
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $defaults = [
            'images' => [
                'ball' => $this->getView()->basePath('img/ball6464.png'),
                'cup'  => $this->getView()->basePath('img/cup.png'),
            ],
        ];

        return array_merge($defaults, $this->options);
    }

    /**
     * @param array $options
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }

        return null;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return self
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    private function getDataModel()
    {
        /**
         * @var SeriesRepository $seriesR
         */
        $stage   = $this->getStage();

        $seriesR = $this->getObjectManager()->getRepository(Series::class);
        $final   = $seriesR->findFinalSeriesOf($stage);
        $seriesCollection  = $seriesR->findAllSeriesOf($stage);
        $result  = [];

        $champion = [
            'category' => self::CATEGORY_CHAMPION,
            'key'      => self::KEY_CHAMPION
        ];

        if ($final) {
            if ($final->getWinner()) {
                $champion['club'] = $final->getWinner()->getName(
                    $this->getView()->locale()->current()
                );
            }
        }

        $result[] = $champion;

        /**
         * @var Series $serie
         */
        foreach ($seriesCollection as $series) {
            $nodes = $this->convertToNodes($series);

            foreach ($nodes as $node) {
                $result[] = $node;
            }
        }

        //echo '<pre>'.$this->getView()->json($result, ['prettyPrint' => true]).'</pre>';exit;

        return $result;
    }

    private function convertToNodes(Series $series)
    {
        $result      = [];

        $node['queue'] = 1;
        if ($series->getFirst()) {
            $node = [
                'clubId'   => $series->getFirst()->getId(),
                'key'      => $series->getId() . 'first',
                'club'     => $series->getFirst()->getName(
                    $this->getView()->locale()->current()
                ),
                'logo'     => $this->getView()->basePath(
                    $series->getFirst()->getSmallLogo()
                ),
                'queue' => 1,
            ];
        } else {
            $node['key']        = $series->getId() . 'first';
            $node['category']   = self::CATEGORY_UNKNOWN;
        }

        $result[] = $node;
        unset($node);

        $node['queue'] = 2;
        if ($series->getSecond()) {
            $node = [
                'clubId'   => $series->getSecond()->getId(),
                'key'      => $series->getId() . 'second',
                'club'     => $series->getSecond()->getName(
                    $this->getView()->locale()->current()
                ),
                'logo'     => $this->getView()->basePath(
                    $series->getSecond()->getSmallLogo()
                ),
                'queue' => 2,
            ];
        } else {
            $node['key'] = $series->getId() . 'second';
            $node['category']   = self::CATEGORY_UNKNOWN;
        }

        $result[] = $node;
        unset($node);

        foreach ($result as $key => $node) {
            if ($series->getNext()) {

                $node['parent']  = $series->getNext()->getId();
                $node['parent'] .= $series->getOrder() == 1 ? 'first' : 'second';
            } else {
                $node['parent'] = self::KEY_CHAMPION;
            }

            if ($series->isFinal()) {
                $node['dir'] = ($node['queue'] == 1 ? self::DIRECTION_RIGHT : self::DIRECTION_LEFT);
            }

            $result[$key] = $node;
        }

        return $result;
    }
}