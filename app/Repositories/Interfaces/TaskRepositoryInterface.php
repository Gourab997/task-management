<?php

namespace App\Repositories\Interfaces;

interface TaskRepositoryInterface
{
    public function all();

    public function store(array $data);

    public function update(array $data, int $id);

    public function delete(int $id);

    public function show(int $id);
}
