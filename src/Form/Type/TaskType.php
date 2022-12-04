<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class TaskType extends AbstractType
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task_list', ChoiceType::class, [
                'choices'  => $this->getTaskList()])
            ->add('title', TextType::class)
            ->add('description', TextType::class)
            ->add('estimation', NumberType::class)
            ->add('save', SubmitType::class)
        ;
    }


    private function getTaskList(){
        $response = $this->client->request('GET', 'https://api.productive.io/api/v2/task_lists?filter[project_id]=264012', [
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
        return $returnData;
    }
}