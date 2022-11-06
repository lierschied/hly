<?php

namespace Core\Application;

use JetBrains\PhpStorm\ArrayShape;

class ViewCompiler
{
    //TODO: think of a way to capture content between starting and ending 'directives'
    //and context based slots e.g. named slots

    /**
     * Searching all @ tags and only try to execute those
     * preg_match_all('/@(\w+)(?>\((.*)\))?/', $file, $matches);
     * 0 => directive name
     */
    private static function find($file): array
    {
        preg_match_all('/(?<=@)\w+/', $file, $matches);
        return array_unique($matches[0]);
    }

    public static function compile($file): string
    {
        $file = self::compileCurlyBrackets($file);
        foreach (self::find($file) as $directive) {
            ['regex' => $regex, 'replace' => $replace] = self::$directive();
            $file = preg_replace($regex, $replace, $file);
        }
        return $file;
    }

    private static function compileCurlyBrackets($file): array|string|null
    {
        return preg_replace("/{{\s([^{}]+)\s}}/", '<?php echo htmlspecialchars($1); ?>', $file);
    }

    #[ArrayShape(['regex' => "string", 'replace' => "string"])] private static function if(): array
    {
        return [
            'regex' => '/@if\((.*)\)/',
            'replace' => '<?php if ($1) { ?>'
        ];
    }

    #[ArrayShape(['regex' => "string", 'replace' => "string"])] private static function else(): array
    {
        return [
            'regex' => '/@else/',
            'replace' => '<?php } else { ?>'
        ];
    }

    #[ArrayShape(['regex' => "string", 'replace' => "string"])] private static function elseif(): array
    {
        return [
            'regex' => '/@elseif\((.*)\)/',
            'replace' => '<?php } else if($1) { ?>'
        ];
    }

    #[ArrayShape(['regex' => "string", 'replace' => "string"])] private static function endif(): array
    {
        return [
            'regex' => '/@endif/',
            'replace' => '<?php } ?>'
        ];
    }

    #[ArrayShape(['regex' => "string", 'replace' => "string"])] private static function auth(): array
    {
        return [
            'regex' => '/@auth/',
            'replace' => '<?php if (session()->user()->isLoggedIn()) { ?>'
        ];
    }

    #[ArrayShape(['regex' => "string", 'replace' => "string"])] private static function endauth(): array
    {
        return [
            'regex' => '/@endauth/',
            'replace' => '<?php } ?>'
        ];
    }

    #[ArrayShape(['regex' => "string", 'replace' => "string"])] private static function component(): array
    {
        return [
            'regex' => '/@component\((.*),\s(.*)\)/',
            'replace' => '<?php echo \Core\Application\View::render("Components/".$1, $2) ?>'
        ];
    }
}