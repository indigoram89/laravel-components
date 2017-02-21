<?php

namespace Indigoram89\Components\Test;

use Indigoram89\Components\Component;
use Indigoram89\Components\Test\TestCase;

class ComponentTest extends TestCase
{
    /** @test */
    public function instance_of()
    {
        page('test');

        $this->assertInstanceOf(Component::class, page());
    }

    /** @test */
    public function current_page()
    {
        page('test');

        $this->assertTrue(page()->isKey('test'));
    }

    /** @test */
    public function parent_page()
    {
        // page('test.show')->setParent('test');

        // $parent = page()->parent;
        // dd($parent);
        // $this->assertTrue($parent->isKey('test'));
    }

    /** @test */
    public function check_url()
    {
        page('test');

        $this->assertEquals('http://localhost/test', page()->url);
    }

    /** @test */
    public function duplicate_components()
    {
        page('test');

        $header = page()->component('header');
        $header = page()->component('header');

        $this->assertCount(1, page()->components);
        $this->assertCount(2, component()->get());
    }

    /** @test */
    public function keys_of_the_components()
    {
        page('test');

        $header = page()->component('header');
        $this->assertEquals('header', $header->key);

        $footer = page()->extend('footer');
        $this->assertEquals('test.footer', $footer->key);
    }

    /** @test */
    public function elements()
    {
        page('test');

        $item1 = page()->component('menu')->element('item-1');
        $item2 = page()->component('menu')->element('item-2');

        $this->assertEquals('menu.item-1', $item1->key);
        $this->assertEquals('menu.item-2', $item2->key);
        $this->assertCount(2, component('menu')->elements);

        $item1 = page()->extend('menu')->element('item-1');
        $item2 = page()->extend('menu')->element('item-2');

        $this->assertEquals('test.menu.item-1', $item1->key);
        $this->assertEquals('test.menu.item-2', $item2->key);
        $this->assertCount(2, component('test.menu')->elements);
    }
}
