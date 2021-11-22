<?php

namespace App\Controller;

use Exception;
use App\Entity\Transaction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class TransactionController extends AbstractController
{

    public function __construct()
    {
    }

    public function __invoke(Transaction $data, Request $request)
    {
        if ($data->getProduct()->getTransaction() == NULL) {
            if ($request->getUri() == 'http://127.0.0.1:8000/api/transactions/clients') {
                $data = $this->postTransactionClient($data);
            } else if ($request->getUri() == 'http://127.0.0.1:8000/api/transactions/providers') {
                $data = $this->postTransactionProvider($data);
            }

            return $data;
        } else {
            throw new \Exception('Impossible create new transaction for product');
        }

    }

    public function postTransactionProvider(Transaction $data)
    {
        
        $provider = $data->getProduct()->getProvider();
        $data->setProvider($provider);
        return $data;
    }

    public function postTransactionClient(Transaction $data)
    {
        $company = $data->getProduct()->getCompany();
        $data->setCompany($company);
        return $data;
    }

}
