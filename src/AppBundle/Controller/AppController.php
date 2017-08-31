<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use Goat\Query\Query;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AppController extends AbstractAppController
{
    /**
     * Main screen action: display all pending tasks
     */
    public function tasksAction(Request $request)
    {
        /** @var \Goat\Runner\RunnerInterface $session */
        $session = $this->get('goat.session');

        $query = $session
            ->select('task', 't')
            ->column('t.*')
            ->columnExpression('count(c.id)', 'note_count')
            ->condition('t.id_account', $this->getUserAccountOrDie()->getId())
            ->leftJoin('task_comment', 'c.id_task = t.id',  'c')
            ->groupBy('t.id')
        ;

        // Handle filters
        $showDone = (bool)$request->get('done', false);
        if (!$showDone) {
            $query->condition('t.is_done', false);
        }

        $showHidden = (bool)$request->get('hidden', false);
        if (!$showHidden) {
            $query->condition('t.is_hidden', false);
        }

        // Handle sorts
        $sortField = $request->get('sort', 'ts_deadline');
        $sortOrder = $request->get('order', 'desc');
        if ($sortOrder !== 'asc') {
            $sortOrder = Query::ORDER_DESC;
        } else {
            $sortOrder = Query::ORDER_ASC;
        }
        switch ($sortField) {

            case 'created':
                $query->orderBy('t.ts_added', $sortOrder);
                break;

            case 'priority':
                $query->orderBy('t.priority', $sortOrder);
                break;

            case 'updated':
                $query->orderBy('t.ts_updated', $sortOrder);
                break;

            default:
                $query->orderBy('t.ts_deadline', $sortOrder);
                break;
        }

        return $this->render('app/tasks.html.twig', [
            'tasks' => $query->execute([], Task::class),
        ]);
    }

    /**
     * Help screen
     */
    public function helpAction()
    {
        $account = $this->getUserAccountOrDie();

        return $this->render('app/help.html.twig', [
            'account' => $account,
            'task_dav' => $this->generateUrl('app_dav', [
                'incomingUrl' => 'calendars/john.smith@example.com/task-list-' . $account->getId() . '.ics',
            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
}
