<?php

namespace App\Controller;

use App\View\HtmlView; // Fix the namespace import

class HomePageController
{
    private HtmlView $view;

    public function __construct()
    {
        $this->view = new HtmlView();
    }

    public function index(): void
    {
        $this->view->render('index');
    }
}