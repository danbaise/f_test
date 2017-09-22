<?php

namespace App\Core;

class Pipeline
{
    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * @var int
     */
    protected $request;

    // Get the initial slice
    function getInitialSlice(\Closure $destination)
    {
        return function ($passable) use ($destination) {
            return call_user_func($destination, $passable);
        };
    }

    // Get the slice in every step.
    function getSlice()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                ;
                /**
                 * @var Middleware $pipe
                 */
                return call_user_func_array([$pipe, 'handle'], [$passable, $stack]);
            };
        };
    }

    // When process the Closure, send it as parameters. Here, input an int number.
    function send($request)
    {
        $this->request = $request;
        return $this;
    }

    // Get the middlewares array.
    function through(array $middlewares)
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    // Run the Filters.
    function then(\Closure $destination)
    {
        $firstSlice = $this->getInitialSlice($destination);
        $pipes = array_reverse($this->middlewares);
        $run = array_reduce($pipes, $this->getSlice(), $firstSlice);
        return call_user_func($run, $this->request);
    }
}