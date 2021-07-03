<?php
/** @noinspection PhpUnnecessaryCurlyVarSyntaxInspection */

/** @noinspection PhpUnusedPrivateMethodInspection */

use voku\helper\HtmlDomParser as Parser;
use voku\helper\SimpleHtmlDomBlank as NullNode;
use voku\helper\SimpleHtmlDomInterface as Node;

/*
 * SPX uses `translateMachinely()` to determine contents to be translated.
 * (converter.ts)
 * Elements which use this function are the contents that need to be translated.
 * So, we need to manage an array, holding these critical elements.
 * Let JavaScript do the replace work.
 */

class Converter {
    private const MONOSPACED = 'Monaco,Menlo,Consolas,"Courier New",monospaced';
    private const NAMED = true;

    private string $title;
    private string $url;
    private string $author;
    private string $translator;
    private DateTime $date;

    private bool $complete = false;
    private array $contents = []; // critical nodes
    private Parser $dom;
    private int $counter = 0;

    function __construct(string $title, string $url, string $translator, DateTime $date, Parser $dom) {
        $this->title = $title;
        $this->url = $url;
        $this->translator = $translator;
        $this->date = $date;
        $this->dom = $dom;
    }

    function get_result(): array {
        $result = $this->parse_head() . $this->parse_body() . '[/indent]';

        return [$this->simplify($result), $this->contents];
    }

    // Utility functions.
    private function parse_head(): string {
        $ret = "[postbg]bg5.png[/postbg][indent]";

        $img = $this->dom->findOne(".article-head__image");
        if (!($img instanceof NullNode)) {
            $ret .= $this->convert($img);
        }

        return $ret;
    }

    private function parse_body(): string {
        $footer = $this->convert($this->dom->findOne('.attribution__details'));

        return $this->unwrap($this->dom->findOne(".article-body")) . $footer;
    }

    private function simplify(string $content): string {
        do {
            $ret = $content;
            $content = preg_replace('/\[(\w+)(?:=\w+)?](\s*)\[\/\1]/', '\2', $ret);
        } while ($content !== $ret);

        return $ret;
    }

    private function count(Node $node): string {
        $this->contents[] = $node->text;
        return '{' . $this->counter++ . '}';
    }

    private function unwrap(Node $node): string {
        $ret = '';
        foreach ($node->children() as $child) {
            if (!$this->complete) $ret .= $this->convert($child);
        }
        return $ret;
    }

    private function src_url(string $url): string {
        return str_starts_with($url, '/') ? 'https://www.minecraft.net' . $url : $url;
    }

    // Convert functions.
    private function a(Node $node): string {
        $url = $this->src_url($node->getAttribute('href'));
        $unwrapped = $this->unwrap($node);
        return $url ? " [url=$url][color=#388d40]" . $unwrapped . '[/color][/url] ' : $unwrapped;
    }

    private function b(Node $node): string {
        return '[b]' . $this->unwrap($node) . '[/b]';
    }

    private function blockquote(Node $node): string {
        return $this->unwrap($node);
    }

    private function br(): string {
        return "\n";
    }

    private function cite(Node $node): string {
        return '-- ' . $this->unwrap($node);
    }

    private function code(Node $node): string {

        return '[backcolor=White][font=' . self::MONOSPACED . ']'
            . $this->unwrap($node)
            . '[/font][/backcolor]';
    }

    private function div(Node $node): string {
        $ret = $this->unwrap($node);
        $cl = $node->classList;

        if ($cl->contains('article-social') || $cl->contains("preloader")) {
            return '';
        } elseif ($cl->contains('article-image-carousel__caption')) {
            // Image description
            $ret = preg_replace('/\n/', '', $ret);
            return "[/indent][align=center][b]{$ret}[/b][/align][indent]\n";
        } elseif ($cl->contains('video')) {
            return "\n[/indent][align=center]
            【REPLACE HTTPS URL[media]XXX[/media]】[/align][indent]\n";
        } elseif ($cl->contains('quote') || $cl->contains('attributed-quote')) {
            return "\n[quote]\n{$ret}\n[/quote]\n";
        } elseif ($cl->contains('text-center')) {
            // End of the content
            return "[/indent][align=center]{$ret}[/align][indent]\n";
        } else {
            return $ret;
        }
    }

    private function dl(Node $node): string {
        $ret = "\n\n" . $this->unwrap($node);
        if (self::NAMED) $ret .= "\n[color=DarkGrey][i]Powered by [color=#388d40][u]4Mbps[/u][/color][/i][/color]\n";
        return $ret;
    }

