<?php

namespace App\Services\Users;

use App\Repositories\Interfaces\UserRepositoryInterface;

class UserService
{
    private UserRepositoryInterface $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

       public function register($data)
       {
           $sortData = $this->makeData($data);

           return $this->userRepositoryInterface->store($sortData);
       }

       public function profile($data)
       {
           return $this->userRepositoryInterface->show($data);
       }

       public function updateProfile($id, $data)
       {
           $sortData = $this->makeData($data);

           return $this->userRepositoryInterface->update($sortData, $id);
       }

       public function updatePassword($id, $data)
       {
           $sortData = $this->makeData($data);

           return $this->userRepositoryInterface->updatePassword($sortData, $id);
       }

       private function makeData($data)
       {
           $newData = [];
           if (isset($data['first_name'])) {
               $newData['first_name'] = $data['first_name'];
           }
           if (isset($data['last_name'])) {
               $newData['last_name'] = $data['last_name'];
           }
           if (isset($data['email'])) {
               $newData['email'] = $data['email'];
           }
           if (isset($data['password'])) {
               $newData['password'] = $data['password'];
           }
           if (isset($data['phone'])) {
               $newData['phone'] = $data['phone'];
           }
           if (isset($data['old_password'])) {
               $newData['old_password'] = $data['old_password'];
           }

           return $newData;
       }
}
