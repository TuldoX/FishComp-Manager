<?php


namespace pwa\Controller;

class AddCatchController
{
    public function index(): void
    {
        // Render the home page
        $htmlView = new \pwa\View\HtmlView();
        $htmlView->render('pridanie_ulovku.html');
    }

}