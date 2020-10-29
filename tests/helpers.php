<?php

use Symfony\Component\VarDumper\VarDumper;

if (! function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd(...$arguments)
    {
        foreach ($arguments as $argument) {
            VarDumper::dump($argument);
        }

        exit(1);
    }
}
