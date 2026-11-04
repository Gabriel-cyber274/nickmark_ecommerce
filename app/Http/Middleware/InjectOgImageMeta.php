<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectOgImageMeta
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! str_contains($response->headers->get('Content-Type'), 'text/html')) {
            return $response;
        }

        $content = $response->getContent();

        if (str_contains($content, 'property="og:image"')) {
            return $response;
        }

        $logo = asset('assets/images/demos/demo-4/logo.png');

        $favicons = "\n    <link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"{$logo}\">\n    <link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"{$logo}\">\n    <link rel=\"icon\" type=\"image/png\" sizes=\"16x16\" href=\"{$logo}\">\n    <link rel=\"shortcut icon\" href=\"{$logo}\">";
        $ogImage = "\n    <meta property=\"og:image\" content=\"{$logo}\">";

        $headInjection = $favicons . $ogImage . "\n";

        $content = str_replace('</head>', $headInjection . "</head>", $content);

        $response->setContent($content);

        return $response;
    }
}
