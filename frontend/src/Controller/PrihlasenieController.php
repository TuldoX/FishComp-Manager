<?php


namespace App\Controller;
use App\View\HtmlView;
class PrihlasenieController
{
    public function index(): void
    {
        // Render the home page
        $htmlView = new HtmlView();
        $htmlView->render('prihlasenie');
    }

}