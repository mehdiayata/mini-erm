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
        if ($data->getProduct()->getTransaction()->getId() == NULL) {

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
        if ($this->verifyBalance($data->getCompany(), $data->getProduct())) {
            $provider = $data->getProduct()->getProvider();
            $data->setProvider($provider);
            return $data;
        } else {
            throw new \Exception("Your balance is not high enough for buy this product");
        }
    }

    public function postTransactionClient(Transaction $data)
    {
        $company = $data->getProduct()->getCompany();
        $data->setCompany($company);
        return $data;
    }


    public function verifyBalance($company, $product)
    {
        $balance = $company->getBalance();

        $price = $product->getPrice() * $product->getTax();

        if ($balance >= $price) {
            return true;
        } else {
            return false;
        }
    }

}
