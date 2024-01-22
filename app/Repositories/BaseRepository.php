<?php

namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function firstOrCreate($data)
    {
        return $this->model->firstOrCreate($data);
    }

    public function insert(array $data)
    {
        $insert_data = array_map(function ($value) {
            return $value;
        }, $data);

        return $this->model->insert($insert_data);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getAll()
    {
        return $this->model->get();
    }

    public function update(array $data, int $id)
    {
        return $this->model->update($data);
    }

    public function delete(int $id)
    {
        return $this->model->delete($id);
    }

    public function updateTemplate(array $data, int $id, array $batchProduct)
    {
        return $this->model->update($data);
    }
}
