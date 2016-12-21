<?php
namespace App\Http\Controllers;

class PageController extends Controller
{
    public function privacy()
    {
        return $this->renderTemplate('@theme/page/privacy.twig');
    }

    public function terms()
    {
        return $this->renderTemplate('@theme/page/terms.twig');
    }

    public function contact()
    {
        echo 'TODO: Contact';
    }
}
