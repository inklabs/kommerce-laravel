<?php
namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return $this->renderTemplate(
            'home/index.twig',
            [
                'recommendedProducts' => $this->getRandomProducts(12),
            ]
        );
    }
}
