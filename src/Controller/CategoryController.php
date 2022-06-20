<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{

    protected $categoryRepository;
    protected $request;
    protected $slugger;
    protected $em;

    public function __construct(CategoryRepository $categoryRepository, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $this->categoryRepository = $categoryRepository;
        $this->slugger = $slugger;
        $this->em = $em;
    }

    public function renderMenuList()
    {
        $categories = $this->categoryRepository->findAll();

        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request)
    {

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setSlug(strtolower($this->slugger->slug($category->getName())));

            $this->em->persist($category);
            $this->em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, Request $request)
    {

        $category = $this->categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("Cette catégory n'existe pas");
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($this->slugger->slug($category->getName())));

            $this->em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/category/list", name="category_list")
     */
    public function list()
    {

        $categories = $this->categoryRepository->findAll();

        return $this->render('category/list.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/category/list/remove/{id}", name="category_remove", requirements={"id":"\d+"})
     */
    public function remove($id, ProductRepository $productRepository)
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("Cette catégory n'existe pas");
        }

        $products = $productRepository->findBy(['category' => $id]);

        if (count($products) > 0) {
            foreach ($products as $product) {
                $this->em->remove($product);
            }
        }

        $this->em->remove($category);
        $this->em->flush();

        return $this->redirectToRoute('category_list');
    }
}