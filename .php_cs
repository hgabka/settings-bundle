<?php

$config = Symfony\CS\Config\Symfony23Config::create()
    ->level(\Symfony\CS\FixerInterface::NONE_LEVEL)
    ->fixers(
        [
            // Symfony level
            'psr0',
            'encoding',
            'short_tag',
            'braces',
            'elseif',
            'eof_ending',
            'function_declaration',
            'indentation',
            'line_after_namespace',
            'linefeed',
            'lowercase_constants',
            'lowercase_keywords',
            'method_argument_space',
            'multiple_use',
            'php_closing_tag',
            'trailing_spaces',
            'visibility',
            'array_element_no_space_before_comma',
            'array_element_white_space_after_comma',
            //'concat_without_spaces',
            'double_arrow_multiline_whitespaces',
            'duplicate_semicolon',
            'empty_return',
            'extra_empty_lines',
            'function_typehint_space',
            'include',
            'join_function',
            //'multiline_array_trailing_comma',
            'namespace_no_leading_whitespace',
            'new_with_braces',
            'object_operator',
            'operators_spaces',
            'phpdoc_indent',
            'phpdoc_params',
            'phpdoc_scalar',
            'phpdoc_separation',
            'print_to_echo',
            'remove_leading_slash_use',
            'remove_lines_between_uses',
            'return',
            'self_accessor',
            'single_array_no_trailing_comma',
            'single_quote',
            'spaces_before_semicolon',
            'spaces_cast',
            'standardize_not_equal',
            'ternary_spaces',
            'trim_array_spaces',
            'unused_use',
            'whitespacy_lines',
            // Custom
            'ordered_use',
            'short_array_syntax',
            'concat_with_spaces',
        ]
    );
$config->getFinder()
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'src')
;

return $config;
