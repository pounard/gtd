<?php

namespace AppBundle\Entity;

/**
 * Note entity
 */
class Note
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
     * @var string
     */
    private $description;

    /**
     * @var \DateTimeInterface
     */
    private $ts_added;

    /**
     * @var \DateTimeInterface
     */
    private $ts_updated;

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
        return $this->id_account;
    }

    /**
     * Get description (note contents)
     *
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * Get creation date
     *
     * @return \DateTimeInterface
     */
    public function addedAt() : \DateTimeInterface
    {
        return $this->ts_added;
    }

    /**
     * Get latested modification date
     *
     * @return \DateTimeInterface
     */
    public function updatedAt() : \DateTimeInterface
    {
        return $this->ts_updated;
    }
}
