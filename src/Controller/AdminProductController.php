<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Type\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProductController extends AbstractController
{
    #[Route('/admin/product', name: 'app_admin_product')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin_product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/admin/product/add', name: 'app_admin_product_add')]
    public function addProduct(Request $request, SluggerInterface $slugger, ProductRepository $productRepository): Response{

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
            $slug = $slugger->slug($product->getName());
            $product->setCreatedAt(new \DateTimeImmutable)
            ->setSlug($slug);

            $productRepository->save($product);

            return $this->redirectToRoute('app_admin_product');
        }
        return $this->render('admin_product/addProduct.html.twig', [
            'form' => $form
        ]);
    }
}
