<?php

namespace AppBundle\Entity;

/**
 * Alarm entity
 */
class Alarm
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $id_task;

    /**
     * @var int
     */
    private $id_account;

    /**
     * @var \DateTime
     */
    private $ts_trigger;

    /**
     * @var int
     */
    private $repeat;

    /**
     * @var \DateInterval
     */
    private $duration;

    /**
     * Get note identifier
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get task identifier
     *
     * @return int
     */
    public function getTaskId() : int
    {
        return $this->id_task;
    }

    /**
     * Get account identifier
     *
     * @return int
     */
    public function getAccountId() : int
    {
        return $this->id_account ?? 0;
    }

    /**
     * Get alarm trigger date
     *
     * @return \DateTimeInterface
     */
    public function triggersAt() : \DateTimeInterface
    {
        return $this->ts_trigger;
    }

    /**
     * Get repeat count
     *
     * @return int
     */
    public function getRepeat() : int
    {
        return $this->repeat;
    }

    /**
     * Has this task a duration
     *
     * @return bool
     */
    public function hasDuration() : bool
    {
        return isset($this->duration);
    }

    /**
     * Get duration
     *
     * @return \DateInterval
     */
    public function getDuration()
    {
        return $this->duration;
    }
}
