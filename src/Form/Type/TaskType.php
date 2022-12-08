<?php

namespace App\Form\Type;

use App\Controller\ProductiveClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class TaskType extends AbstractType
{
    private $productiveClient;

    public function __construct(ProductiveClient $productiveClient)
    {
        $this->productiveClient = $productiveClient;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task_list', ChoiceType::class, [
                'choices'  => $this->productiveClient->fetchTaskList()])
            ->add('title', TextType::class)
            ->add('description', TextType::class)
            ->add('estimation', NumberType::class)
            ->add('save', SubmitType::class)
        ;
    }

}