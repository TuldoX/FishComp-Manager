<?php


namespace App\Controller;
use App\View\HtmlView;
class CatchesController
{
    public function index(): void
    {
        // Render the home page
        $htmlView = new HtmlView();
        $htmlView->render('ulovky');
    }

}