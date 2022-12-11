<?php

namespace App\ApiHandler;

use App\Controller\ProductiveClient;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ApiHandler
{

    public function __invoke(ProductiveClient $productiveClient)
    {
        foreach ($productiveClient->getData() as $datum){
            $productiveClient->getClient()->request('POST', 'https://api.productive.io/api/v2/tasks', [
                'json' => $productiveClient->getRequestBody($datum),
                'headers' => [
                    'Content-Type' => 'application/vnd.api+json',
                    'X-Auth-Token' => $productiveClient->getApiKey(),
                    'X-Organization-Id' => $productiveClient->getOrganizationID(),
                ],
            ]);
        }
    }

}