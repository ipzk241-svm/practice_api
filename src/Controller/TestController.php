<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Ramsey\Uuid\Uuid;

#[Route('/api/v1')]
class TestController extends AbstractController
{
    private array $users = [
        [
            'id' => '1',
            'name' => 'test1',
            'email' => 'test@gmail.com',
        ],
        [
            'id' => '2',
            'name' => 'test2',
            'email' => '123123@gmail.com',
        ]
    ];

    private array $response = ['success' => null, 'message' => null, 'data' => null];

    #[Route('/users', name: 'app_get_users', methods: ['GET'])]
    public function getCollection(): JsonResponse
    {
        $this->response['success'] = true;
        $this->response['message'] = 'Get users succesfully';
        $this->response['data'] = $this->users;

        return new JsonResponse($this->response);
    }

    #[Route('/users/{id}', name: 'app_get_user', methods: ['GET'])]
    public function getItem(string $id): JsonResponse
    {
        $userToFind = null;
        foreach ($this->users as $user) {
            if($user['id'] === $id){
                $userToFind = $user;
                break;
            }
        }
        
        if ($userToFind === null) {
            $this->response['success'] = false;
            $this->response['message'] = 'user is not found';
            $this->response['data'] = [];

            return new JsonResponse($this->response, 404);
        }

        $this->response['success'] = true;
        $this->response['message'] = 'Get user succesfully';
        $this->response['data'] = [$userToFind];

        return new JsonResponse($this->response);
    }

    #[Route('/users', name: 'app_create_users', methods: ['POST'])]
    public function postCollection(Request $request): JsonResponse
    {
        $newId = Uuid::uuid4();
        $data = json_decode($request->getContent(), true);
        $newUser = null;
        $newUser['id'] = $newId;
        foreach($data as $key => $dataVal){

            $newUser[$key] = $dataVal;
        }

        array_push($this->users, $newUser);
        
        $this->response['success'] = true;
        $this->response['message'] = 'user created succesfully';
        $this->response['data'] = [$newUser];

        return new JsonResponse($this->users, 201);
    }

    #[Route('/users/{id}', name: 'app_update_user', methods: ['PATCH'])]
    public function patchItem(string $id, Request $request): JsonResponse
    {
        $userToUpdate = null;

        foreach ($this->users as $user) {
            if ($user['id'] === $id) {
                $userToUpdate = $user; 
                break;
            }
        }

        if ($userToUpdate === null) {
            $this->response['success'] = false;
            $this->response['message'] = 'user is not found';
            $this->response['data'] = [];

            return new JsonResponse($this->response, 404);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if($data != null){
            foreach($data as $key => $dataVal){
                if(array_key_exists($key, $userToUpdate)){
                    $userToUpdate[$key] = $dataVal;
                }
            }
        }
        
        $this->response['success'] = true;
        $this->response['message'] = 'user changed succesfully';
        $this->response['data'] = [$userToUpdate];
        
        return new JsonResponse($this->response);
    }

    #[Route('/users/{id}', name: 'app_delete_user', methods: ['DELETE'])]
    public function deleteItem(string $id): JsonResponse
    {
        $userToDeleteInd = null;

        foreach ($this->users as $key => $user) {
            if ($user['id'] === $id) {
                $userToDeleteInd = $key;
                break;
            }
        }

        if ($userToDeleteInd === null) {
            $this->response['success'] = true;
            $this->response['message'] = 'user is not found';
            $this->response['data'] = [];

            return new JsonResponse( $this->response, 404);
        }

        unset($this->users[$userToDeleteInd]);

        $this->response['success'] = true;
        $this->response['message'] = 'user deleted succesfully';
        $this->response['data'] = [];

        return new JsonResponse($this->response, 200);
    }


}
