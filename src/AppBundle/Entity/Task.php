<?php

namespace AppBundle\Entity;

/**
 * Task entity
 */
class Task
{
    /**
     * @var bool
     */
    private $is_done = '';

    /**
     * @var bool
     */
    private $is_starred = '';

    /**
     * @var bool
     */
    private $is_hidden = '';

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description = '';

    /**
     * @var int
     */
    private $priority = 0;

    /**
     * @var \DateTimeInterface
     */
    private $ts_added;

    /**
     * @var \DateTimeInterface
     */
    private $ts_updated;

    /**
     * @var \DateTimeInterface
     */
    private $ts_done;

    /**
     * @var \DateTimeInterface
     */
    private $ts_deadline;

    /**
     * @var \DateTimeInterface
     */
    private $ts_unhide;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $id_account;

    /**
     * @var int
     */
    private $note_count = 0;

    /**
     * Is task done
     *
     * @return bool
     */
    public function isDone() : bool
    {
        return $this->is_done;
    }

    /**
     * Is task starred
     *
     * @return bool
     */
    public function isStarred() : bool
    {
        return $this->is_starred;
    }

    /**
     * Is task hidden
     *
     * @return bool
     */
    public function isHidden() : bool
    {
        return $this->is_hidden;
    }

    /**
     * Is task visible
     *
     * @return bool
     */
    public function isVisible() : bool
    {
        return !$this->is_hidden;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority() : int
    {
        return $this->priority;
    }

    /**
     * Attempt to compute something like "importancy factor"
     *
     * @return int
     *   An int between 0 and 10 bounds included, 0 being no importance
     */
    public function getImportancyFactor() : int
    {
        $reference = new \DateTime();

        if ($this->ts_deadline < $reference) {
            return 10; // You're fucked.
        }

        $interval = $reference->diff($this->ts_deadline);
        if ($interval->y || $interval->m || ($interval->d && 7 < $interval->d)) {
            return 0;
        }

        if ($interval->d) {
            return 7 - $interval->d;
        }

        return 6 + ceil(3 / (24 - $interval->h));
    }

    /**
     * Get add date
     *
     * @return \DateTimeInterface
     */
    public function addedAt() : \DateTimeInterface
    {
        return $this->ts_added;
    }

    /**
     * Get update date
     *
     * @return null|\DateTimeInterface
     */
    public function updatedAt()
    {
        return $this->ts_updated;
    }

    /**
     * Get done date
     *
     * @return null|\DateTimeInterface
     */
    public function doneAt()
    {
        return $this->ts_done;
    }

    /**
     * Get deadline date
     *
     * @return null|\DateTimeInterface
     */
    public function deadlinesAt()
    {
        return $this->ts_deadline;
    }

    /**
     * Get unhide date
     *
     * @return null|\DateTimeInterface
     */
    public function unhideAt()
    {
        return $this->ts_unhide;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get note count
     *
     * @return int
     */
    public function getNoteCount() : int
    {
        return $this->note_count;
    }

    /**
     * Get account identifier
     *
     * @return int
     */
    public function getIdAccount() : int
    {
        return $this->id_account;
    }
}
