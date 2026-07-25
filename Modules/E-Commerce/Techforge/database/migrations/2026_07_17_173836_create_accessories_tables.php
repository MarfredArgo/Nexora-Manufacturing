<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Monitors
        Schema::create('accessories_monitors', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_sold_out')->default(false);
            
            // Specific specs
            $table->string('resolution')->nullable();
            $table->string('refresh_rate')->nullable();
            $table->string('panel_type')->nullable();
            $table->string('size')->nullable();
            
            $table->timestamps();
        });

        // 2. Keyboards
        Schema::create('accessories_keyboards', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_sold_out')->default(false);
            
            $table->string('type')->nullable(); // Mechanical, Membrane
            $table->string('switch_type')->nullable();
            $table->string('size_layout')->nullable();
            $table->boolean('is_wireless')->default(false);
            
            $table->timestamps();
        });

        // 3. Keyboard Accessories
        Schema::create('accessories_keyboard_accessories', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_sold_out')->default(false);
            
            $table->string('type')->nullable(); // Keycaps, Switches
            $table->string('compatibility')->nullable();
            
            $table->timestamps();
        });

        // 4. Headsets
        Schema::create('accessories_headsets', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_sold_out')->default(false);
            
            $table->boolean('is_wireless')->default(false);
            $table->boolean('has_microphone')->default(true);
            $table->boolean('surround_sound')->default(false);
            
            $table->timestamps();
        });

        // 5. Mice
        Schema::create('accessories_mice', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_sold_out')->default(false);
            
            $table->integer('dpi')->nullable();
            $table->boolean('is_wireless')->default(false);
            $table->string('sensor_type')->nullable();
            $table->integer('buttons')->nullable();
            
            $table->timestamps();
        });

        // 6. Mouse Pads
        Schema::create('accessories_mouse_pads', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_sold_out')->default(false);
            
            $table->string('size')->nullable();
            $table->string('surface_type')->nullable();
            
            $table->timestamps();
        });

        // 7. Speaker Systems
        Schema::create('accessories_speaker_systems', function (Blueprint $table) {
            $table->id();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('brand')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_sold_out')->default(false);
            
            $table->string('channels')->nullable();
            $table->boolean('is_wireless')->default(false);
            $table->integer('total_wattage')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories_speaker_systems');
        Schema::dropIfExists('accessories_mouse_pads');
        Schema::dropIfExists('accessories_mice');
        Schema::dropIfExists('accessories_headsets');
        Schema::dropIfExists('accessories_keyboard_accessories');
        Schema::dropIfExists('accessories_keyboards');
        Schema::dropIfExists('accessories_monitors');
    }
};
