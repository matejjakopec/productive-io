<?php

namespace App\Controller;

use App\Form\Type\TaskType;
use App\Form\Type\TaskUploadType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TaskController extends AbstractController
{
    private $productiveClient;

    public function __construct(ProductiveClient $productiveClient)
    {
        $this->productiveClient = $productiveClient;
    }

    #[Route(path: '/upload-tasks', name: 'upload-tasks')]
    public function tasksFromFile(Request $request){

        $form = $this->createForm(TaskUploadType::class);

        $form->handleRequest($request);
        $data = $form->getData();
        if ($form->isSubmitted()) {
            $this->productiveClient->setApiKey($data['api_key']);
            $this->productiveClient->fetchProjects();
        }
        $form = $this->createForm(TaskUploadType::class);
        $form->handleRequest($request);
        $data = $form->getData();
        if ($form->isSubmitted()) {
            $this->productiveClient->setSelectedProject($data['project']);
            $this->productiveClient->fetchTaskList();
        }
        $form = $this->createForm(TaskUploadType::class);
        $form->handleRequest($request);
        $data = $form->getData();
        if ($form->isSubmitted()) {
            $this->productiveClient->setSelectedTaskList($data['task_list']);
            if($data['file']){
                $file = $data['file'];
                $file = fopen($file->getPathname(), 'r');
                while (! feof($file)) {
                    $csvArray[] = fgetcsv($file, 1000, ';');
                }
                $this->checkFile($csvArray);
                fclose($file);
                return $this->redirectToRoute('upload-tasks');
            }
        }
        return $this->render('base.html.twig',[
            'form' => $form
        ]);
    }


    private function checkFile(array $csvArray){
        $functionality = '';
        $module = '';
        $subModule = '';
        array_shift($csvArray);
        foreach ($csvArray as $row){
            if($row){
                $functionality = $row[0] == '' ? $functionality : $row[0];
                $module = $row[1] == '' ? $module : $row[1];
                $subModule = $row[2] == '' ? $subModule : $row[2];
                $title = $functionality . " :: " . $module . " :: " . $subModule;
                $description = $row[3];
                $estimated = str_replace(',', '.', $row[7]);
                $estimated = floatval($estimated) * 60.0;
                if($row[0] == "" && $row[0] == "" && $row[1] == "" && $row[2] == "" && $row[3] == "" && $row[4] == "" && $row[5] == "" && $row[6] == ""){
                    break;
                }
                $output[] = [$title, $description, $estimated];
            }
        }
        if($this->productiveClient->getApiKey() && $this->productiveClient->getSelectedProject() &&
            $this->productiveClient->getSelectedTaskList()){
            ini_set('max_execution_time', '300');
            set_time_limit(300);
            $this->productiveClient->makeTask($output);
        }
    }



}