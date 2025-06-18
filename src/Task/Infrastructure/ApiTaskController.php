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
use App\Task\Domain\VO\OwnerId;
use App\Task\Domain\VO\Priority;
use App\Task\Domain\VO\TaskId;
use App\Task\Domain\VO\Title;
use App\Lib\IdGenerator;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

#[Route('/api', name: 'api_')]
#[OA\Tag(name: 'Tasks', description: 'Task management endpoints')]
class ApiTaskController extends AbstractController
{
    #[Route('/task', name: 'create_task', methods: ['POST'])]
    #[OA\Post(
        path: '/api/task',
        operationId: 'createTask',
        description: 'Creates a new task with the provided data',
        summary: 'Create a new task'
    )]
    #[OA\RequestBody(
        description: 'Task data to create',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'title', required: ['title'], type: 'string'),
                new OA\Property(property: 'description', required: ['description'], type: 'string'),
                new OA\Property(property: 'priority', required: ['priority'], type: 'integer', enum: [1, 2, 3, 4, 5]),
                new OA\Property(property: 'epicTaskId', description: 'Task ID of the epic task this task belongs to', type: 'string')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the task that was created',
        content: new Model(type: Task::class)

    )]
    #[OA\Response(
        response: 400,
        description: 'Failed to create task'
    )]
    public function createTask(
        TaskCreator $creator,
        Request     $request,
    ): Response {
        try {
            $task = new Task(
                new TaskId(IdGenerator::generate()),
                new OwnerId($this->getUser()->getUserIdentifier()),
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
    #[OA\Delete(
        path: '/api/task/{id}',
        operationId: 'deleteTask',
        description: 'Deletes a task with the specified ID',
        summary: 'Delete a task'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Task ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 204,
        description: 'Task successfully deleted'
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 406,
        description: 'Task cannot be removed'
    )]
    public function deleteTask(TaskRemover $remover, string $id): Response
    {
        try {
            $remover->remove(new TaskId($id));
        } catch (AuthenticationException) {
            return new Response("You are not authorized to delete this task", 401);
        } catch (Exception $e) {
            return new Response("Task with id $id cannot be removed", 406);
        }
        return new Response("Task with id $id was successfully deleted", 204);
    }

    #[Route('/tasks', name: 'load_tasks', methods: ['GET'])]
    #[OA\Get(
        path: '/api/tasks',
        operationId: 'getTasks',
        description: 'Retrieves a list of tasks with optional filtering and sorting',
        summary: 'Get all tasks'
    )]
    #[OA\Parameter(
        name: 'query',
        description: 'Search query',
        in: 'query',
        required: false,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns a list of tasks',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Task')
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Failed to load tasks'
    )]
    public function getTasks(
        TaskLoader                    $loader,
        #[MapQueryParameter] ?string  $query = null,
        #[MapQueryString] TaskFilters $filter = new TaskFilters(),
        #[MapQueryString] Sort        $sort = new Sort()
    ): Response {
        try {
            $tasks = $loader->loadBy(new OwnerId($this->getUser()->getUserIdentifier()), $query, $filter, $sort);
        } catch (Exception $e) {
            return new Response("Failed to load tasks", 400);
        }
        return $this->json($tasks);
    }

    #[Route('/task/{id}/done', name: 'mark_as_done', requirements: ['id' => '\S{4}'], methods: ['PATCH'])]
    #[OA\Patch(
        path: '/api/task/{id}/done',
        operationId: 'markTaskAsDone',
        description: 'Marks a task with the specified ID as done',
        summary: 'Mark task as done'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Task ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Response(
        response: 204,
        description: 'Task successfully marked as done'
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 406,
        description: 'Task cannot be marked as done'
    )]
    public function markTaskAsDone(TaskEditor $editor, string $id): Response
    {
        try {
            $editor->edit(new TaskId($id), ["status" => "done"]);
        } catch (AuthenticationException) {
            return new Response("You are not authorized to mark this task as done", 401);
        } catch (Exception $e) {
            return new Response("Task with id $id cannot marc as done", 406);
        }
        return new Response("Task with id $id was successfully mark as done", 204);
    }

    #[Route('/task/{id}', name: 'update_task', requirements: ['id' => '\S{4}'], methods: ['PUT'])]
    #[OA\Put(
        path: '/api/task/{id}',
        operationId: 'updateTask',
        description: 'Updates a task with the specified ID with the provided data',
        summary: 'Update a task'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'Task ID',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\RequestBody(
        description: 'Task data to update',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'title', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'priority', type: 'integer', enum: [1, 2, 3, 4, 5]),
                new OA\Property(property: 'status', type: 'string', enum: ['todo', 'done']),
                new OA\Property(property: 'epicTaskId', description: 'Task ID of the epic task this task belongs to', type: 'string')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns the updated task',
        content: new Model(type: Task::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'No fields were updated or invalid request'
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized'
    )]
    #[OA\Response(
        response: 406,
        description: 'Task cannot be updated'
    )]
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
        } catch (AuthenticationException) {
            return new Response("You are not authorized to update this task", 401);
        } catch (Exception $e) {
            return new Response("Task with id $id cannot be updated", 406);
        }

        return $this->json($loader->loadById($taskId));
    }
}