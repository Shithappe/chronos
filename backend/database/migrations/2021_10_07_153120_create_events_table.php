<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');

            $table->dateTime('start', $precision = 0);
            $table->dateTime('end', $precision = 0);

            $table->string('backgroundColor');
            $table->boolean('allDay')->default(false);
            $table->enum('type', ['none', 'arrangement', 'reminder', 'task'])->default('none');

            $table->foreignId('calendar_id')
                  ->constrained('calendars')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
