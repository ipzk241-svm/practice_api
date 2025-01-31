<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1')]
class TestController extends AbstractController
{
    private static array $users = [
        [
            'id' => '1',
            'name' => 'test1',
            'email' => 'ipzk241_svm@student.ztu.edu.ua',
        ],
        [
            'id' => '2',
            'name' => 'test2',
            'email' => 'ipzk241_svm1@student.ztu.edu.ua',
        ],
        [
            'id' => '3',
            'name' => 'test3',
            'email' => 'ipzk241_svm2@student.ztu.edu.ua',
        ]
    ];

    private static array $response = ['success' => null, 'code' => null, 'message' => null, 'data' => null];

    #[Route('/users', name: 'app_get_users', methods: ['GET'])]
    public function getCollection(): JsonResponse
    {
        self::$response['success'] = true;
        self::$response['message'] = 'Get users succesfully';
        self::$response['data'] = self::$users;
        self::$response['code'] = 200;

        return new JsonResponse(self::$response);
    }

    #[Route('/users/{id}', name: 'app_get_user', methods: ['GET'])]
    public function getItem(string $id): JsonResponse
    {
        $userToFind = null;
        foreach (self::$users as $user) {
            if(!isset($user['id'])){
                continue;
            }
            if($user['id'] === $id){
                $userToFind = $user;
                break;
            }
        }
        
        if ($userToFind === null) {
            self::$response['success'] = false;
            self::$response['message'] = 'user is not found';
            self::$response['code'] = 404;
            self::$response['data'] = [];

            return new JsonResponse(self::$response, Response::HTTP_NOT_FOUND);
        }

        self::$response['success'] = true;
        self::$response['message'] = 'Get user succesfully';
        self::$response['data'] = [$userToFind];
        self::$response['code'] = 200;

        return new JsonResponse(self::$response);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/users', name: 'app_create_users', methods: ['POST'])]
    public function postCollection(Request $request): JsonResponse
    {
        $newId = Uuid::uuid4();
        $data = json_decode($request->getContent(), true);

        if(!isset($data['email'], $data['name'])){
            self::$response['success'] = false;
            self::$response['message'] = 'Unprocessable Entity';
            self::$response['data'] = [];
            self::$response['code'] = 422;   

            return new JsonResponse(self::$response, Response::HTTP_UNPROCESSABLE_ENTITY);

        }

        $newUser['id'] = $newId;
        foreach($data as $key => $dataVal){
            $newUser[$key] = $dataVal;
        }

        array_push(self::$users, $newUser);
        
        self::$response['success'] = true;
        self::$response['message'] = 'user created succesfully';
        self::$response['data'] = [self::$users];
        self::$response['code'] = 201;

        return new JsonResponse(self::$response, 201);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/users/{id}', name: 'app_update_user', methods: ['PATCH'])]
    public function patchItem(string $id, Request $request): JsonResponse
    {
        $userToUpdate = null;

        foreach (self::$users as $user) {
            if ($user['id'] === $id) {
                $userToUpdate = $user; 
                break;
            }
        }

        if ($userToUpdate === null) {
            self::$response['success'] = false;
            self::$response['message'] = 'user is not found';
            self::$response['data'] = [];
            self::$response['code'] = 404;

            return new JsonResponse(self::$response, 404);
        }
        
        $data = json_decode($request->getContent(), true);
        
        if($data != null){
            foreach($data as $key => $dataVal){
                if($key === 'id'){
                    continue;
                }
                if(array_key_exists($key, $userToUpdate)){
                    $userToUpdate[$key] = $dataVal;
                }
            }
        }

        self::$response['success'] = true;
        self::$response['message'] = 'user changed succesfully';
        self::$response['data'] = [$userToUpdate];
        self::$response['code'] = 200;
        
        return new JsonResponse(self::$response);
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/users/{id}', name: 'app_delete_user', methods: ['DELETE'])]
    public function deleteItem(string $id): JsonResponse
    {
        $userToDeleteInd = null;

        foreach (self::$users as $key => $user) {
            if ($user['id'] === $id) {
                $userToDeleteInd = $key;
                break;
            }
        }

        if ($userToDeleteInd === null) {
            self::$response['success'] = true;
            self::$response['message'] = 'user is not found';
            self::$response['data'] = [];
            self::$response['code'] = 404;

            return new JsonResponse( self::$response, 404);
        }

        unset(self::$users[$userToDeleteInd]);

        self::$response['success'] = true;
        self::$response['message'] = 'user deleted succesfully';
        self::$response['data'] = [];
        self::$response['code'] = 200;

        return new JsonResponse(self::$response, 200);
    }


}
