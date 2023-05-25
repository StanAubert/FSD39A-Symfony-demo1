<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class AdminCategoryController extends AbstractController
{
    #[Route('/category', name: 'app_admin_category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('admin/category.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/add', name: 'app_admin_category_add')]
    #[Route('/category/edit/{id<\d+>}', name: 'app_admin_category_edit')]
    public function add(Category $category = null, Request $request, SluggerInterface $sluggerInterface, CategoryRepository $categoryRepository ): Response
    {
        if($category == null)
            $category = new Category();
       
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $category->setSlug($sluggerInterface->slug($category->getName()));

            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_admin_category');
        }

        return $this->render('admin/addCategory.html.twig', [
            'form'=> $form,
            'category'=>$category,
            'edit'=> empty($category->getId())?false:true
        ]);
    }
}
