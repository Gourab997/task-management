<?php

namespace App\Services\Tasks;

use App\Repositories\Interfaces\TaskRepositoryInterface;

class TaskService
{
    private TaskRepositoryInterface $taskRepositoryInterface;

    public function __construct(TaskRepositoryInterface $taskRepositoryInterface)
    {
        $this->taskRepositoryInterface = $taskRepositoryInterface;
    }

       public function store($data)
       {
           $sortData = $this->makeData($data);

           return $this->taskRepositoryInterface->store($sortData);
       }

       public function update($data,$id)
       {
           return $this->taskRepositoryInterface->update($data,$id);
       }

         public function destroy($id)
         {
              return $this->taskRepositoryInterface->delete($id);
         }


       private function makeData($data)
       {
           $newData = [];
           if (isset($data['title'])) {
               $newData['title'] = $data['title'];
           }
           if (isset($data['description'])) {
               $newData['description'] = $data['description'];
           }
           if (isset($data['priority'])) {
               $newData['priority'] = $data['priority'];
           }
           if (isset($data['assignee'])) {
               $newData['assignee'] = $data['assignee'];
           }
           if (isset($data['image'])) {
               $newData['image'] = $data['image'];
           }
           if (isset($data['status'])) {
               $newData['status'] = $data['status'];
           }

           return $newData;
       }
}
