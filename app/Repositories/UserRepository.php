<?php

namespace App\Repositories;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
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
            $users = $this->model->create($data);

            return response()->success(new UserResource($users), 'User created successfully');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'Registration failed');
        }
    }

    public function show(int $id)
    {
        try {
            $user = $this->model::with('organization')->where('id', $id)->first();

            return response()->success(new UserResource($user), 'User profile');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'User not found');
        }
    }

    public function update(array $data, int $id)
    {
        try {
            $user = $this->model::find($id);
            $user->update($data);

            return response()->success(new UserResource($user), 'User updated successfully');
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'User not found');
        }
    }

    public function delete(int $id)
    {
        $user = $this->model->find($id);
    }

    public function updatePassword($data, $id)
    {
        try {
            $user = $this->model->find($id);
            if (\Hash::check(request()->old_password, $user->password)) {
                $user->password = \Hash::make(request()->password);
                $user->save();

                return response()->success('', 'Password updated successfully');
            }

            return response()->error('Old password is incorrect', 401);
        } catch (\Exception $e) {
            return response()->error($e->getMessage(), 'Password not updated');
        }
    }
}
