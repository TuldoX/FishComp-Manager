<?php


namespace pwa\Controller;

class DashboardController
{
    public function index(): void
    {
        // Render the home page
        $htmlView = new \pwa\View\HtmlView();
        $htmlView->render('dashboard.html');
    }

}