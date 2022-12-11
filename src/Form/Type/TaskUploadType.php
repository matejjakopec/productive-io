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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class TaskUploadType extends AbstractType
{
    private ProductiveClient $productiveClient;

    public function __construct(ProductiveClient $productiveClient)
    {
        $this->productiveClient = $productiveClient;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('apiKey',TextType::class);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event){
            $data = $event->getData();
            $form = $event->getForm();
            if(!empty($data['apiKey'])){
                $this->productiveClient->setApiKey($data['apiKey']);
                $this->productiveClient->fetchProjects();
                if(!empty($data['projects'])){
                    $this->productiveClient->setSelectedProject($data['projects']);
                }
                $form->add('projects', ChoiceType::class, [
                    'choices' => $this->productiveClient->getProjects(),
                    'data' => $this->productiveClient->getSelectedProject()]);
            }
            if(!empty($data['projects'])){
                $this->productiveClient->setSelectedProject($data['projects']);
                $this->productiveClient->fetchTaskList();
                $form->add('taskList', ChoiceType::class, [
                    'choices' => $this->productiveClient->getTaskList(),
                    'data' => $this->productiveClient->getSelectedTaskList()]);
            }
            if(!empty($data['taskList'])){
                $this->productiveClient->setSelectedTaskList($data['taskList']);
                $form->add('file', FileType::class,[
                    'label' => 'csv file',
                    'required' => false,
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '10240k',
                        ])
                    ]
                ]);
            }
            if(!empty($data['file'])){
                $this->productiveClient->setFile($data['file']);
            }
        });

        $builder->add('Continue',SubmitType::class);
    }




    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => ProductiveClient::class]);
    }


}