<?php

/**
 * IDE Resolution Noop
 * /
if (!function_exists('add_action')) {
    function add_action(string $hook, callable $function, ?int $priority = null, ?int $args = null) {}
}



/**/

