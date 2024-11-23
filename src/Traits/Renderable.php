<?php

namespace App\Traits;

trait Renderable
{
    /**
     * Render a Mustache template
     *
     * @param string $template Template name
     * @param array $data Data to inject into the template
     * @return string Rendered content
     */
    public function render($template, $data = []): string
    {
        global $mustache;

        try {
            $tpl = $mustache->loadTemplate($template);
            return $tpl->render($data); // Return the rendered content
        } catch (\Exception $e) {
            throw new \RuntimeException("Error rendering template: {$e->getMessage()}");
        }
    }
}
