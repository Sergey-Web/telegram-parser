<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dto\UpdateTaskDto;
use App\Models\Repositories\TaskRepository;
use App\Models\Task;
use App\Services\TaskService;
use App\Validators\TaskValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class TaskController extends Controller
{
    public function show(int $id): JsonResponse
    {
        $taskData = TaskService::findTaskToShow($id);

        if (empty($taskData)) {
            return response()->json(['response' => $taskData], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['response' => $taskData], Response::HTTP_OK);
    }

    public function store(Request $request): JsonResponse
    {
        $errors = TaskValidator::toStore($request);

        if (!empty($errors)) {
            return response()->json(
                ['response' => ['errors' => $errors]],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            DB::beginTransaction();
            $task = (new TaskRepository())->createTaskData($request);
            DB::commit();

            $res = ['id' => $task->id, 'name' => $task->name];
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(
                ['response' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(['response' => $res], Response::HTTP_CREATED);
    }

    public function update(Task $task, Request $request): JsonResponse
    {
        $errors = TaskValidator::toUpdate($task, $request);

        if (!empty($errors)) {
            return response()->json(
                ['response' => ['errors' => $errors]],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            DB::beginTransaction();
            $updateTaskDto = new UpdateTaskDto($request);
            $taskData = (new TaskRepository())->updateDataTask($task, $updateTaskDto);
            DB::commit();

            $res = ['id' => $taskData->id, 'name' => $taskData->name];
        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(
                ['response' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(['response' => $res], Response::HTTP_CREATED);
    }

    public function destroy(int $id): JsonResponse
    {
        if (TaskService::remove($id) === false) {
            return response()->json(['response' => ['delete' => null]], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['response' => ['delete' => $id]], Response::HTTP_OK);
    }
}
