<?php

namespace AppBundle\Controller;

use AppBundle\Mapper\AlarmMapper;
use AppBundle\Mapper\NoteMapper;
use AppBundle\Mapper\TaskMapper;
use Goat\AccountBundle\Controller\AccountMapperAwareController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractAppController extends Controller
{
    use AccountMapperAwareController;
    use RefererControllerTrait;

    /**
     * Get task mapper
     *
     * @return TaskMapper
     */
    final protected function getTaskMapper() : TaskMapper
    {
        return $this->getMapper('App:Task');
    }

    /**
     * Get task mapper
     *
     * @return TaskMapper
     */
    final protected function getNoteMapper() : NoteMapper
    {
        return $this->getMapper('App:Note');
    }

    /**
     * Get alarm mapper
     *
     * @return AlarmMapper
     */
    final protected function getAlarmMapper() : AlarmMapper
    {
        return $this->getMapper('App:Alarm');
    }

    /**
     * Get criteria for task that is owned by the current user
     *
     * @param int|string $id
     */
    final private function getTaskForUserCriteria($id) : array
    {
        if (!$id || !is_numeric($id)) {
            throw $this->createNotFoundException();
        }

        return ['t.id' => $id, 't.id_account' => $this->getUserAccountOrDie()->getId()];
    }

    /**
     * Render the given view as page blocks in a JSON response
     *
     * @param string $name
     * @param array $context
     *
     * @return JsonResponse
     */
    final protected function renderJsonPage(string $name, array $context) : JsonResponse
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->container->get('twig');

        /** @var \Twig_Template $template */
        $template = $twig->load($name);

        $data = [];
        foreach (['messages', 'menu', 'content'] as $block) {
            if ($template->hasBlock($block, $context)) {
                $data[$block] = $template->renderBlock($block, $context);
            }
        }

        return new JsonResponse($data);
    }
}
