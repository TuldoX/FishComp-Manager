<?php


namespace pwa\Controller;

class CatchesController
{
    public function index(): void
    {
        // Render the home page
        $htmlView = new \pwa\View\HtmlView();
        $htmlView->render('ulovky.html');
    }

}