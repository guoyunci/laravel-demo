<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_index()
    {
        $response = $this->get('wx/catalog/index');
        $response->assertJson(['errno' => 0]);
    }
}
