<?php
namespace App\Http\Controllers;

class PageController extends Controller
{
    public function privacy()
    {
        return $this->renderTemplate('page/privacy.twig');
    }

    public function terms()
    {
        return $this->renderTemplate('page/terms.twig');
    }

    public function contact()
    {
        echo 'TODO: Contact';
    }
}
