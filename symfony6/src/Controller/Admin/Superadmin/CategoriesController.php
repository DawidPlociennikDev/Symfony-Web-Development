<?php

namespace App\Controller\Admin\Superadmin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Utils\CategoryTreeListAdmin;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/admin")
 */

class CategoriesController extends AbstractController
{

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/su/categories', name: 'categories', methods: ['GET', 'POST'])]
    public function categories(CategoryTreeListAdmin $categories, Request $request): Response
    {
        $categories->getCategoryList($categories->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;

        if ($this->saveCategory($category, $form, $request)) 
        {   
            return $this->redirectToRoute('categories');
        } 
        elseif($request->isMethod('post'))
        {
            $is_invalid = ' is-invalid';
        }

        return $this->render('admin/categories.html.twig', [
            'categories' => $categories->categoryList,
            'form' => $form->createView(),
            'is_invalid' => $is_invalid
        ]);
    }

    #[Route('/su/edit-category/{id}', name: 'edit_category', methods: ['GET', 'POST'])]
    public function editCategory(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $is_invalid = null;

        if ($this->saveCategory($category, $form, $request)) 
        {   
            return $this->redirectToRoute('categories');
        } 
        elseif($request->isMethod('post'))
        {
            $is_invalid = ' is-invalid';
        }
        
        return $this->render('admin/edit_category.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'is_invalid' => $is_invalid
        ]);
    }

    #[Route('/su/delete-category/{id}', name: 'delete_category')]
    public function deleteCategory(Category $category): Response
    {
        $this->manager->remove($category);
        $this->manager->flush();
        return $this->redirectToRoute('categories');
    }

    private function saveCategory(Category $category, $form, Request $request) : bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {   
            $category->setName($request->request->all('category')['name']);
            $repository = $this->manager->getRepository(Category::class);
            $parent = $repository->find($request->request->all('category')['parent']);
            $category->setParent($parent);

            $this->manager->persist($category);
            $this->manager->flush();

            return true;
        } 
        return false;
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/_all_categories.html.twig', [
            'categories' => $categories,
            'editedCategory' => $editedCategory
        ]);
    }
}
