<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Modules\Procurement\Models\Supplier;
use Tests\TestCase;

class ProcurementClientIsolationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.connections.procurement', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        Schema::connection('procurement')->create('suppliers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('client_id')->index();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function test_procurement_models_are_scoped_to_the_signed_in_client(): void
    {
        session(['employee_client_id' => 10]);
        Supplier::create(['name' => 'Client Ten Supplier']);

        session(['employee_client_id' => 20]);
        Supplier::create(['name' => 'Client Twenty Supplier']);

        session(['employee_client_id' => 10]);
        $this->assertSame(['Client Ten Supplier'], Supplier::pluck('name')->all());

        session(['employee_client_id' => 20]);
        $this->assertSame(['Client Twenty Supplier'], Supplier::pluck('name')->all());
    }

    public function test_procurement_routes_reject_a_non_employee_session(): void
    {
        $this->get('/procurement/dashboard')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');
    }
}
