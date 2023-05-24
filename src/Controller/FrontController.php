<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class FrontController extends AbstractController
{
    #[Route('/', name: 'app_front')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('front/pages/index.html.twig', [
            'products' => $products
        ]);
    }
    #[Route('/product/{slug}', name: 'app_product_detail')]
    public function productDetail(Product $product = null):Response{
        if($product == null){
            throw new NotFoundHttpException;
        }
        return $this->render('front/pages/productDetail.html.twig',  [
            'product' => $product
        ]);
    }
    #[Route('/categories', name:'app_categories')]
    public function categories(CategoryRepository $categoryRepository): Response{
        $categories = $categoryRepository->findAll();

        return $this->render('front/pages/categories.html.twig',[
            'categories' => $categories
        ]);
    }

    #[Route('/category/{slug}', name: 'app_category')]
    public function category(Category $category = null): Response
    {
        if($category == null){
            throw new NotFoundHttpException;
        }
        return $this->render('front/pages/category.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/pages/{page}', name: 'app_static_page')]
    public function staticPage($page, Environment $twig): Response
    {
        $template = 'front/pages/' . $page . '.html.twig';
        return $this->render($template, [
           dump($page)
        ]);
    }
}