    private function dd(Node $node): string {
        if ($node->classList->contains('pubDate')) {
            return '[b]「' . $this->translator . ' 译自 [url=' . $this->url . '][color=#388d40][u]Minecraft.net [i]'
                . $this->title . '[/i][/u][/color][/url] (' . $this->date->format('Y/m/d') . ') - '
                . $this->author
                . '」[/b]';
        } else {
            // Author
            $this->author = $this->unwrap($node);
            return '';
        }
    }

    // h1, h2, h3, h4, h5
    private function h(int $level, Node $node): string {
        $unwrapped = $this->unwrap($node);

        switch (strtolower($unwrapped)) {
            case 'get the release':
            case 'get the pre-release':
            case 'get the snapshot':
            case 'get the release candidate':
                $this->complete = true;
                return '';
        }

        $prefix = '[size=' . (7 - $level) . '][b]';
        static $SUFFIX = "[/b][/size]\n";
        return "\n{$prefix}[color=Silver]"
            . preg_replace('/#388d40/', 'Silver', $unwrapped)
            . "[/color]{$SUFFIX}{$prefix}"
            . $this->count($node)
            . $SUFFIX;
    }

    private function i(Node $node): string {
        return '[i]' . $this->unwrap($node) . '[/i]';
    }

    private function img(Node $node): string {
        if ($node->getAttribute('alt') === 'Author image') {
            $this->complete = true;
            return '';
        }

        $src = $this->src_url($node->getAttribute('src'));

        if ($node->classList->contains('attributed-quote__image'))
            // SPX: Attributed quote author avatar.
            return "\n[float=left][img]{$src}[/img][/float]";
        else
            return "\n\n[/indent][align=center][img]{$src}[/img][/align][indent]\n";
    }

    private function li(Node $node): string {
        return '[*][color=Silver]'
            . preg_replace('/#388d40/', 'Silver', $this->unwrap($node))
            . "[/color]\n[*]"
            . $this->count($node)
            . "\n";
    }

    private function ol(Node $node): string {
        return "\n[list=1]\n" . $this->unwrap($node) . "[/list]\n";
    }

    private function p(Node $node): string {
        $ret = $this->unwrap($node);

        if (empty(trim($ret))) return '';

        if ($node->classList->contains('lead')) {
            return "[size=4][b][size=2][color=Silver]{$ret}[/color][/size][/b][/size]\n[size=4][b]"
                . $this->count($node)
                . "[/b][/size]\n\n[size=3][color=DimGray]"
                . $this->author
                . "[/color][/size]\n\n";
        } else {
            return '[color=Silver]'
                . preg_replace('/#388d40/', 'Silver', $ret)
                . "[/color]\n"
                . $this->count($node)
                . "\n\n";
        }
    }

    private function span(Node $node): string {
        $ret = $this->unwrap($node);

        if ($node->classList->contains('bedrock-server')) {
            // Code
            return '[backcolor=White][font=' . self::MONOSPACED . "][color=#7824c5]{$ret}[/color][/font][/backcolor]";
        } elseif ($node->classList->contains('strikethrough')) {
            return "[s]{$ret}[/s]";
        }
        return $ret;
    }

    private function table(Node $node): string {
        return $this->unwrap($node);
    }

    private function tbody(Node $node): string {
        return "\n[table]\n"
            . $this->unwrap($node)
            . "[/table]\n";
    }

    private function td(Node $node): string {
        return '[td]' . $this->unwrap($node) . '[/td]';
    }

    private function tr(Node $node): string {
        return '[tr]' . $this->unwrap($node) . '[/tr]';
    }

    private function ul(Node $node): string {
        return "\n[list]\n" . $this->unwrap($node) . "[/list]\n";
    }

    private function convert(Node $node): string {
        // More magical impl in less than 20 lines.
        // By KawaiiZapic

        $tag = $node->tag;
        $alias = [
            'strong' => 'b',
            'section' => 'div',
            'em' => 'i'
        ];
        $tag = str_replace(array_keys($alias), array_values($alias), $tag);
        return match ($tag) {
            'a', 'b', 'blockquote', 'br', 'cite', 'code',
            'div', 'dd', 'dl', 'i', 'img', 'li', 'ol',
            'p', 'span', 'table', 'tbody', 'td', 'tr', 'ul' => $this->$tag($node),
            'h1', 'h2', 'h3', 'h4', 'h5' => $this->h(substr($tag, 1), $node),
            'dt', 'script', 'button', 'nav', 'svg' => '',
            '#text', 'picture' => $node->text,
            // print_r always return true
            default => (print_r("<b style='color: Red'>Warning: 4Mbps does not know how to deal with <{$node->tag}> tag.</b>") ? $node->text : '')
        };
    }
}
