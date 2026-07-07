<?php
namespace App\Services;

use Automattic\WooCommerce\Client;
use App\Services\BaseService;

class WoocommerceService extends BaseService

{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            env('WC_STORE_URL'),
            env('WC_CONSUMER_KEY'),
            env('WC_CONSUMER_SECRET'),
            [
                'version' => 'wc/v3',
            ]
            
        );
    }

    public function createOrUpdateProduct($product)
    {
        $data = [
            'name' => $product->name,
            'sku' => $product->sku,
            'regular_price' => (string) $product->price,
            'stock_quantity' => $product->stock,
            'description' => $product->description,
            'images' => [
                [
                    'src' => asset('storage/' . $product->image),
                ],
            ],
        ];

        try {
            if ($product->woocommerce_id) {
                // Update the product
                return $this->client->put("products/{$product->woocommerce_id}", $data);
            } else {
                // Create a new product
                $response = $this->client->post('products', $data);
                $product->woocommerce_id = $response['id'];
                $product->save();

                return $response;
            }
        } catch (\Exception $e) {
            throw new \Exception('WooCommerce API error: ' . $e->getMessage());
        }
    }
}
