<?php

namespace AppBundle\Entity;

use AppBundle\Annotation\ReportFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BaseTimeEntry
 *
 * @ORM\MappedSuperclass
 */
class BaseTimeEntry
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @ReportFilter(name = "ID")
     */
    protected $id;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     *
     * @ReportFilter(name = "Team")
     */
    protected $team;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @ReportFilter(name = "User")
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255)
     *
     * @Assert\NotNull()
     * @Assert\Length(max = 255)
     *
     * @ReportFilter(name = "Note")
     */
    protected $note;

    /**
     * @var DateTimeRange
     *
     * @ORM\Embedded(class = "DateTimeRange", columnPrefix=false)
     *
     * @ReportFilter(name = "Date Range", type="AppBundle\Entity\DateTimeRange")
     */
    protected $dateTimeRange;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->dateTimeRange . ' ' . $this->note;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Team $team
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return DateTimeRange
     */
    public function getDateTimeRange()
    {
        return $this->dateTimeRange;
    }

    /**
     * @param DateTimeRange $dateTimeRange
     */
    public function setDateTimeRange(DateTimeRange $dateTimeRange)
    {
        $this->dateTimeRange = $dateTimeRange;
    }
}
