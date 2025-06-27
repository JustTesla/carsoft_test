<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/about', name: 'about')]
    public function index(): Response
    {
        return $this->render('about.html.twig');
    }

    #[Route('/blog-home', name: 'blog_home')]
    public function blogHome(): Response
    {
        return $this->render('blog_home.html.twig');
    }

    #[Route('/blog-post', name: 'blog_post')]
    public function blogPost(): Response
    {
        return $this->render('blog_post.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('contact.html.twig');
    }

    #[Route('/faq', name: 'faq')]
    public function faq(): Response
    {
        return $this->render('faq.html.twig');
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('pricing.html.twig');
    }

    #[Route('/portfolio-overview', name: 'portfolio_overview')]
    public function portfolioOverview(): Response
    {
        return $this->render('portfolio_overview.html.twig');
    }

    #[Route('/portfolio-item', name: 'portfolio_item')]
    public function portfolioItem(): Response
    {
        return $this->render('portfolio_item.html.twig');
    }
}