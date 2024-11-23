<?php

namespace App\Controllers;

use App\Traits\Renderable;

class BaseController
{
    use Renderable;

    protected function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function renderPage(string $templateName, array $data): string
    {
        // Render the template
        $content = $this->render($templateName, $data);

        // Combine the rendered content with the layout data
        $layoutData = array_merge($data, [
            'content' => $content,
        ]);

        // Render the layout
        return $this->render('layout', $layoutData);
    }

    protected function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }

    protected function setSessionMessage($message, $type)
    {
        $_SESSION['msg'] = $message;
        $_SESSION['msg_type'] = $type;
    }
}