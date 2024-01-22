<?php


namespace App\Repositories;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function all()
    {
        return $this->model->all();
    }

    public function store(array $data)
    {
        try {
            // manage image

            if (request()->hasFile('image')) {
                $image = request()->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $data['image'] = $imageName;
            }
            $data['status'] = 'New';
            $task = $this->model->create($data);

            return response()->success(new TaskResource($task), 'Task created successfully');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'Error occurred');
        }
    }

    public function show(int $id)
    {
        try {
            $task = Cache::remember('task_' . $id, now()->addMinutes(60), function () use ($id) {
                return $this->model::find($id);
            });

            if (!$task) {
                return response()->error('Task not found', 404);
            }

            $cacheKey = 'task_' . $id . '_updated_at';
            $lastUpdatedAt = Cache::get($cacheKey);

            if (!$lastUpdatedAt || $lastUpdatedAt < $task->updated_at) {
                $imagePath = public_path('images/' . $task->image);
                $imageFile = file_get_contents($imagePath);
                $imageBase64 = base64_encode($imageFile);
                $task->image = $imageBase64;

                Cache::put('task_' . $id, $task, now()->addMinutes(60));

                Cache::put($cacheKey, $task->updated_at, now()->addMinutes(60));
            }

            return response()->success(new TaskResource($task), 'Task Details');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'Task not found');
        }
    }

    public function update(array $data, int $id)
    {
        try {
            $task = $this->model::find($id);

            if (!$task) {
                return response()->error('Task not found', 404);
            }

            if (isset($data['status']) && $data['status'] !== $task->status) {
                $currentTime = now();
                $statusChangeTime = $task->updated_at ?? $task->created_at;
                if ($task->status === 'In Progress' && $currentTime->diffInMinutes($statusChangeTime) < 15) {
                    return response()->error('Cannot change status within 15 minutes', 400);
                }
                if ($task->status === 'Deployed') {
                    return response()->error('Cannot change status after it is Deployed', 400);
                }
            }
            $task->update($data);

            return response()->success(new TaskResource($task), 'Task updated successfully');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'Error updating task');
        }
    }

    public function delete(int $id)
    {
        try {
            $task = $this->model::find($id);

            if (!$task) {
                return response()->error('Task not found', 404);
            }

            $task->delete();

            return response()->success(new TaskResource($task), 'Task deleted successfully');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'Error deleting task');
        }
    }

}
