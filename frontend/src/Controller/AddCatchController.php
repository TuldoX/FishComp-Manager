<?php

namespace App\Controller;
use App\View\HtmlView;

class AddCatchController
{
    public function index(): void
    {
        $htmlView = new HtmlView();

        $htmlView->render('pridanie_ulovku');
    }

}