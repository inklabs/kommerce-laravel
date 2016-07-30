<?php

namespace App\Http\Middleware;

use Closure;
use inklabs\KommerceTemplates\Lib\TwigTemplate;

class RenderTwig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        $action = $request->route()->getAction();
        $actionName = array_get($action, 'as');

        $response->header('Content-Type', 'text/html');
        $response->setContent(
            $this->getTwig()->render(
                $actionName . '.twig',
               $response->getOriginalContent()
            )
        );

        return $response;
    }

    protected function getTwig()
    {
        $baseThemePath = __DIR__ . '/../../../vendor/inklabs/kommerce-templates/themes/base/templates';

        $twigTemplate = new TwigTemplate([
            $baseThemePath,
        ]);

        $twigTemplate->enableDebug();

        return $twigTemplate->getTwigEnvironment();
    }
}
