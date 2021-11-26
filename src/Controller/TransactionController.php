<?php

namespace App\Controller;

use Exception;
use App\Entity\Transaction;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class TransactionController extends AbstractController
{

    public function __construct(private ProductRepository $productRepository)
    {
        
    }

    public function __invoke(Transaction $data, Request $request)
    {
        // Vérifie si le produit est déjà dans une transaction
        $getProductTransaction = $this->productRepository->findIfTransaction($data->getProduct()->getId());
      

        if ($getProductTransaction == false) {
            // Si le stock est à jour
            if ($this->verifyStock($data->getProduct(), $data->getQuantity())) {

                // Si la transactione est entre le client et l'entreprise
                if ($request->getUri() == 'http://127.0.0.1:8000/api/transactions/clients') {
                    $data = $this->postTransactionClient($data);
                } else if ($request->getUri() == 'http://127.0.0.1:8000/api/transactions/providers') {
                    $data = $this->postTransactionProvider($data);
                }

                $data->setProduct($this->updateStock($data->getProduct(), $data->getQuantity()));

                return $data;
            } else {
                return throw new \Exception("This quantity of product is inferior as stock");
            }
        } else {
            return throw new \Exception("The product is already sold");
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

    public function verifyStock($product, $quantity)
    {
        if ($product->getStock() > $quantity) {
            return true;
        } else {
            return false;
        }
    }

    public function updateStock($product, $quantity)
    {
        $product->setStock($product->getStock() - $quantity);

        return $product;
    }

}
