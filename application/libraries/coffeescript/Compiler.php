<?php

namespace CoffeeScript;

Init::init();

/**
 * @package   CoffeeScript
 * @author    Alex Little
 * @license   MIT
 * @homepage  http://github.com/alxlit/coffeescript-php
 */
class Compiler
{

    /**
     * Compile some CoffeeScript.
     *
     * Available options:
     *
     *  'filename' => The source file, for debugging (formatted into error messages)
     *  'header'   => Add a header to the generated source (default: TRUE)
     *  'rewrite'  => Enable rewriting token stream (debugging)
     *  'tokens'   => Reference to token stream (debugging)
     *  'trace'    => File to write parser trace to (debugging)
     *
     * @param  string  The source CoffeeScript code
     * @param  array   Options (see above)
     *
     * @return string  The resulting JavaScript (if there were no errors)
     */
    static function compile($code, $options = array())
    {
        $lexer = new Lexer($code, $options);

        if (isset($options['filename'])) {
            Parser::$FILE = $options['filename'];
        }

        if (isset($options['tokens'])) {
            $tokens = & $options['tokens'];
        }

        if (isset($options['trace'])) {
            Parser::Trace(fopen($options['trace'], 'w', TRUE), '> ');
        }

        try {
            $parser = new Parser();

            foreach (($tokens = $lexer->tokenize()) as $token) {
                $parser->parse($token);
            }

            $js = $parser->parse(NULL)->compile($options);
        } catch (\Exception $e) {
            throw new Error("In {$options['filename']}, " . $e->getMessage());
        }

        if (!isset($options['header']) || $options['header']) {
            $js = '// Generated by CoffeeScript PHP ' . COFFEESCRIPT_VERSION . "\n" . $js;
        }

        return $js;
    }

}

?>
