<?php

namespace AppBundle\SabreDav;

use AppBundle\Entity\Note;
use AppBundle\Entity\Task;
use AppBundle\Mapper\NoteMapper;
use AppBundle\Mapper\TaskMapper;
use Goat\AccountBundle\Entity\Account;
use Goat\AccountBundle\Security\User\GoatUser;
use Goat\Mapper\Error\EntityNotFoundError;
use Sabre\CalDAV;
use Sabre\CalDAV\Backend\AbstractBackend;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * CalDav backend for user tasks.
 *
 * It extracts tasks as VTODO and comments as VJOURNAL iCal entries.
 *
 * @todo
 *   - implements methods from the AbstractBackend to have scalable queries
 */
class CalDavBackend extends AbstractBackend
{
    const ICAL_DATE_FORMAT = 'Ymd\THis';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var TaskMapper
     */
    private $taskMapper;

    /**
     * @var NoteMapper
     */
    private $noteMapper;

    /**
     * Default constructor
     *
     * @param TokenStorageInterface $tokenStorage
     * @param TaskMapper $taskMapper
     * @param NoteMapper $noteMapper
     */
    public function __construct(TokenStorageInterface $tokenStorage, TaskMapper $taskMapper, NoteMapper $noteMapper)
    {
        $this->tokenStorage = $tokenStorage;
        $this->taskMapper = $taskMapper;
        $this->noteMapper = $noteMapper;
    }

