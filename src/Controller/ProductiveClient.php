<?php

namespace App\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductiveClient
{

    private static $client;

    private static $organizationID;

    private static $apiKey;

    private static $projects;

    private static $selectedProject;

    private static $taskList;

    private static $selectedTaskList;

    public function __construct(HttpClientInterface $client)
    {
        self::$client = $client;
    }

    public function makeTask(array $data){
        foreach ($data as $datum){
            self::$client->request('POST', 'https://api.productive.io/api/v2/tasks', [
                'json' => $this->getRequestBody($datum),
                'headers' => [
                    'Content-Type' => 'application/vnd.api+json',
                    'X-Auth-Token' => self::$apiKey,
                    'X-Organization-Id' => self::$organizationID,
                ],
            ]);
        }
    }

    public function fetchTaskList(){
        $response = self::$client->request('GET', 'https://api.productive.io/api/v2/task_lists?filter[project_id]=' . self::$selectedProject, [
            'headers' => [
                'Content-Type' => 'application/vnd.api+json',
                'X-Auth-Token' => '639b5ddd-2a05-4c44-a33c-51513ad633d0',
                'X-Organization-Id' => '4085',
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
        $response = self::$client->request('GET', 'https://api.productive.io/api/v2/users', [
            'headers' => [
                'Content-Type' => 'application/vnd.api+json',
                'X-Auth-Token' => self::$apiKey
            ],
        ]);
        $data = $response->toArray()['data'];
        $attributes = $data[0]['attributes'];
        $this->setOrganizationID($attributes['default_organization_id']);
    }

    public function fetchProjects(){
        self::$organizationID == null ? $this->findOrganizationID() : null;
        $response = self::$client->request('GET', 'https://api.productive.io/api/v2/projects', [
            'headers' => [
                'Content-Type' => 'application/vnd.api+json',
                'X-Auth-Token' => self::$apiKey,
                'X-Organization-Id' => self::$organizationID,
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

    private function getRequestBody(array $data){
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
                            "id" => self::$selectedProject
                        ]
                    ],
                    "task_list" => [
                        "data" => [
                            "type" => "task_lists",
                            "id" => self::$selectedTaskList
                        ]
                    ]
                ]
            ]
        ];
    }


    public function getOrganizationID()
    {
        return self::$organizationID;
    }

    public function setOrganizationID($organizationID)
    {
        self::$organizationID = $organizationID;
    }

    public function getApiKey()
    {
        return self::$apiKey;
    }

    public function setApiKey($apiKey)
    {
        self::$apiKey = $apiKey;
    }

    public function getProjects()
    {
        return self::$projects;
    }

    public function setProjects($projects)
    {
        self::$projects = $projects;
    }

    public function getSelectedProject()
    {
        return self::$selectedProject;
    }

    public function setSelectedProject($project)
    {
        self::$selectedProject = $project;
    }

    public function getTaskList()
    {
        return self::$taskList;
    }

    public function setTaskList($taskList)
    {
        self::$taskList = $taskList;
    }

    public function getSelectedTaskList()
    {
        return self::$selectedTaskList;
    }

    public function setSelectedTaskList($taskList)
    {
        self::$selectedTaskList = $taskList;
    }


}