<?php

namespace App\Repositories\Interfaces;

interface BaseRepositoryInterface
{
    public function create($data);

    public function insert(array $data);

    public function find($id);

    public function firstOrCreate($data);

    public function findOrFail($id);

    public function getAll();

    public function update(array $data, int $id);

    public function updateTemplate(array $data, int $id,array $batchProduct);

    public function delete(int $id);
}
