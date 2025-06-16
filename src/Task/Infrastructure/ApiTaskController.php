<?php

declare(strict_types=1);

namespace App\Task\Infrastructure;

use App\Task\Application\API\TaskCreator;
use App\Task\Application\API\TaskEditor;
use App\Task\Application\API\TaskLoader;
use App\Task\Application\API\TaskRemover;
use App\Task\Application\DTO\Sort;
use App\Task\Application\DTO\TaskFilters;
use App\Task\Domain\Entities\Task;
use App\Task\Domain\VO\Description;
use App\Task\Domain\VO\Priority;
use App\Task\Domain\VO\TaskId;
use App\Task\Domain\VO\Title;
use App\Lib\IdGenerator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

//28.30
#[Route('/api', name: 'api_')]
class ApiTaskController extends AbstractController
{
    #[Route('/task', name: 'create_task', methods: ['POST'])]
    public function createTask(
        TaskCreator $creator,
        Request     $request,
    ): Response {
        try {
            $task = new Task(
                new TaskId(IdGenerator::generate()),
                new Title($request->getPayload()->get("title")),
                new Description($request->getPayload()->get("description")),
                Priority::from($request->getPayload()->get("priority")),
                epicTaskId: $request->getPayload()->has('epicTaskId')
                    ? new TaskId($request->getPayload()->get("epicTaskId")) : null);
            $creator->create($task);
        } catch (Exception $e) {
            return new Response("Failed to create task", 400);
        }
        return $this->json($task);

    }

    #[Route('/task/{id}', name: 'delete_task', requirements: ['id' => '\S{4}'], methods: ['DELETE'])]
    public function deleteTask(TaskRemover $remover, string $id): Response
    {
        try {
            $remover->remove(new TaskId($id));
        } catch (Exception $e) {
            return new Response("Task with id $id cannot be removed", 406);
        }
        return new Response("Task with id $id was successfully deleted", 204);
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

    #[Route('/task/{id}/done', name: 'mark_as_done', requirements: ['id' => '\S{4}'], methods: ['PATCH'])]
    public function markTaskAsDone(TaskEditor $editor, string $id): Response
    {
        try {
            $editor->edit(new TaskId($id), ["status" => "done"]);
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