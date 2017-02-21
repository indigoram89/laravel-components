<?php

namespace Indigoram89\Components;

use Route;
use InvalidArgumentException;
use Indigoram89\Fields\HasFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Component extends Model
{
    use HasFields;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'laravel_components';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['visible' => 'boolean'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];

    /**
     * Get a model of the current page component.
     *
     * @param string|null $key
     * @return self|null
     */
    public function page(string $key = null)
    {
        if (is_null($key)) {
            return $this->getPage() ?: $this->setPage();
        }

        return $this->setPage($key);
    }

    /**
     * Get current page component.
     *
     * @return self|null
     */
    public function getPage()
    {
        return $this->exists ? $this : null;
    }

    /**
     * Set current page component.
     *
     * @param string|null $key
     * @return self
     *
     * @throws \InvalidArgumentException
     */
    public function setPage(string $key = null)
    {
        $key = $this->getKeyOrCurrentRouteName($key);

        if ($this->isKey($key)) {
            return $this;
        }

        $page = Component::firstOrCreate(compact('key'));

        $this->fill($page->getAttributes());

        $this->syncOriginal();

        $this->exists = true;

        $this->wasRecentlyCreated = $page->wasRecentlyCreated;

        return $this;
    }

    /**
     * Check if the key is current.
     *
     * @param  string $key
     * @return boolean
     */
    public function isKey(string $key)
    {
        return $this->exists && $this->key === $key;
    }

    /**
     * Check and get route name.
     *
     * @param  string|null $key
     * @return boolean
     */
    protected function getKeyOrCurrentRouteName(string $key = null)
    {
        $key = $key ?: Route::currentRouteName();

        if (Route::has($key)) {
            return $key;
        }

        throw new InvalidArgumentException("Route {$key} not defined.");
    }

    /**
     * Scope a query to only include components where key.
     *
     * @param  Builder $query
     * @param  string  $key
     * @return Builder
     */
    public function scopeForKey(Builder $query, string $key)
    {
        return $query->where(compact('key'));
    }

    /**
     * Scope a query to only include components where visible is true.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible(Builder $query)
    {
        return $query->where('visible', true);
    }

    /**
     * Belongs to parent root component.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Component::class, 'parent_id');
    }

    /**
     * Set parent root component.
     *
     * @param string $key
     * @return void
     */
    public function setParent(string $key)
    {
        $this->parent()->associate(
            Component::firstOrCreate(compact('key'))
        )->save();
    }

    /**
     * Has many child root components.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Component::class, 'parent_id');
    }

    /**
     * Generate the URL to a named route of current root component.
     *
     * @param  array $params
     * @return string|null
     */
    public function route($params = [])
    {
        if ( ! $this->exists) {
            return null;
        }

        if ( ! is_array($params)) {
            $params = func_get_args();
        }

        return explode('?', route($this->key, $params))[0];
    }

    /**
     * Get url of the page.
     *
     * @return string|null
     */
    public function getUrlAttribute()
    {
        if ($route = Route::current()) {
            $params = $route->parameters();
        }

        return $this->route($params ?? []);
    }

    /**
     * Belongs to many not root components.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function components()
    {
        return $this->belongsToMany(Component::class, 'laravel_component_component', 'parent_component_id', 'child_component_id');
    }

    /**
     * Alias for components().
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function comps()
    {
        return $this->components();
    }

    /**
     * Find or create a new component.
     *
     * @param  string $key
     * @param  string|array|null $name
     * @return \Indigoram89\Components\Component
     */
    public function component(string $key, $name = null)
    {
        if ( ! $component = $this->components->where('key', $key)->first()) {
            $component = Component::firstOrCreate(compact('key'));
            $this->components()->attach($component);
            $this->components->push($component);
        }

        $name && $component->name($name);

        return $component;
    }

    /**
     * Alias for component().
     *
     * @param  string $key
     * @param  string|array|null $name
     * @return \Indigoram89\Components\Component
     */
    public function comp(string $key, $name = null)
    {
        return $this->component($key, $name);
    }

    /**
     * Find or create a new component using the current key name.
     *
     * @param  string $key
     * @param  string|array|null $name
     * @return \Indigoram89\Components\Component
     */
    public function extend(string $key, $name = null)
    {
        $key = "{$this->key}.{$key}";

        return $this->component($key, $name);
    }

    /**
     * Has many elements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function elements()
    {
        return $this->hasMany(Component::class);
    }

    /**
     * Alias for elements().
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function elems()
    {
        return $this->elements();
    }

    /**
     * Find or create a new element using the current key name.
     *
     * @param string $key
     * @param string|array|null $name
     * @return \Indigoram89\Components\Component
     */
    public function element(string $key, $name = null)
    {
        $key = "{$this->key}.$key";

        if ( ! $element = $this->elements->where('key', $key)->first()) {
            $element = $this->elements()->firstOrCreate(compact('key'));
            $this->elements->push($element);
        }

        $name && $element->name($name);

        return $element;
    }

    /**
     * Alias for element().
     *
     * @param string $key
     * @param string|array|null $name
     * @return \Indigoram89\Components\Component
     */
    public function elem(string $key, $name = null)
    {
        return $this->element($key, $name);
    }
}
