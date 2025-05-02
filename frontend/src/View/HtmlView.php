<?php

namespace App\View;

use InvalidArgumentException;

class HtmlView
{
    private string $templatePath;

    public function __construct()
    {
        // Set the template directory path
        $this->templatePath = dirname(__DIR__) . '/templates';
    }

    public function render(string $template, array $data = []): void
    {
        // Resolve full path for .html files
        $templateFile = $this->templatePath . '/' . $template . '.html';

        // Check if the file exists and is readable
        if (!file_exists($templateFile) || !is_readable($templateFile)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Template file not found or is not readable: %s (Resolved path: %s)',
                    $template,
                    $templateFile
                )
            );
        }

        // Output the HTML file (no PHP processing)
        readfile($templateFile);
    }
}