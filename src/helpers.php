<?php

if ( ! function_exists('component')) {

    /**
     * Get a component model.
     *
     * @param string|null $key
     * @param string|array|null $name
     * @return \Indigoram89\Components\Component
     */
    function component(string $key = null, $name = null) {

        $model = app('component');

        if (is_null($key)) {
            return $model;
        }

        $component = $model->firstOrCreate(compact('key'));

        $name && $component->name($name);

        return $component;
    }
}

if ( ! function_exists('page')) {

    /**
     * Get a model of the current page component.
     *
     * @param string|null $key
     * @return \Indigoram89\Components\Component
     */
    function page(string $key = null) {

        return component()->page($key);
    }
}
