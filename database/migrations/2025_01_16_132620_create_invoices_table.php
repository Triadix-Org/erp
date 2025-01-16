<?php

use App\Models\Customer;
use App\Models\HeaderSalesOrder;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('inv_no');
            $table->dateTime('date');
            $table->foreignIdFor(Customer::class);
            $table->integer('total_tax')->default(0);
            $table->integer('total_amount')->default(0);
            $table->string('po')->nullable();
            $table->foreignIdFor(User::class);
            $table->string('payment_terms')->nullable();
            $table->foreignIdFor(HeaderSalesOrder::class);
            $table->string('note')->nullable();
            $table->date('payment_due')->nullable();
            $table->date('ship_date')->nullable();
            $table->string('destination_country')->nullable();
            $table->string('port_of_origin')->nullable();
            $table->string('port_of_embarkation')->nullable();
            $table->string('bill_of_lading')->nullable();
            $table->integer('total_weight')->nullable();
            $table->integer('shipment_price')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
