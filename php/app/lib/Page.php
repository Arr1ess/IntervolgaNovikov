<?php

namespace app\lib;

use app\router\Response;
use handlers\UserHandler;

class Page
{
    protected array $data = [];
    protected array $scripts = [];
    protected array $styles = [];
    protected array $seoData = [];
    protected bool $isSinglePageAplication = false;

    public function __construct(protected string $filePath)
    {
        $queryString = $_SERVER['QUERY_STRING'] ?? '';
        parse_str($queryString, $queryParams);
        if (isset($queryParams['singlePageApplication']) && $queryParams['singlePageApplication'] === 'true') {
            $this->isSinglePageAplication = true;
        }
    }

    public function addScript(string $src, array $attributes = [])
    {
        $this->scripts[] = ['src' => "$src", 'attributes' => $attributes];
    }

    public function addStyle(string $href, array $attributes = [])
    {
        $this->styles[] = ['href' => "$href", 'attributes' => $attributes];
    }

    public function setSeoData(array $seoData)
    {
        $this->seoData = $seoData;
    }

    private function renderSeoTags(): string
    {
        $tagsHtml = '';
        if (!empty($this->seoData)) {
            $tagsHtml .= $this->renderMetaTags($this->seoData);
            $tagsHtml .= $this->renderLinkTags($this->seoData);
        }
        return $tagsHtml;
    }

    private function renderMetaTags(array $seoData): string
    {
        $metaTagsHtml = '';
        foreach ($seoData as $key => $value) {
            if (in_array($key, ['title', 'description', 'keywords'])) {
                $metaTagsHtml .= "<meta name=\"$key\" content=\"{$value}\">";
            }
        }
        return $metaTagsHtml;
    }

    private function renderLinkTags(array $seoData): string
    {
        $linkTagsHtml = '';
        if (!empty($seoData['fonts'])) {
            foreach ($seoData['fonts'] as $font) {
                $linkTagsHtml .= "<link rel=\"{$font['rel']}\" href=\"{$font['href']}\">";
            }
        }
        if (!empty($seoData['preconnect'])) {
            foreach ($seoData['preconnect'] as $preconnect) {
                $crossorigin = $preconnect['crossorigin'] ? ' crossorigin' : '';
                $linkTagsHtml .= "<link rel=\"preconnect\" href=\"{$preconnect['href']}\"$crossorigin>";
            }
        }
        return $linkTagsHtml;
    }

    private function renderScripts()
    {
        $scriptsHtml = '';
        $this->scripts = array_unique($this->scripts, SORT_REGULAR);
        foreach ($this->scripts as $script) {
            $attributes = '';
            foreach ($script['attributes'] as $name => $value) {
                $attributes .= " $name=\"$value\"";
            }
            $scriptsHtml .= "<script src=\"{$script['src']}\"$attributes></script>";
        }
        return $scriptsHtml;
    }
    public function addMetaTag(string $key, string $value)
    {
        $this->seoData[$key] = $value;
    }

    private function renderStyles()
    {
        $stylesHtml = '';
        $this->styles = array_unique($this->styles, SORT_REGULAR);
        foreach ($this->styles as $style) {
            $attributes = '';
            foreach ($style['attributes'] as $name => $value) {
                $attributes .= " $name=\"$value\"";
            }
            $stylesHtml .= "<link href=\"{$style['href']}\" rel=\"stylesheet\"$attributes>";
        }
        return $stylesHtml;
    }

    protected function getBody()
    {
        include $this->filePath;
    }
    public function addPreconnect($href, $crossorigin = false)
    {
        if (!isset($this->seoData['preconnect'])) {
            $this->seoData['preconnect'] = [];
        }
        $this->seoData['preconnect'][] = [
            'href' => $href,
            'crossorigin' => $crossorigin
        ];
    }

    public function addFont($href, $rel = 'stylesheet')
    {
        if (!isset($this->seoData['fonts'])) {
            $this->seoData['fonts'] = [];
        }
        $this->seoData['fonts'][] = [
            'href' => $href,
            'rel' => $rel
        ];
    }

    public function render()
    {
        ob_start();
        $this->getBody();
        $body = ob_get_clean();



        if ($this->isSinglePageAplication) {
            return Response::json(['body' => $body, 'scripts' => $this->scripts, 'styles' => $this->styles, 'seoTags' => $this->seoData]);
        } else {
            $seoTags = $this->renderSeoTags();
            $scripts = $this->renderScripts();
            $styles = $this->renderStyles();

            $page = <<<HTML
            <!DOCTYPE html>
            <html lang="ru">
            <head>
                $seoTags
                $styles
            </head>
            <body>
                $body
                $scripts
            </body>
            </html>
            HTML;

            return Response::html($page);
        }
    }

    public static function __set_state($array)
    {
        $instance = new self($array['filePath']);
        return $instance;
    }
}
