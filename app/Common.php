<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

if (!function_exists('render_backtrace')) {
    /**
     * Compatibility shim for older CodeIgniter exception handlers.
     */
    function render_backtrace(array $backtrace): string
    {
        $backtraces = [];

        foreach ($backtrace as $index => $trace) {
            $frame = $trace + ['file' => '[internal function]', 'line' => 0, 'class' => '', 'type' => '', 'args' => []];

            if ($frame['file'] !== '[internal function]') {
                $frame['file'] = sprintf('%s(%s)', $frame['file'], $frame['line']);
            }

            unset($frame['line']);
            $idx = str_pad((string) ($index + 1), 2, ' ', STR_PAD_LEFT);

            $args = implode(', ', array_map(static fn($value): string => match (true) {
                is_object($value) => sprintf('Object(%s)', $value::class),
                is_array($value) => $value !== [] ? '[...]' : '[]',
                $value === null => 'null',
                is_resource($value) => sprintf('resource (%s)', get_resource_type($value)),
                default => var_export($value, true),
            }, $frame['args']));

            $backtraces[] = sprintf(
                '%s %s: %s%s%s(%s)',
                $idx,
                clean_path($frame['file']),
                $frame['class'],
                $frame['type'],
                $frame['function'],
                $args,
            );
        }

        return implode("\n", $backtraces);
    }
}
