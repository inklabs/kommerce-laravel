<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use inklabs\KommerceTemplates\Lib\TwigTemplate;

class RenderTwig
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        if ($this->responseCanBeRenderedWithTwig($response)) {
            $action = $request->route()->getAction();
            $actionName = array_get($action, 'as');

            $response->header('Content-Type', 'text/html');
            $response->setContent(
                $this->getTwig()->render(
                    $actionName . '.twig',
                    $response->getOriginalContent()
                )
            );
        }

        return $response;
    }

    /**
     * @return \Twig_Environment
     */
    private function getTwig()
    {
        $baseThemePath = __DIR__ . '/../../../vendor/inklabs/kommerce-templates/themes/base/templates';

        $twigTemplate = new TwigTemplate([
            $baseThemePath,
        ]);

        $twigTemplate->enableDebug();

        return $twigTemplate->getTwigEnvironment();
    }

    /**
     * @param Response $response
     * @return bool
     */
    private function responseCanBeRenderedWithTwig(Response $response)
    {
        return ! $response->isRedirection() && ! $response->isServerError();
    }
}
