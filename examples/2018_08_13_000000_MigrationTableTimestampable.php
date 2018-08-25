<?php

use ChrisHalbert\LaravelNomadic\NomadicMigration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class MigrationTableTimestampable extends NomadicMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("migrations", function (Blueprint $b) {
            $b->dateTime('started_at')->nullable();
            $b->dateTime('completed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("migrations", function (Blueprint $b) {
            $b->dropColumn(['started_at', 'completed_at']);
        });
    }
}