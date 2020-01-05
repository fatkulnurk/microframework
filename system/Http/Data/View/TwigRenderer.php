<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TwigRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
        $loader = new \Twig\Loader\ArrayLoader([
            'theme' => $templateString,
        ]);
        $twig = new \Twig\Environment($loader);

        try {
            return $twig->render('theme', $arguments);
        } catch (LoaderError $e) {
            die($e);
        } catch (RuntimeError $e) {
            die($e);
        } catch (SyntaxError $e) {
            die($e);
        }
    }
}