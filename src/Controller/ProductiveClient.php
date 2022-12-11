<?php

namespace App\Controller;

use App\ApiHandler\ApiHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductiveClient extends AbstractController
{

    private $client;

    private $organizationID;

    private $apiKey;

    private $projects;

    private $selectedProject;

    private $taskList;

    private $selectedTaskList;

    private $file;

    private $bus;

    private $data;


    public function __construct(HttpClientInterface $client, MessageBusInterface $bus)
    {
        $this->client = $client;
        $this->bus = $bus;
    }

    public function makeTask(array $data){
        $this->data = $data;
        $this->bus->dispatch($this);
    }

    public function fetchTaskList(){
        $response = $this->client->request('GET', 'https://api.productive.io/api/v2/task_lists?filter[project_id]=' . $this->selectedProject, [
            'headers' => [
                'Content-Type' => 'application/vnd.api+json',
                'X-Auth-Token' => $this->apiKey,
                'X-Organization-Id' => $this->organizationID,
            ],
        ]);
        $data = $response->toArray()['data'];
        $returnData = [];
        foreach ($data as $datum){
            $attributes = $datum['attributes'];
            $name = $attributes['name'];
            $returnData[$name] = $datum['id'];
        }
        $this->setTaskList($returnData);
    }




    private function findOrganizationID(){
        $response = $this->client->request('GET', 'https://api.productive.io/api/v2/users', [
            'headers' => [
                'Content-Type' => 'application/vnd.api+json',
                'X-Auth-Token' => $this->apiKey
            ],
        ]);
        $data = $response->toArray()['data'];
        $attributes = $data[0]['attributes'];
        $this->setOrganizationID($attributes['default_organization_id']);
    }

    public function fetchProjects(){
        $this->organizationID == null ? $this->findOrganizationID() : null;
        $response = $this->client->request('GET', 'https://api.productive.io/api/v2/projects', [
            'headers' => [
                'Content-Type' => 'application/vnd.api+json',
                'X-Auth-Token' => $this->apiKey,
                'X-Organization-Id' => $this->organizationID,
            ],
        ]);
        $data = $response->toArray()['data'];
        $returnData = [];
        foreach ($data as $datum){
            $attributes = $datum['attributes'];
            $name = $attributes['name'];
            $returnData[$name] = $datum['id'];
        }
        $this->setProjects($returnData);
    }

    public function getRequestBody(array $data){
        return [
            "data" => [
                "type" => "tasks",
                "attributes" => [
                    "title" => $data[0],
                    "description" => $data[1],
                    "initial_estimate" => $data[2]
                ],
                "relationships" => [
                    "project" => [
                        "data" => [
                            "type" => "projects",
                            "id" => $this->selectedProject
                        ]
                    ],
                    "task_list" => [
                        "data" => [
                            "type" => "task_lists",
                            "id" => $this->selectedTaskList
                        ]
                    ]
                ]
            ]
        ];
    }


    public function getOrganizationID()
    {
        return $this->organizationID;
    }

    public function setOrganizationID($organizationID)
    {
        $this->organizationID = $organizationID;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function setProjects($projects)
    {
        $this->projects = $projects;
    }

    public function getSelectedProject()
    {
        return $this->selectedProject;
    }

    public function setSelectedProject($project)
    {
        $this->selectedProject = $project;
    }

    public function getTaskList()
    {
        return $this->taskList;
    }

    public function setTaskList($taskList)
    {
        $this->taskList = $taskList;
    }

    public function getSelectedTaskList()
    {
        return $this->selectedTaskList;
    }

    public function setSelectedTaskList($taskList)
    {
        $this->selectedTaskList = $taskList;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file): void
    {
        $this->file = $file;
    }

    public function getClient(): HttpClientInterface
    {
        return $this->client;
    }

    public function getData()
    {
        return $this->data;
    }


}