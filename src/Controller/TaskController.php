<?php

namespace App\Controller;

use App\Form\Type\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TaskController extends AbstractController
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route(path: '/task', name: 'task')]
    public function makeTask(Request $request){

        $form = $this->createForm(TaskType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->client->request('POST', 'https://api.productive.io/api/v2/tasks', [
                'json' => $this->getRequestBody($form),
                'headers' => [
                    'Content-Type' => 'application/vnd.api+json',
                    'X-Auth-Token' => '639b5ddd-2a05-4c44-a33c-51513ad633d0',
                    'X-Organization-Id' => '4085',
                ],
            ]);
            return $this->redirectToRoute('task');
        }




        return $this->render('base.html.twig',[
            'form' => $form
        ]);
    }

    private function getRequestBody(FormInterface $form){
        $data = $form->getData();
        return [
            "data" => [
                "type" => "tasks",
                "attributes" => [
                    "title" => $data['title'],
                    "description" => $data['description'],
                    "initial_estimate" => $data['estimation']
                ],
                "relationships" => [
                    "project" => [
                        "data" => [
                            "type" => "projects",
                            "id" => "264012"
                        ]
                    ],
                    "task_list" => [
                        "data" => [
                            "type" => "task_lists",
                            "id" => $data['task_list']
                        ]
                    ]
                ]
            ]
        ];
    }



}