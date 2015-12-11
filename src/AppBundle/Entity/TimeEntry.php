<?php

namespace AppBundle\Entity;

use AppBundle\Annotation\ReportDimension;
use AppBundle\Annotation\ReportFilter;
use Doctrine\ORM\Mapping as ORM;

/**
 * TimeEntry
 *
 * @ORM\Table(name="timeEntries")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TimeEntryRepository")
 *
 * @ReportDimension(name="timeEntry")
 */
class TimeEntry extends BaseTimeEntry
{
    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     *
     * @ReportFilter(name = "Project")
     */
    protected $project;

    /**
     * @var Task
     *
     * @ORM\ManyToOne(targetEntity="Task")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     *
     * @ReportFilter(name = "Task")
     */
    protected $task;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->dateTimeRange . ' ' . $this->project . ' ' . $this->task . ' ' . $this->note;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param Project|null $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param Task|null $task
     */
    public function setTask($task)
    {
        $this->task = $task;
    }
}
