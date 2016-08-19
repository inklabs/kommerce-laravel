<?php
namespace App\Http\Controllers;

class PageController extends Controller
{
    public function privacy()
    {
        $this->displayTemplate('page/privacy.twig');
    }

    public function terms()
    {
        $this->displayTemplate('page/terms.twig');
    }

    public function contact()
    {
        echo 'TODO: Contact';
    }
}
