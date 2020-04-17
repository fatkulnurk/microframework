<?php
namespace Fatkulnurk\Microframework\Http\Data\View;

use Fatkulnurk\Microframework\System\Core\Exception\TemplateEngineNotFoundException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TwigRenderer implements TemplateRenderer
{
    public function render(string $templateString, array $arguments = []): string
    {
//        $loader = new \Twig\Loader\ArrayLoader([
//            'theme' => $templateString,
//        ]);
//        $twig = new \Twig\Environment($loader);


        if (class_exists(\Twig\Loader\FilesystemLoader::class)) {
            $loader = new \Twig\Loader\FilesystemLoader(\Fatkulnurk\Microframework\App::getInstance()
                    ->getPath() . "./../src/views");
            $twig = new \Twig\Environment($loader);

            try {
                return $twig->render($templateString, $arguments);
//            return $twig->render('theme', $arguments); // if use array loader
            } catch (LoaderError $e) {
                die($e);
            } catch (RuntimeError $e) {
                die($e);
            } catch (SyntaxError $e) {
                die($e);
            }
        }
        throw new TemplateEngineNotFoundException('Twig not installed');

    }
}