    /**
     * Get user identifier
     *
     * @return Account
     */
    private function getUserAccount() : Account
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new AccessDeniedHttpException();
        }

        $user = $token->getUser();
        if (!$user instanceof GoatUser) {
            throw new AccessDeniedHttpException();
        }

        return $user->getAccount();
    }

    /**
     * Get user mail
     *
     * @return string
     */
    private function getUserMail() : string
    {
        return $this->getUserAccount()->getMail();
    }

    /**
     * Get calendar URI
     *
     * @return string
     */
    private function getCalendarUri() : string
    {
        return 'task-list-1.ics';
    }

    /**
     * Get task URI
     *
     * @param int $taskId
     *
     * @return string
     */
    private function getTaskUri(int $taskId) : string
    {
        return 'task-' . $taskId . '.ics';
    }

    /**
     * Get task URI
     *
     * @param int $taskId
     * @param int $noteId
     *
     * @return string
     */
    private function getNoteUri(int $taskId, int $noteId) : string
    {
        return 'note-' . $taskId . '-' . $noteId . '.ics';
    }

    /**
     * Parse object URI
     *
     * @return array
     *   First value is a string container the type ('task' or 'note')
     *   Second value is the object identifier
     */
    private function parseObjectUri(string $uri) : array
    {
        $matches = [];

        if (preg_match('/note-\d+-(\d+)\.ics$/', $uri, $matches)) {
            return ['note', $matches[1]];
        }
        if (preg_match('/task-(\d+)\.ics$/', $uri, $matches)) {
            return ['task', $matches[1]];
        }

        throw new NotFoundHttpException();
    }

    /**
     * {@inheritdoc}
     */
    public function getCalendarsForUser($principalUri)
    {
        // Calendar list is fixed by the app.
        return [
            [
                'id'            => $this->getUserAccount()->getId(),
                'uri'           => $this->getCalendarUri(),
                'read-only'     => 1,
                'principaluri'  => $principalUri
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function createCalendar($principalUri, $calendarUri, array $properties)
    {
        throw new AccessDeniedHttpException("This CalDAV backend is read-only");
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCalendar($calendarId)
    {
        throw new AccessDeniedHttpException("This CalDAV backend is read-only");
    }

    /**
     * Escape iCal string
     *
     * @param string $string
     *
     * @return string
     */
    private function escapeICalString(string $string) : string
    {
        return $string;
    }

    /**
     * Escape iCal date
     *
     * @param \DateTime $date
     *
     * @return string
     */
    private function escapeICalDate(\DateTime $date) : string
    {
        // From what I can read, there is no timezone offset support in the
        // iCal standard, so the only way to support it without having the
        // timezone identifier (good bye, DST) is to give the date as an
        // UTC date, so we rely on PHP to convert it.
        $utc = clone $date;
        $utc->setTimezone(new \DateTimeZone("UTC"));

        return $utc->format(self::ICAL_DATE_FORMAT) . "Z";
    }

    private function convertTaskToICalAlarm(Task $task, Alarm $alarm)
    {
        $trigger = clone $task->deadlinesAt();
        $trigger = $trigger->sub($alarm->getDelay());

        return <<<EOT
BEGIN:VALARM
ACTION:DISPLAY
TRIGGER;VALUE=DATE-TIME:{$this->escapeICalDate($trigger)}
REPEAT:{$alarm->getRepeatCount()}
DURATION:PT15M
DESCRIPTION:{$this->escapeICalString($task->getTitle())}
DURATION:PT1H
END:VALARM
EOT;
    }

    /**
     * Convert task to iCal data
     *
     * @param Task $task
     *
     * @return string
     */
    private function convertTaskToICal(Task $task) : string
    {
        // @todo
        //   - CATEGORIES : tags
        //   - LOCATION : not supported for now

        $start = clone $task->deadlinesAt();
        $start->sub(new \DateInterval('PT3600S'));

        return <<<EOT
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//GTD//GTD//EN
CALSCALE:GREGORIAN
BEGIN:VEVENT
UID:{$this->escapeICalString($this->getUserMail())}
CREATED;VALUE=DATE-TIME:{$this->escapeICalDate($task->addedAt())}
LAST-MODIFIED;VALUE=DATE-TIME:{$this->escapeICalDate($task->updatedAt())}
SUMMARY:{$this->escapeICalString($task->getTitle())}
DTSTART;VALUE=DATE-TIME:{$this->escapeICalDate($start)}
DTEND;VALUE=DATE-TIME:{$this->escapeICalDate($task->deadlinesAt())}
LOCATION:
DESCRIPTION:{$this->escapeICalString($task->getDescription())}
CATEGORIES:
END:VEVENT
END:VCALENDAR
EOT;
    }

    /**
     * Convert note to iCal data
     *
     * @param Task $task
     * @param Note $note
     *
     * @return string
     */
    private function convertNoteToICal(Task $task, Note $note) : string
    {
        $escapedMail = $this->escapeICalString($this->getUserMail());

        return <<<EOT
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//GTD//GTD//EN
BEGIN:VJOURNAL
DTSTAMP;VALUE=DATE-TIME:{$this->escapeICalDate($note->addedAt())}
UID:{$escapedMail}
ORGANIZER:MAILTO:{$escapedMail}
STATUS:DRAFT
CLASS:PUBLIC
CATEGORY:{$this->escapeICalString($task->getTitle())}
DESCRIPTION:{$this->escapeICalString($note->getDescription())}
END:VJOURNAL
END:VCALENDAR
EOT;
    }

    /**
     * Build task calendar object for SabreDav
     *
     * @param int|string $calendarId
     * @param Task $task
     *
     * @return array
     */
    private function buildTaskCalendarObject($calendarId, Task $task) : array
    {
        $taskId = $task->getId();
        $iCalData = $this->convertTaskToICal($task);

        return [
            'id'           => $taskId,
            'uri'          => $this->getTaskUri($taskId),
            'etag'         => '"' . md5($iCalData) . '"',
            'lastmodified' => $task->updatedAt()->getTimestamp(),
            'calendarid'   => $calendarId,
            'size'         => strlen($iCalData),
            'calendardata' => $iCalData,
        ];
    }

    /**
     * Build task calendar object for SabreDav
     *
     * @param int|string $calendarId
     * @param Task $task
     * @param Note $note
     *
     * @return array
     */
    private function buildNoteCalendarObject($calendarId, Task $task, Note $note) : array
    {
        $taskId = $task->getId();
        $noteId = $note->getId();

        $iCalData = $this->convertNoteToICal($task, $note);

        return [
            'id'           => $taskId,
            'uri'          => $this->getNoteUri($taskId, $noteId),
            'etag'         => '"' . md5($iCalData) . '"',
            'lastmodified' => $note->updatedAt()->getTimestamp(),
            'calendarid'   => $calendarId,
            'size'         => strlen($iCalData),
            'calendardata' => $iCalData,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCalendarObjects($calendarId)
    {
        $ret = [];
        $tasks = $this->taskMapper->findBy(['t.id_account' => $this->getUserAccount()->getId()]);

        /** @var \AppBundle\Entity\Task $task */
        foreach ($tasks as $task) {
            $ret[] = $this->buildTaskCalendarObject($calendarId, $task);

            if ($task->getNoteCount()) {
                $notes = $this->noteMapper->findBy(['id_task' => $task->getId()]);

                /** @var \AppBundle\Entity\Note $note */
                foreach ($notes as $note) {
                    $ret[] = $this->buildNoteCalendarObject($calendarId, $task, $note);
                }
            }
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function getCalendarObject($calendarId, $objectUri)
    {
        // Check that calendar identifier is valid
        if ($this->getUserAccount()->getId() !== (int)$calendarId) {
            throw new AccessDeniedHttpException();
        }

        list($type, $id) = $this->parseObjectUri($objectUri);

        try {
            switch ($type) {

                case 'task':
                    $task = $this->taskMapper->findOne($id);
                    return $this->buildTaskCalendarObject($calendarId, $task);

                case 'note':
                    /** @var \AppBundle\Entity\Note $note */
                    $note = $this->noteMapper->findOne($id);
                    $task = $this->taskMapper->findOne($note->getTaskId());
                    return $this->buildNoteCalendarObject($calendarId, $task, $note);

                default:
                    throw new NotFoundHttpException();
            }
        } catch (EntityNotFoundError $e) {
            throw new NotFoundHttpException(null, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createCalendarObject($calendarId, $objectUri, $calendarData)
    {
        // @todo allows this for VTODO and VJOURNAL
        throw new AccessDeniedHttpException("This CalDAV backend is read-only");
    }

    /**
     * {@inheritdoc}
     */
    public function updateCalendarObject($calendarId, $objectUri, $calendarData)
    {
        // @todo allows this for VTODO and VJOURNAL
        throw new AccessDeniedHttpException("This CalDAV backend is read-only");
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCalendarObject($calendarId, $objectUri)
    {
        // @todo allows this for VTODO and VJOURNAL
        throw new AccessDeniedHttpException("This CalDAV backend is read-only");
    }
}
