<?php


namespace pwa\Controller;

class PrihlasenieController
{
    public function index(): void
    {
        // Render the home page
        $htmlView = new \pwa\View\HtmlView();
        $htmlView->render('prihlasenie.html');
    }

}