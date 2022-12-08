<?php

namespace App\Form\Type;

use App\Controller\ProductiveClient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\File;

class TaskUploadType extends AbstractType
{
    private $productiveClient;

    public function __construct(ProductiveClient $productiveClient)
    {
        $this->productiveClient = $productiveClient;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('api_key',TextType::class, [
                    'data' => $this->productiveClient->getApiKey()])
                ->add('project', ChoiceType::class, [
                    'choices' => $this->productiveClient->getProjects(),
                    'data' => $this->productiveClient->getSelectedProject()])
                ->add('task_list', ChoiceType::class, [
                    'choices' => $this->productiveClient->getTaskList(),
                    'data' => $this->productiveClient->getSelectedTaskList()])
                ->add('file', FileType::class,[
                    'label' => 'csv file',
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '10240k',
                            'mimeTypes' => ['text/csv'],
                            'mimeTypesMessage' => 'Please upload a valid CSV document'
                        ])
                    ]
                ]);

        $builder->add('Continue',SubmitType::class);


    }


}