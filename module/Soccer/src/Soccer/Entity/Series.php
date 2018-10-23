<?php
/**
 * FC Nasaf official website
 *
 * @author      Kakhramonov Javlonbek <kakjavlon@gmail.com>
 * @copyright   Copyright (c) 2015-2016 Foreach.Soft Ltd. (http://www.foreach.uz)
 * @license     FOR EACH SOFT LTD. PUBLIC LICENSE.
 */
namespace Soccer\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Soccer\Exception\BadMethodCallException;

/**
 * Series
 *
 * @ORM\Entity(repositoryClass="Soccer\Repository\SeriesRepository")
 * @ORM\Table(name="soccer_tournament_play_off_series")
 */
class Series
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var int
     * @ORM\Column(type="smallint", nullable=true, name="`order`")
     */
    protected $order;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, options={"default" : "none"})
     */
    protected $alias;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $final;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $winner;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $first;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="Club")
     */
    protected $second;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Series", mappedBy="next")
     * @ORM\OrderBy({"order" = "ASC"})
     */
    protected $previous;

    /**
     * @var Series
     * @ORM\ManyToOne(targetEntity="Series", inversedBy="previous", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $next;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Match", mappedBy="series")
     * @ORM\OrderBy({"date" = "ASC"})
     */
    protected $matches;

    /**
     * @var Stage
     * @ORM\ManyToOne(targetEntity="Stage", inversedBy="series")
     */
    protected $stage;

    /**
     * Series constructor.
     */
    public function __construct()
    {
        $this->matches  = new ArrayCollection();
        $this->previous = new ArrayCollection();
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments = [])
    {
        if (strlen($name) > 5 && strtolower(substr($name, 3, 5)) == 'label') {
            $locale = str_replace('_', '-', substr($name, 8));

            return $this->getLabel($locale);
        }

        throw new BadMethodCallException(
            sprintf(
                'Method with name %s does not exists in %s class',
                $name,
                self::class
            )
        );
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return self
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFinal()
    {
        return $this->final;
    }

    /**
     * @param bool $final
     * @return self
     */
    public function setFinal($final)
    {
        $this->final = $final;

        return $this;
    }

    /**
     * @return Club
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * @param  null|string
     * @return null|string
     */
    public function getLabel($ns = null)
    {
        if ($ns && $this->getStage()) {
            return $this->getStage()->getLabel($ns) . ' ' . $this->getAlias();
        }

        return null;
    }

    /**
     * @param Club $first
     * @return self
     */
    public function setFirst(Club $first)
    {
        $this->first = $first;

        return $this;
    }

    /**
     * @return Club
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * @param Club $second
     * @return self
     */
    public function setSecond(Club $second)
    {
        $this->second = $second;

        return $this;
    }

    /**
     * @return Series
     */
    public function getFirstPrevious()
    {
        if ($this->getPrevious()) {
            /**
             * @var Series $prev
             */
            foreach ($this->getPrevious() as $prev) {
                if ($prev->getOrder() == 1) {
                    return $prev;
                }
            }
        }

        return null;
    }

    /**
     * @param Series $firstPrevious
     * @return self
     */
    public function setFirstPrevious(Series $firstPrevious)
    {
        $firstPrevious->setOrder(1);

        if ($this->getPrevious()) {
            $criteria = Criteria::create();
            $expr     = $criteria->expr();

            $matching = $this->getPrevious()->matching(
                $criteria->where(
                    $expr->eq('order', 1)
                )
            );

            if ($matching->count()) {
                /**
                 * @var Series
                 */
                $matching = $matching->first();

                if ($matching->getId() != $firstPrevious->getId()) {
                    $this->getPrevious()->removeElement($matching);
                    $matching->setOrder(null);
                }
            }
        }

        $this->getPrevious()->add($firstPrevious);
        $firstPrevious->setNext($this);

        return $this;
    }

    /**
     * @return Series
     */
    public function getSecondPrevious()
    {
        if ($this->getPrevious()) {
            /**
             * @var Series $prev
             */
            foreach ($this->getPrevious() as $prev) {
                if ($prev->getOrder() == 2) {
                    return $prev;
                }
            }
        }

        return null;
    }

    /**
     * @param Series $secondPrevious
     * @return self
     */
    public function setSecondPrevious(Series $secondPrevious)
    {
        $secondPrevious->setOrder(2);

        if ($this->getPrevious()) {
            $criteria = Criteria::create();
            $expr     = $criteria->expr();
            $matching = $this->getPrevious()->matching(
                $criteria->where(
                    $expr->eq('order', 2)
                )
            );

            if ($matching->count()) {
                /**
                 * @var Series
                 */
                $matching = $matching->first();

                if ($matching->getId() != $secondPrevious->getId()) {
                    $this->getPrevious()->removeElement($matching);
                    $matching->setOrder(null);
                }
            }
        }

        $this->getPrevious()->add($secondPrevious);
        $secondPrevious->setNext($this);

        return $this;
    }

    /**
     * @return Club
     */
    public function getWinner()
    {
        return $this->winner;
    }

    /**
     * @param Club $winner
     * @return self
     */
    public function setWinner(Club $winner)
    {
        $this->winner = $winner;

        if ($this->getNext() && $this->getOrder()) {
            if ($this->getOrder() == 1) {
                $this->getNext()->setFirst($winner);
            } elseif ($this->getOrder() == 2) {
                $this->getNext()->setSecond($winner);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * @param ArrayCollection $matches
     * @return self
     */
    public function setMatches(ArrayCollection $matches)
    {
        $this->matches = $matches;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @param ArrayCollection $previous
     * @return self
     */
    public function setPrevious(ArrayCollection $previous)
    {
        $this->previous = $previous;

        return $this;
    }

    /**
     * @return Series
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param Series $next
     * @return self
     */
    public function setNext(Series $next)
    {
        $this->next = $next;

        return $this;
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
}