<?php


namespace App\Repositories;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;

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
            $task = $this->model::where('id', $id)->first();

            return response()->success(new TaskResource($task), 'Task Details');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'Task not found');
        }
    }

    public function update(array $data, int $id)
    {
        try {
            $user = $this->model::find($id);
            $user->update($data);

            return response()->success(new TaskResource($user), 'Task updated successfully');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'Task not found');
        }
    }

    public function delete(int $id)
    {
        $user = $this->model->find($id);
    }

}
