<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->mediumText('sort_desc')->nullable();
            $table->string('added_by', 10);
            $table->integer('user_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('subcategory_id')->nullable();
            $table->integer('subsubcategory_id')->nullable();
            $table->mediumText('property_options')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('unit')->nullable();
            $table->string('weight', 100)->nullable();
            $table->string('length', 10)->nullable();
            $table->string('width', 10)->nullable();
            $table->string('height', 10)->nullable();
            $table->mediumText('tags')->nullable();
            $table->string('product_type', 20)->nullable();
            $table->string('photos', 2000)->nullable();
            $table->string('thumbnail_img', 100)->nullable();
            $table->string('featured_img', 100)->nullable();
            $table->string('flash_deal_img', 100)->nullable();
            $table->string('video_link', 100)->nullable();
            $table->mediumText('colors')->nullable();
            $table->mediumText('color_image')->nullable();
            $table->string('attributes', 1000)->nullable();
            $table->mediumText('attribute_options')->nullable();
            $table->double('tax', 8, 2)->nullable();
            $table->string('tax_type', 10)->nullable();
            $table->double('discount', 8, 2)->nullable();
            $table->string('discount_type', 10)->nullable();
            $table->boolean('discount_variation')->nullable()->default(0);
            $table->boolean('orderQtyLimit')->nullable()->default(0);
            $table->string('orderQtyLimitValue', 10)->nullable();
            $table->boolean('priceType')->default(0);
            $table->boolean('stockManagement')->default(1);
            $table->double('unit_price', 8, 2)->nullable();
            $table->integer('currency_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('sku')->nullable();
            $table->longText('description')->nullable();
            $table->string('linkProduct')->nullable();
            $table->string('shipping_type', 20)->default('free')->nullable();
            $table->double('shipping_cost', 8, 2)->default(0.00)->nullable();
            $table->integer('num_of_sale')->default(0);
            $table->mediumText('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('meta_img')->nullable();
            $table->mediumText('slug');
            $table->double('rating', 8, 2)->default(0.00);
            $table->boolean('digital')->default(0);
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
        Schema::dropIfExists('products');
    }
}
