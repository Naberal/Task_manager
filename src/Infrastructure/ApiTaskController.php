<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\API\TaskCreator;
use App\Application\API\TaskEditor;
use App\Application\API\TaskLoader;
use App\Application\API\TaskRemover;
use App\Application\DTO\Sort;
use App\Application\DTO\TaskFilters;
use App\Domain\Entities\Task;
use App\Domain\VO\Description;
use App\Domain\VO\Priority;
use App\Domain\VO\TaskId;
use App\Domain\VO\Status;
use App\Domain\VO\Title;
use App\Lib\IdGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

//16
#[Route('/api', name: 'api_')]
class ApiTaskController extends AbstractController
{
    #[Route('/task', name: 'create_task', methods: ['POST'])]
    public function createTask(
        TaskCreator                      $creator,
        #[MapRequestPayload] Title       $title,
        #[MapRequestPayload] Description $description,
        #[MapRequestPayload] Priority    $priority,
        #[MapRequestPayload] ?TaskId     $epicTaskId = null,
    ): Response {
        $task = new Task(new TaskId(IdGenerator::generate()), $title, $description, $priority, epicTaskId: $epicTaskId);
        $creator->create($task);
        return $this->json($task);

    }

    #[Route('/task/{id}', name: 'delete_task', methods: ['DELETE'])]
    public function deleteTask(TaskRemover $remover, #[MapQueryString] TaskId $id): Response
    {
        $remover->remove($id);
        return new Response("Task with id $id was successfully deleted", 201);
    }

    #[Route('/tasks', name: 'load_tasks', methods: ['GET'])]
    public function getTasks(
        TaskLoader                    $loader,
        #[MapQueryParameter] ?string  $query = null,
        #[MapQueryString] TaskFilters $filter = new TaskFilters(),
        #[MapQueryString] Sort        $sort = new Sort()
    ): Response {
        $tasks = $loader->loadBy($query, $filter, $sort);
        return $this->json($tasks);
    }

    #[Route('/task/{id}/done', name: 'mark_as_done', requirements: ['id' => '\S{4}'], methods: ['PUT'])]
    public function markTaskAsDone(TaskEditor $editor, string $id): Response
    {
        try {
            $editor->edit(new TaskId($id), ["status"=>"done"]);
        } catch (Exception $e) {
            return new Response("Task with id $id cannot marc as done", 406);
        }
        return new Response("Task with id $id was successfully mark as done", 204);
    }

    #[Route('/task/{id}', name: 'update_task', requirements: ['id' => '\S{4}'], methods: ['PUT'])]
    public function updateTask(
        TaskEditor $editor,
        TaskLoader $loader,
        Request    $request,
        string     $id
    ): Response {
        $requestData = json_decode($request->getContent(), true);
        if (empty($requestData)) {
            return new Response("No fields were updated", 400);
        }
        try {
            $taskId = new TaskId($id);
            $editor->edit($taskId, $requestData);
        } catch (Exception $e) {
            return new Response("Task with id $id cannot be updated", 406);
        }

        return $this->json($loader->loadById($taskId));
    }
}