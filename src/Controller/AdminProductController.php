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

#[Route('/admin')]
class AdminProductController extends AbstractController
{
    #[Route('/product', name: 'app_admin_product')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('admin/products.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/add', name: 'app_admin_product_add')]
    #[Route('/product/edit/{id<\d+>}', name: 'app_admin_product_edit')]
    public function add(Product $product = null, Request $request, SluggerInterface $sluggerInterface, ProductRepository $productRepository): Response
    {
        if($product == null)
            $product = new Product();
       
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            if($product->getId()==null)
                $product->setCreatedAt(new \DateTimeImmutable());
            else
                $product->setModifiedAt(new \DateTimeImmutable()); 

            $product->setSlug($sluggerInterface->slug($product->getName()));

            $productRepository->save($product, true);

            return $this->redirectToRoute('app_admin_product');
        }

        return $this->render('admin/addProduct.html.twig', [
            'form'=> $form,
            'product'=>$product,
            'edit'=> empty($product->getId())?false:true
        ]);
    }

    #[Route('product/delete/{id<\d+>}', name:'app_admin_product_delete')]
        public function delete(Request $request, ProductRepository $productRepository, Product $product): Response
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
        $productRepository->remove($product, true);
        }
        return $this->redirectToRoute('app_admin_product');
    }
}
