<?php

namespace App\Traits;

ini_set('memory_limit', '-1'); // Unlimited
ini_set('max_execution_time', '0'); //Unlimited

use Throwable;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Platform;
use App\Models\Purchase;
use App\Models\Attribute;
use App\Models\Variation;
use App\Models\Warehouse;
use App\Models\ProductTag;
use App\Models\InvoiceItem;
use App\Models\ProductStock;
use App\Models\PurchaseItem;
use App\Models\AttributeItem;
//use App\Services\Invoice\InvoiceService;
use App\Models\InvoicePayment;
use App\Jobs\FetchWooOrdersJob;
use App\Models\InvoicePlatform;
use App\Models\ProductCategory;
use App\Models\ProductPlatform;
use App\Models\PurchaseReceive;
use App\Models\CategoryPlatform;
use App\Models\CustomerPlatform;
use App\Models\ProductAttribute;
use App\Models\ProductVariation;
use App\Models\ProgressTracking;
use App\Jobs\FetchWooProductsJob;
use App\Models\AttributePlatform;
use App\Models\VariationPlatform;
use Illuminate\Http\UploadedFile;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseItemReceive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\ProductStoredCategory;
use App\Models\VariationStockStataus;
use Illuminate\Support\Facades\Cache;
use App\Models\ProductCategoryPlatform;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductVariationPlatform;
use Illuminate\Http\Client\HttpClientException;

trait Woocommerce
{

    public function woocommerceConnection($credential)
    {
        // $woo_store_url = env('WOOCOMMERCE_STORE_URL') ?? '';
        // $woo_consumer_key = env('WOOCOMMERCE_CONSUMER_KEY') ?? '';
        // $woo_consumer_secret = env('WOOCOMMERCE_CONSUMER_SECRET') ?? '';
        // $woo_timeout = 600;
        // $woocommarce_setting = getSettingBySettingKey('woocommarce');
        // if($woocommarce_setting){
        //     $woo_store_url = $woocommarce_setting->settings_value['store_url'] ?? '';
        //     $woo_consumer_key = $woocommarce_setting->settings_value['consumer_key'] ?? '';
        //     $woo_consumer_secret = $woocommarce_setting->settings_value['consumer_secret'] ?? '';
        //     $woo_timeout = $woocommarce_setting->settings_value['timeout']?? 600;
        // }
        // $woocommerce = new Client(
        // 	$woo_store_url,
        // 	$woo_consumer_key,
        // 	$woo_consumer_secret,
        // 	 'consumer_secret',
        // 	[
        // 		// 'wp_api' => true,
        // 		'version' => 'wc/v3',
        // 		"timeout" => $woo_timeout,
        // 		// 'query_string_auth' => true // Force Basic Authentication as query string true and using under HTTPS
        // 	]
        // );
        $woocommerce = new Client(
            $credential['store_url'],
            $credential['consumer_key'],
            $credential['consumer_secret'],
            'consumer_secret',
            [
                // 'wp_api' => true,
                'version' => 'wc/v3',
                "timeout" => 600,
                // 'query_string_auth' => true // Force Basic Authentication as query string true and using under HTTPS
            ]
        );
        return $woocommerce;
    }

    public function fetchAll()
    {
        $product_tags = $this->fetchProductTags();
        $attributes_number = $this->fetchAttributes();
        $attributes_terms_number = $this->fetchAttributeItems();
        $categories_number = $this->fetchProductCategories();
        $products_number = $this->fetchProducts();
        return [$attributes_number, $categories_number, $products_number, $product_tags];
    }

    public function fetchAttributes($platform_id, $credential)
    {

        $attributes = [];

        try {
            // Fetch attributes from the WooCommerce API
            $attributes = $this->woocommerceConnection($credential)->get('products/attributes');
        } catch (HttpClientException $e) {
            // Log the error and handle it gracefully instead of stopping the application
            logger()->error("Failed to fetch WooCommerce attributes: " . $e->getMessage());
            return 0; // Return 0 to indicate no attributes were processed
        }

        // Check if attributes were fetched successfully
        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                // Save or update the attribute in the 'attributes' table
                $attributeModel = Attribute::updateOrCreate(
                    ['name' => $attribute->name], // Match by name
                    ['status' => Attribute::STATUS_ACTIVE] // Update status if needed
                );

                AttributePlatform::updateOrCreate(
                    [

                        'ecommerce_id' => $attribute->id,
                        'platform_id' => $platform_id,
                    ],
                    [
                        'attribute_id' => $attributeModel->id,
                        'platform_data' => (array)$attribute, // WooCommerce category ID
                        'is_active' => true
                    ],

                );
    
            }
        }

        return count($attributes);
    }


    public function fetchProductCategories($platform_id, $credential, $progress_id = null)
    {
        $allCategories = [];
        $page = 1;

        try {
            // Fetch all WooCommerce product categories (paginated)
            do {
                $categories = $this->woocommerceConnection($credential)->get('products/categories', [
                    'per_page' => 100,
                    'page'     => $page,
                ]);

                $allCategories = array_merge($allCategories, $categories);

                if (count($categories) < 100) {
                    break;
                }

                $page++;
            } while (true);
        } catch (HttpClientException $e) {
            logger()->error("Error fetching product categories: {$e->getMessage()}");
            return "Can't fetch product categories: {$e->getMessage()}";
        }

        $total = count($allCategories);
        $progress = $progress_id ? ProgressTracking::find($progress_id) : null;

        // Initialize or complete immediately if no categories found
        if ($progress) {
            $progress->total = $total;
            if ($total === 0) {
                $progress->status = 'completed';
            }
            $progress->save();
        }

        if ($total === 0) {
            return 0;
        }

        $processed = 0;

        // ----------------------------
        // PASS 1: Create or update all categories (ignore parent linkage)
        // ----------------------------
        foreach ($allCategories as $category) {

            $categoryData = [
                'name' => $category->name,
                'image' => !empty($category->image->src)
                    ? $this->downloadWooImageAndUpload($category->image->src, null, ProductCategory::FILE_STORE_PATH)
                    : '',
                'desc' => $category->description,
            ];

            // Update or create main category
            $categoryModel = ProductCategory::updateOrCreate(
                ['name' => $category->name],
                $categoryData
            );

            // Update or create platform linkage
            ProductCategoryPlatform::updateOrCreate(
                [
                    'ecommerce_id' => $category->id,
                    'platform_id'  => $platform_id,
                ],
                [
                    'product_category_id' => $categoryModel->id,
                    'platform_data'      =>(array) $category,
                    'is_active'          => true
                ]
            );

            $processed++;

            if ($progress && $progress->status === 'running') {
                $progress->processed = $processed;
                $progress->save();
            }
        }

        // ----------------------------
        // PASS 2: Update parent-child relationships
        // ----------------------------
        foreach ($allCategories as $category) {
            if (!empty($category->parent)) {
                $childMapping = ProductCategoryPlatform::where('ecommerce_id', $category->id)
                    ->where('platform_id', $platform_id)
                    ->first();

                $parentMapping = ProductCategoryPlatform::where('ecommerce_id', $category->parent)
                    ->where('platform_id', $platform_id)
                    ->first();

                if ($childMapping && $parentMapping) {
                    ProductCategory::where('id', $childMapping->product_category_id)
                        ->update(['parent_id' => $parentMapping->product_category_id]);
                }
            }
        }

        // ----------------------------
        // Finalize progress
        // ----------------------------
        if ($progress) {
            $progress->processed = $total;
            $progress->status = 'completed';
            $progress->save();
        }

        return $total;
    }

    public function syncCategoryToWooCommerce($platform_id, $category_id)
    {
        logger('looking category');

        $credential = Platform::find($platform_id);
        $category   = ProductCategory::find($category_id);
        $parentWooId = null;

        // 🧩 Resolve parent category Woo ID
        if ($category->parent_id) {
            $parentMapping = ProductCategoryPlatform::where('product_category_id', $category->parent_id)
                ->where('platform_id', $platform_id)
                ->first();

            if ($parentMapping) {
                $parentWooId = $parentMapping->ecommerce_id;
            }
        }

        // 🧾 Prepare WooCommerce data
        $data = [
            'name'        => $category->name,
            'description' => $category->desc ?? '',
            'parent'      => $parentWooId ?? 0,
        ];

        if ($category->image) {
            $data['image'] = [
                'src' => $category->file_url,
            ];
        }

        try {
            $mapping = ProductCategoryPlatform::where('product_category_id', $category->id)
                ->where('platform_id', $platform_id)
                ->first();

            if ($mapping && $mapping->ecommerce_id) {
                // 🟢 Update existing Woo category
                $wooResponse = $this->woocommerceConnection($credential)
                    ->put("products/categories/{$mapping->ecommerce_id}", $data);

                // ✅ Reactivate if was inactive
                $mapping->update([
                    'is_active'     => true,
                    'platform_data' => $wooResponse,
                ]);
            } else {
                // 🆕 Create new Woo category
                $wooResponse = $this->woocommerceConnection($credential)
                    ->post("products/categories", $data);

                ProductCategoryPlatform::updateOrCreate(
                    [
                        'product_category_id' => $category->id,
                        'platform_id'         => $platform_id,
                    ],
                    [
                        'ecommerce_id'  => $wooResponse->id ?? null,
                        'platform_data' => (array)$wooResponse,
                        'is_active'     => true,
                    ]
                );
            }

            return [
                'success' => true,
                'data'    => $wooResponse,
            ];
        } catch (\Exception $e) {
            logger()->error("WooCommerce category sync failed for category {$category->id}: " . $e->getMessage());

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }


    public function syncAttributeToWooCommerce($platform_id, $attribute_id)
    {
        logger('🔍 Looking up attribute for sync');

        // 1️⃣ Platform & Attribute resolve
        $credential = \App\Models\Platform::find($platform_id);
        $attribute  = \App\Models\Attribute::with('items')->find($attribute_id);

        if (!$credential || !$attribute) {
            return [
                'success' => false,
                'error'   => "Platform or Attribute not found",
            ];
        }

        // 2️⃣ Prepare Woo data
        $data = [
            'name'     => $attribute->name,
            'type'     => 'select',
            'order_by' => 'menu_order',
        ];

        try {
            $woocommerce = $this->woocommerceConnection($credential);

            // 3️⃣ Get existing mapping (if any)
            $mapping = \App\Models\AttributePlatform::where('attribute_id', $attribute->id)
                ->where('platform_id', $platform_id)
                ->first();

            if ($mapping && $mapping->ecommerce_id) {
                // 🔄 Update existing attribute in Woo
                $wooResponse = $woocommerce->put("products/attributes/{$mapping->ecommerce_id}", $data);

                // Reactivate mapping if it was inactive
                $mapping->update([
                    'platform_data' => $wooResponse,
                    'is_active'     => true,
                ]);
            } else {
                // 🆕 Create new attribute in Woo
                logger('🆕 Creating new attribute in WooCommerce');

                $wooResponse = $woocommerce->post("products/attributes", $data);

                // ✅ Always ensure local mapping stays up-to-date
                \App\Models\AttributePlatform::updateOrCreate(
                    [
                        'attribute_id' => $attribute->id,
                        'platform_id'  => $platform_id,
                    ],
                    [
                        'ecommerce_id'  => $wooResponse->id ?? null,
                        'platform_data' => $wooResponse,
                        'is_active'     => true, // reactivate if previously false
                    ]
                );

                logger('✅ Attribute created successfully');
            }

            // 4️⃣ Sync Attribute Items (terms)
            $finalMapping = \App\Models\AttributePlatform::where('attribute_id', $attribute->id)
                ->where('platform_id', $platform_id)
                ->first();

            if ($attribute->items && $finalMapping && $finalMapping->ecommerce_id) {
                foreach ($attribute->items as $item) {
                    try {
                        $termData = [
                            'name'        => $item->name,
                            'description' => $item->name,
                            'slug'        => \Str::slug($item->name),
                        ];

                        // Create or update terms (safe to post, Woo handles duplicates gracefully)
                        $woocommerce->post("products/attributes/{$finalMapping->ecommerce_id}/terms", $termData);
                    } catch (\Exception $e) {
                        logger("⚠️ Failed syncing term [{$item->name}] for attribute {$attribute->name}: " . $e->getMessage());
                    }
                }
            }

            return [
                'success' => true,
                'data'    => $wooResponse ?? null,
            ];
        } catch (\Exception $e) {
            logger()->error("❌ WooCommerce attribute sync failed for {$attribute->id}: " . $e->getMessage());
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }







    public function fetchProducts($platform_id, $credential, $progress_id)
    {
        $perPage = 10;
        $page = 1;
        $totalProducts = 0;

        do {
            try {
                $products = $this->woocommerceConnection($credential)->get('products', [
                    'per_page' => $perPage,
                    'page'     => $page,
                    'order'    => 'desc',
                    'orderby'  => 'id'
                ]);

                logger('products' . count($products));

                if (count($products) === 0) {
                    break;
                }

                // Dispatch job for this page
                FetchWooProductsJob::dispatch($products, $platform_id, $credential, $page, $progress_id);

                $totalProducts += count($products);
                $page++;

                if (count($products) < $perPage) {
                    break;
                }
            } catch (HttpClientException $e) {
                logger()->error("Failed to fetch products from WooCommerce: {$e->getMessage()}");
                break;
            }
        } while (true);
        logger('while loop done');

        // Update total count in progress tracking
        $progress = \App\Models\ProgressTracking::find($progress_id);
        if ($progress) {
            logger('progress id found' . $progress_id);
            $progress->total = $totalProducts;
            // If there are no orders at all, mark as completed immediately
            if ($totalProducts === 0) {
                $progress->status = 'completed';
            }
            $progress->save();
        }
        logger()->info("Total products queued: " . $totalProducts);
    }


    public function createParentProductForWoocommerce(array $productData, int $storeId, int $localProductId, bool $is_update = false)
    {
        $store = Platform::find($storeId);
        if (!$store) return null;

        try {
            $productPlatform = ProductPlatform::where('product_id', $localProductId)
                ->where('platform_id', $storeId)
                ->first();

            if ($is_update && $productPlatform && $productPlatform->ecommerce_id) {
                // 🔄 Update product
                $response = $this->woocommerceConnection($store)
                    ->put("products/{$productPlatform->ecommerce_id}", $productData);
            } else {
                // 🆕 Create product
                $response = $this->woocommerceConnection($store)->post('products', $productData);
            }

            // Save parent product mapping
            ProductPlatform::updateOrCreate(
                [
                    'product_id'  => $localProductId,
                    'platform_id' => $store->id,
                ],
                [
                    'ecommerce_id' => $response->id,
                    'platform_data' => $response,
                    'is_active'    => true,
                ]
            );

            return $response;
        } catch (\Exception $e) {
            logger()->error("WooCommerce parent variable product sync failed for product {$localProductId}: " . $e->getMessage());
            return null;
        }
    }


    public function storeProductToWooCommerce(int $productId, int $storeId, bool $is_update = false)
    {
        logger('calling storeProductToWooCommerce');

        $product = Product::with(['category', 'variations.attributeItems.attribute'])->find($productId);
        $store   = Platform::find($storeId);

        if (!$product || !$store) {
            logger()->warning("Product or Store not found: {$productId}, {$storeId}");
            return null;
        }

        // 1️⃣ Sync category
        $wooCategoryId = $product->category
            ? $this->syncCategoryWithWooCommerce($product->category, $store)
            : null;

        // 2️⃣ Prepare common product data
        $wooData = [
            'name'        => $product->name,
            'sku'         => $product->sku,
            'description' => $product->desc,
            'categories'  => $wooCategoryId ? [['id' => $wooCategoryId]] : [],
            'status' => $product->status == Product::STATUS_ACTIVE
                ? 'publish'
                : 'draft',
        ];
        // ✅ Add main image if available
        if ($product->thumb) {
            $wooData['images'] = [
                ['src' =>  getPublicStorageImage(\App\Models\Product::FILE_STORE_PATH, $product->thumb)]
            ];
        }

        try {
            if ($product->is_variant) {
                // ==============================
                // 1️⃣ Create Parent (Variable) Product
                // ==============================
                $parentWoo = $this->createParentProductForWoocommerce(
                    array_merge($wooData, [
                        'type'   => 'variable',
                    ]),
                    $storeId,
                    $productId,
                    $is_update
                );

                if (!$parentWoo || !isset($parentWoo->id)) {
                    logger()->error("❌ Failed to create/update parent variable product for Product ID={$product->id}");
                    return null;
                }

                $parentWooId = $parentWoo->id;

                // ==============================
                // 2️⃣ Create Attributes in WooCommerce
                // ==============================
                $attributeNames = $product->variations()
                    ->with('attributeItems.attribute')
                    ->get()
                    ->pluck('attributeItems.*.attribute.name')
                    ->flatten()
                    ->unique()
                    ->values()
                    ->all();

                $wooAttributes = $this->createAttributeInWooCommerce($attributeNames, $storeId);

                // ==============================
                // 3️⃣ Build Attributes for Parent Product
                // ==============================
                $attributeMap = [];
                $allVariations = $product->variations()->with('attributeItems.attribute')->get();

                foreach ($allVariations as $variation) {
                    foreach ($variation->attributeItems as $item) {
                        $attributeName = $item->attribute->name ?? $item->name;
                        $attributeMap[$attributeName][] = $item->name;
                    }
                }

                $attributesForParent = [];
                foreach ($attributeMap as $attrName => $options) {
                    $wooAttr = collect($wooAttributes)
                        ->first(fn($wa) => strtolower($wa['name']) === strtolower($attrName));

                    if ($wooAttr && isset($wooAttr['id'])) {
                        $attributesForParent[] = [
                            'id'        => $wooAttr['id'],
                            'variation' => true,
                            'visible'   => true,
                            'options'   => collect($options)->unique()->values()->all(),
                        ];
                    }
                }

                // Update parent with WooCommerce attributes
                $this->woocommerceConnection($store)->put("products/{$parentWooId}", [
                    'attributes' => $attributesForParent,
                ]);

                // ==============================
                // 4️⃣ Create or Update Variations in WooCommerce
                // ==============================
                $existingWooVariations = collect(
                    $this->woocommerceConnection($store)
                        ->get("products/{$parentWooId}/variations")
                );

                $wooVariationIds = $existingWooVariations->pluck('id')->toArray();
                $localVariationIds = $allVariations->pluck('id')->toArray();
                $localWooLinkedIds = VariationPlatform::whereIn('variation_id', $localVariationIds)
                    ->where('platform_id', $storeId)
                    ->pluck('ecommerce_id')
                    ->filter()
                    ->toArray();

                // Detect WooCommerce variations that exist remotely but not locally
                $wooVariationsToDelete = array_diff($wooVariationIds, $localWooLinkedIds);

                // Delete extra WooCommerce variations
                foreach ($wooVariationsToDelete as $wooVarId) {
                    try {
                        $this->woocommerceConnection($store)
                            ->delete("products/{$parentWooId}/variations/{$wooVarId}", ['force' => true]);
                        logger()->info("🗑️ Deleted WooCommerce variation ID={$wooVarId} (no longer exists locally)");
                    } catch (\Exception $e) {
                        logger()->warning("⚠️ Failed to delete WooCommerce variation ID={$wooVarId}: {$e->getMessage()}");
                    }
                }


                // ==============================
                // 4️⃣ Create Variations in WooCommerce
                // ==============================
                foreach ($allVariations as $index => $variation) {
                    $attributesArray = [];

                    foreach ($variation->attributeItems as $attrItem) {
                        $wooAttr = collect($wooAttributes)
                            ->first(fn($wa) => strtolower($wa['name']) === strtolower($attrItem->attribute->name ?? ''));

                        if ($wooAttr && isset($wooAttr['id'])) {
                            $attributesArray[] = [
                                'id'     => $wooAttr['id'],
                                'option' => $attrItem->name,
                            ];
                        }
                    }

                    if (empty($attributesArray)) continue;

                    // WooCommerce variation payload
                    // logger('variation', [$variation->toArray()]);
                    $variationPayload = [
                        'regular_price' => (string)($variation->price ?? 0),
                        'manage_stock'  =>  true,
                        'stock_quantity' => $variation->stock_quantity ?? 0,
                        'sku'           => $variation->sku ??  generateUniqueSku(),
                        'attributes'    => $attributesArray,
                        'menu_order'    => $index,
                        'weight'          => (string)($variation->weight ?? ''), // must be string
                        'dimensions'      => [
                            'length' => (string)($variation->dimension_l ?? ''), // ✅ fix: logical naming
                            'width'  => (string)($variation->dimension_w ?? ''),
                            'height' => (string)($variation->dimension_d ?? ''),
                        ],
                    ];

                    if ($variation->thumb) {
                        $variationPayload['image'] = ['src' => getStorageImage('products', $variation->thumb)];
                    }

                    $variationPlatform = $variation->platforms()->where('platform_id', $storeId)->first();

                    if ($variationPlatform && $variationPlatform->ecommerce_id) {
                        // 🔄 Update existing variation
                        $wooVariation = $this->woocommerceConnection($store)
                            ->put("products/{$parentWooId}/variations/{$variationPlatform->ecommerce_id}", $variationPayload);
                    } else {
                        // 🆕 Create new variation
                        $wooVariation = $this->woocommerceConnection($store)
                            ->post("products/{$parentWooId}/variations", $variationPayload);
                    }

                    if ($wooVariation && isset($wooVariation->id)) {
                        \App\Models\VariationPlatform::updateOrCreate(
                            [
                                'variation_id' => $variation->id,
                                'platform_id'  => $storeId,
                            ],
                            [
                                'ecommerce_id' => $wooVariation->id,
                                'platform_data' => $wooVariation,
                            ]
                        );

                        logger()->info("✅ Synced Variation", [
                            'variation_id' => $variation->id,
                            'woo_id'       => $wooVariation->id,
                        ]);
                    }
                }
            } else {
                // ==============================
                // Simple Product
                // ==============================
                $wooData['type']           = 'simple';
                $wooData['regular_price']  = (string) $product->price;
                $wooData['manage_stock']   = true;
                $wooData['stock_quantity'] = $product->stock ?? 0;

                $productPlatform = ProductPlatform::where('product_id', $product->id)
                    ->where('platform_id', $storeId)
                    ->first();

                if ($is_update && $productPlatform && $productPlatform->ecommerce_id) {
                    $response = $this->woocommerceConnection($store)
                        ->put("products/{$productPlatform->ecommerce_id}", $wooData);
                } else {
                    $response = $this->woocommerceConnection($store)
                        ->post('products', $wooData);
                }

                if ($response && isset($response->id)) {
                    ProductPlatform::updateOrCreate(
                        [
                            'product_id'  => $product->id,
                            'platform_id' => $store->id,
                        ],
                        [
                            'ecommerce_id' => $response->id,
                            'platform_data' => $response,
                            'is_active'    => true,
                        ]
                    );
                }
            }

            return true;
        } catch (\Exception $e) {
            logger()->error("WooCommerce sync failed for product {$product->id}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function removeDeselectedStores(int $productId, array $selectedStoreIds)
    {
        // Get all currently linked store IDs for this product
        $existingStoreIds = ProductPlatform::where('product_id', $productId)
            ->pluck('platform_id')
            ->toArray();

        // Find which stores were deselected
        $storesToRemove = array_diff($existingStoreIds, $selectedStoreIds);

        if (empty($storesToRemove)) {
            return;
        }

        // Just delete those mappings from product_platform
        ProductPlatform::where('product_id', $productId)
            ->whereIn('platform_id', $storesToRemove)
            ->update(['is_active' => false]);
    }


    protected function removeDeselectedCategoryPlatforms(int $categoryId, array $selectedPlatformIds)
    {
        // Current platform mappings for this category
        $existingPlatformIds = ProductCategoryPlatform::where('product_category_id', $categoryId)
            ->pluck('platform_id')
            ->toArray();

        // Platforms to remove (deselected)
        $platformsToRemove = array_diff($existingPlatformIds, $selectedPlatformIds);

        foreach ($platformsToRemove as $platformId) {
            $mapping = ProductCategoryPlatform::where('product_category_id', $categoryId)
                ->where('platform_id', $platformId)
                ->update(['is_active' => false]);
        }
    }
    protected function removeDeselectedAttributePlatforms(int $attributeId, array $selectedPlatformIds)
    {
        // বর্তমান attribute-platform mapping গুলো খুঁজে বের করা
        $existingPlatformIds = \App\Models\AttributePlatform::where('attribute_id', $attributeId)
            ->pluck('platform_id')
            ->toArray();

        // যেগুলো deselect করা হয়েছে সেগুলো বের করা
        $platformsToRemove = array_diff($existingPlatformIds, $selectedPlatformIds);

        foreach ($platformsToRemove as $platformId) {
            $mapping = \App\Models\AttributePlatform::where('attribute_id', $attributeId)
                ->where('platform_id', $platformId)
                ->update(['is_active' => false]);
        }
    }






    public function createAttributeInWooCommerce($attributes, int $platform_id)
    {
        $woocommerce = $this->woocommerceConnection(Platform::find($platform_id));



        $createdAttributes = [];
        // logger('my Attributes', [$attributes]);

        foreach ($attributes as $attribute) {
            // Ensure it's a clean string
            $attributeName = trim($attribute);
            if (!$attributeName) continue;

            // 1️⃣ Create or find local Attribute model
            $attributeModel = Attribute::updateOrCreate(
                ['name' => $attributeName],
                ['status' => Attribute::STATUS_ACTIVE]
            );
            $attributePlatform = AttributePlatform::where([
                'attribute_id' => $attributeModel->id,
                'platform_id'  => $platform_id,
            ])->first();

            if (!$attributePlatform) {
                try {
                    // logger('here you');
                    $wooAttrData = [
                        'name'        => $attributeName,
                        'type'        => 'select',
                        'order_by'    => 'menu_order',
                        'has_archives' => true,
                    ];

                    $response = $woocommerce->post('products/attributes', $wooAttrData);

                    $attributePlatform = AttributePlatform::updateOrCreate(
                        [
                            'platform_id'  => $platform_id,
                            'ecommerce_id' => $response->id,
                        ],
                        [
                            'attribute_id'  => $attributeModel->id,
                            'platform_data' => $response,
                        ]
                    );

                    $createdAttributes[] = $response;
                } catch (\Exception $e) {
                    logger()->error("WooCommerce attribute sync failed: " . $e->getMessage());
                    throw $e;
                }
            } else {
                $createdAttributes[] = $attributePlatform->platform_data;
            }
        }

        return $createdAttributes;
    }




    protected function syncCategoryWithWooCommerce($category, $store)
    {
        if (!$category) return null;

        // 1️⃣ Handle parent
        $parentWooId = null;
        if ($category->parent_category) {
            $parentWooId = $this->syncCategoryWithWooCommerce($category->parent_category, $store);
        }

        // 2️⃣ Check if already synced
        $platformData = ProductCategoryPlatform::where('product_category_id', $category->id)
            ->where('platform_id', $store->id)
            ->first();

        if ($platformData) {
            return $platformData->ecommerce_id;
        }

        // 3️⃣ Prepare WooCommerce category data
        $wooData = ['name' => $category->name];
        if ($parentWooId) {
            $wooData['parent'] = $parentWooId;
        }

        try {
            $response = $this->woocommerceConnection($store)->post('products/categories', $wooData);

            // Save/update local category mapping
            ProductCategoryPlatform::updateOrCreate(
                [
                    'ecommerce_id' => $response->id,
                    'platform_id'  => $store->id,
                ],
                [
                    'product_category_id' => $category->id,
                    'platform_data'       => $response,
                ]
            );

            return $response->id;
        } catch (\Exception $e) {
            \Log::error("WooCommerce category sync failed: " . $e->getMessage());
            return null;
        }
    }

    public function storeOrUpdateProduct($api_product, int $platform_id, $credential)
    {
        logger('store or update called');
        $defaultWarehouse = Warehouse::where('is_default', 1)->first()
            ?? Warehouse::where('status', Warehouse::STATUS_ACTIVE)->first();

        if (!$defaultWarehouse) {
            throw new \RuntimeException('Cannot sync WooCommerce product: no warehouse found. Please create and set a default warehouse first.');
        }

        // Get or create "Uncategorized" category
        $defaultCategory = ProductCategory::firstOrCreate(
            ['name' => 'Uncategorized'],
            ['status' => ProductCategory::STATUS_ACTIVE]
        );

        // Determine category
        $categoryId = $defaultCategory->id;
        if (!empty($api_product->categories) && isset($api_product->categories[0])) {
            $categoryName = $api_product->categories[0]->name;
            $category = ProductCategory::firstOrCreate(
                ['name' => $categoryName],
                ['status' => ProductCategory::STATUS_ACTIVE]
            );
            $categoryId = $category->id;
        }

        // Handle simple product
        if ($api_product->type == 'simple') {
            $sku = !empty($api_product->sku) ? $api_product->sku : generateUniqueSku();
            $price = !empty($api_product->price) ? $api_product->price : 0;
            $customerPrice = !empty($api_product->regular_price) ? $api_product->regular_price : $price;

            $data = [
                'name' => str_replace('<br>', ' ', $api_product->name),
                'sku' => $sku,
                'barcode' => generateUniqueBarcode(),
                'category_id' => $categoryId,
                'desc' => $api_product->description,
                'price' => $price,
                'thumb' =>  isset($api_product->images[0]) && $api_product->images[0]->src ? $this->downloadWooImageAndUpload($api_product->images[0]->src, null, Product::FILE_STORE_PATH) : '',
                'customer_buying_price' => $customerPrice,
                'is_variant' => 0,
                'status' => $api_product->status == 'publish'
                    ? Product::STATUS_ACTIVE
                    : Product::STATUS_INACTIVE,
            ];

            // First update or create the Product itself
            $productId = ProductPlatform::where('ecommerce_id', $api_product->id)
                ->where('platform_id', $platform_id)
                ->value('product_id'); // returns product_id or null

            $productModel = Product::updateOrCreate(
                ['id' => $productId],
                $data
            );

            // Then update or create the ProductPlatform mapping
            ProductPlatform::updateOrCreate(
                [
                    'ecommerce_id' => $api_product->id,
                    'platform_id'  => $platform_id,
                ],
                [
                    'product_id'    => $productModel->id,
                    'platform_data' => (array)$api_product,
                    'is_active'     => true,
                ]
            );


            $this->createNormalStock($productModel->id, $defaultWarehouse->id, $api_product);
            return $productModel;
        }

        // Handle variable product
        elseif ($api_product->type == 'variable') {
            logger('variable prdouct block');
            $sku = !empty($api_product->sku) ? $api_product->sku : generateUniqueSku();

            $data = [
                'name' => str_replace('<br>', ' ', $api_product->name),
                'sku' => $sku,
                'category_id' => $categoryId,
                'desc' => $api_product->description,
                'price' => null, // will calculate from variations
                'thumb' => isset($api_product->images[0]) && $api_product->images[0]->src ? $this->downloadWooImageAndUpload($api_product->images[0]->src, null, Product::FILE_STORE_PATH) : '',
                'customer_buying_price' => null,
                'is_variant' => 1,
                'status' => $api_product->status == 'publish'
                    ?  Product::STATUS_ACTIVE
                    : Product::STATUS_INACTIVE,
            ];


            $productId = ProductPlatform::where('ecommerce_id', $api_product->id)
                ->where('platform_id', $platform_id)
                ->value('product_id'); // returns product_id or null

            $productModel = Product::updateOrCreate(
                ['id' => $productId],
                $data
            );
            ProductPlatform::updateOrCreate(
                [
                    'ecommerce_id' => $api_product->id,
                    'platform_id'  => $platform_id,
                ],
                [
                    'product_id'    => $productModel->id,
                    'platform_data' => (array)$api_product,
                    'is_active'     => true,
                ]
            );

            // Fetch variations
            $variations = $this->woocommerceConnection($credential)
                ->get("products/{$api_product->id}/variations", [
                    'per_page' => 100,
                    'order'    => 'asc',
                    'orderby'  => 'id'
                ]);

            // logger()->info('WooCommerce Variations', (array) $variations);

            // $attributesArray = [];
            // $attributeData = [];
            $lowestPrice = null;
            $variationFlags = [];
            // Ensure $variations is not empty
            if (!empty($variations)) {

                foreach ($variations as $key => $variation) {

                    // Backorders flag
                    $backordersAllowed = false;
                    $manageStock = false;
                    $backordersAllowed = $this->wooBackordersAllowed($variation);
                    $manageStock = $variation->manage_stock;

                    $variationFlags[$key] = [
                        'backorders'   => $backordersAllowed,
                        'manage_stock' => $manageStock,
                    ];


                    // Skip null variation
                    if (is_null($variation)) {
                        continue;
                    }

                    // Determine variation price and quantity
                    $variation_price = !empty($variation->price)
                        ? $variation->price
                        : (!empty($variation->price) ? $variation->price : 0);

                    $variation_quantity = !empty($variation->manage_stock)
                        ? ($variation->stock_quantity ?? 0)
                        : 0;

                    if (is_null($lowestPrice) || $variation_price < $lowestPrice) {
                        $lowestPrice = $variation_price;
                    }

                    $variationData = [
                        'product_id' => $productModel->id,
                        'name' => implode('-', array_map(fn($a) => $a->option, $variation->attributes ?? [])),
                        'sku' => $variation->sku ?? generateUniqueSku(),
                        'price' => $variation_price,
                        'customer_buying_price' => "",
                        'regular_price' => $variation->regular_price ?? $variation_price,
                        'stock_quantity' => $variation_quantity,
                        'stock_status' => $variation->stock_status ?? 'instock',
                        'thumb' => isset($variation->image->src) ? $this->downloadWooImageAndUpload($variation->image->src, null, Product::FILE_STORE_PATH) : null,
                        'barcode' => generateUniqueBarcode(),
                        'backorders'   => $backordersAllowed,
                        'manage_stock' => $manageStock,
                        'desc' => $variation->description ?? null,
                        'weight'                => $variation->weight ?? null, // WooCommerce returns as string
                        'dimension_l'           => $variation->dimensions->length ?? null,
                        'dimension_w'           => $variation->dimensions->width ?? null,
                        'dimension_d'           => $variation->dimensions->height ?? null,
                        'updated_by' => auth()->id() ?? 1,
                    ];
                    $existing_variation = VariationPlatform::where('platform_id', $platform_id)
                        ->where('ecommerce_id', $variation->id)
                        ->value('variation_id');

                    if ($existing_variation) {
                        $productVariation = Variation::find($existing_variation);
                        $productVariation->update($variationData);
                    } else {
                        $variationData['created_by'] = auth()->id() ?? 1;
                        $productVariation = Variation::create($variationData);
                    }
                    if (empty($variation->sku)) {
                        $productVariation->refreshSku();
                    }


                    // Sync attribute items for this variation
                    $attributeItemIds = [];
                    foreach ($variation->attributes ?? [] as $attr) {
                        if (empty($attr->name) || empty($attr->option)) continue;

                        $attribute = Attribute::firstOrCreate(
                            ['name' => $attr->name],
                            ['status' => Attribute::STATUS_ACTIVE]
                        );

                        $attributeItem = AttributeItem::firstOrCreate(
                            ['name' => $attr->option, 'attribute_id' => $attribute->id]
                        );

                        $attributeItemIds[] = $attributeItem->id;

                        // ProductAttribute::firstOrCreate([
                        //     'product_id' => $productModel->id,
                        //     'attribute_id' => $attribute->id,
                        //     'attribute_item_id' => $attributeItem->id,
                        // ]);
                    }

                    $productVariation->attributeItems()->sync($attributeItemIds);



                    // Save platform-specific data only if variation exists
                    if ($productVariation) {
                        VariationPlatform::updateOrCreate(
                            [
                                'variation_id' => $productVariation->id,
                                'platform_id'          => $platform_id,
                            ],
                            [
                                'ecommerce_id' => $variation->id,
                                'platform_data' => $variation, // cast as array in model
                            ]
                        );

                        logger()->info("Saved Variation", ['variation_id' => $productVariation->id]);
                    }
                }
            } else {
                logger()->info("No variations found for this product.");
            }

            // Update parent product price with lowest variation price
            // $productModel->update([
            //     'price' => $lowestPrice ?? 0,
            //     'customer_buying_price' => $lowestPrice ?? 0,
            // ]);

            // Create variant stock
            $this->createVariantStock($productModel->id, $variationFlags, $defaultWarehouse->id);
            return $productModel;
        }
    }


    public function downloadWooImageAndUpload($imageUrl, $oldName = null, $uploadPath = null)
    {
        logger('called download image and upload');
        if (!$imageUrl) {
            return null;
        }

        try {
            // Download the image from WooCommerce
            $response = Http::get($imageUrl);

            if ($response->successful()) {
                // Save temporarily
                $tmpPath = storage_path('app/tmp_' . basename(parse_url($imageUrl, PHP_URL_PATH)));
                file_put_contents($tmpPath, $response->body());

                // Create UploadedFile instance (so it behaves like a real uploaded file)
                $file = new UploadedFile(
                    $tmpPath,
                    basename($tmpPath),
                    mime_content_type($tmpPath),
                    null,
                    true // Mark as test mode, so it doesn't check HTTP upload
                );

                // Reuse your existing method
                $uploadedFileName = $this->uploadFile($file, $oldName, $uploadPath);

                // Delete temporary file
                @unlink($tmpPath);

                return $uploadedFileName;
            } else {
                Log::warning("WooCommerce image download failed", [
                    'url' => $imageUrl,
                    'status' => $response->status()
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to download/upload WooCommerce image", [
                'url' => $imageUrl,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return null;
    }

    public function uploadFile($file, $old_name = null, $upload_path = null)
    {
        logger('called upload file');
        try {
            $fileUploadService = resolve("App\Services\Utils\FileUploadService");

            $path = $upload_path ? $upload_path : $this->model::FILE_STORE_PATH;

            if ($old_name) {
                // Delete and upload
                $fileUploadService->delete($path . '/' . $old_name);
                return $fileUploadService->upload($file, ($path ?? null));
            } else {
                // Upload new
                return $fileUploadService->upload($file, ($path ?? null));
            }
        } catch (\Throwable $e) {
            Log::error("File upload failed", [
                'file' => $file instanceof UploadedFile ? $file->getClientOriginalName() : 'unknown',
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function wooBackordersAllowed($item): bool
    {
        $manageStock = $item->manage_stock ?? false;
        $stockStatus = $item->stock_status ?? null;
        $backorders  = $item->backorders ?? 'no';

        // Case 1: No stock management
        if ($manageStock == false && ($stockStatus == 'instock' || $stockStatus == 'onbackorder')) {
            return true;
        }

        // Case 2: Manage stock enabled
        if ($manageStock === true && in_array($backorders, ['yes', 'notify'])) {
            return true;
        }

        // Otherwise false
        return false;
    }

    // ---------------------- Helper Methods -----------------------

    private function createNormalStock($productId, $warehouseId, $api_product)
    {
        $stockQty = $api_product->manage_stock ? $api_product->stock_quantity : 0;
        $price = $api_product->regular_price ?? $api_product->price ?? 0;
        $customerPrice = $api_product->regular_price ?? $api_product->price ?? 0;


        $productService = resolve("App\Services\Product\ProductStockService");
        $productService->normalStockUpdate([
            "is_variant" => "0",
            "alert_quantity" => "",
            "backorders_allowed" => $this->wooBackordersAllowed($api_product),
            "manage_stock" => $api_product->manage_stock,
            "warehouse_stock" => [
                [
                    "warehouse" => $warehouseId,
                    "stock" => $stockQty,
                    "quantity" => $stockQty,
                    "price" => $price,
                    "customer_buying_price" => $customerPrice,
                    "adjust_type" => null
                ]
            ],
        ], $productId);
    }

    // private function createVariantStock($productId, $attributeData, $warehouseId)
    // {
    //     $warehouseStock = [
    //         'warehouse' => $warehouseId,
    //         'stock' => [],
    //         'quantity' => [],
    //         'price' => [],
    //         'customer_buying_price' => [],
    //         'adjust_type' => 'Set' // Use 'Set' for initial import
    //     ];

    //     foreach ($attributeData as $attribute) {
    //         $attributeId = $attribute['attribute'];

    //         foreach ($attribute['attribute_items'] as $itemId => $itemData) {
    //              if(isset($itemData['backorders'])) {
    //                 $backordersAllowed = $itemData['backorders'];
    //             }
    //             $attributeItemId = $itemData['attribute_item_id'];

    //             $qty = $itemData['quantity'] ?? 0;
    //             $price = $itemData['price'] ?? 0;
    //             $customerPrice = $itemData['customer_buying_price'] ?? $price;

    //             // Set values for each attribute combination
    //             $warehouseStock['stock'][$attributeId][$attributeItemId] = $qty;
    //             $warehouseStock['quantity'][$attributeId][$attributeItemId] = $qty;
    //             $warehouseStock['price'][$attributeId][$attributeItemId] = $price;
    //             $warehouseStock['customer_buying_price'][$attributeId][$attributeItemId] = $customerPrice;
    //             $warehouseStock['backorders_allowed'][$attributeId][$attributeItemId] = $backordersAllowed;
    //             $warehouseStock['manage_stock'][$attributeId][$attributeItemId] = $itemData['manage_stock'];

    //         }
    //     }

    //     $stockData = [
    //         'is_variant' => '1',
    //         'alert_quantity' => '10',
    //         'warehouse_stock' => [$warehouseStock],
    //         'supplier_id' => '1'
    //     ];



    //     $productService = resolve("App\Services\Product\ProductStockService");
    //     $productService->variantStockUpdate($stockData, $productId);
    // }
    private function createVariantStock($productId, $variationFlags, $warehouseId)
    {
        $variations = Variation::where('product_id', $productId)->get();
        $warehouse_stock = [];

        foreach ($variations as $key => $variation) {
            if (!isset($warehouse_stock[$warehouseId])) {
                $warehouse_stock[$warehouseId] = [
                    "warehouse" => $warehouseId,
                    "stock" => [],
                    "quantity" => [],
                    "price" => [],
                    "customer_buying_price" => [],
                    "variation_id" => [],
                    "adjust_type" => null,
                    "backorders_allowed" => [],
                    "manage_stock" => []
                ];
            }

            $warehouse_stock[$warehouseId]["variation_id"][$variation->id] = $variation->id;
            $warehouse_stock[$warehouseId]["stock"][$variation->id] = $variation->stock_quantity ?? "0";
            $warehouse_stock[$warehouseId]["quantity"][$variation->id] = $variation->stock_quantity ?? "0";
            $warehouse_stock[$warehouseId]["price"][$variation->id] = $variation->price ?? "0";
            $warehouse_stock[$warehouseId]["customer_buying_price"][$variation->id] = $variation->customer_buying_price ?? "0";
            $warehouse_stock[$warehouseId]["backorders_allowed"][$variation->id] = $variationFlags[$key]['backorders'] ?? false;
            $warehouse_stock[$warehouseId]["manage_stock"][$variation->id] = $variationFlags[$key]['manage_stock'] ?? false;
        }

        $warehouse_stock = array_values($warehouse_stock);
        // logger('warehouse_stock: ', $warehouse_stock);
        $productStockService = resolve("App\Services\Product\ProductStockService");
        //     $productService->variantStockUpdate($stockData, $productId);
        $productStockService->variantStockUpdate([
            "is_variant" => "1",
            "alert_quantity" => "",
            "warehouse_stock" => $warehouse_stock,
        ], $productId);
    }




    public function fetchOrders($platform_id, $credential, $progress_id)
    {
        $perPage = 10; // adjust per store capacity
        $page = 1;
        $totalOrders = 0;

        do {
            try {
                $orders = $this->woocommerceConnection($credential)->get('orders', [
                    'per_page' => $perPage,
                    'page'     => $page,
                    'order'    => 'desc',
                    'orderby'  => 'id'
                ]);

                logger('orders fetched page ' . $page . ' count: ' . count($orders));

                if (count($orders) === 0) {
                    break;
                }

                // Dispatch job for this page
                FetchWooOrdersJob::dispatch($orders, $platform_id, $credential, $page, $progress_id);

                // Count total orders
                $totalOrders += count($orders);

                $page++;

                if (count($orders) < $perPage) {
                    break;
                }
            } catch (\Exception $e) {
                logger()->error("Failed to fetch orders from WooCommerce: {$e->getMessage()}");
                break;
            }
        } while (true);

        // Update total count in progress tracking
        $progress = \App\Models\ProgressTracking::find($progress_id);
        if ($progress) {
            $progress->total = $totalOrders;
            // If there are no orders at all, mark as completed immediately
            if ($totalOrders === 0) {
                $progress->status = 'completed';
            }
            $progress->save();
        }

        logger()->info("Total orders queued: " . $totalOrders);
    }


    protected function storeOrUpdateOrder($order, $platformId)
    {
        logger('calling storeOrupdateOrder');
        $userId = auth()->id() ?? 1;
        $warehouseId = Warehouse::where('is_default', 1)->value('id');

        // Map WooCommerce status to local invoice status
        $total = $order->total ?? 0;



        // Billing info
        $billing = [
            'name' => trim(($order->billing->first_name ?? '') . ' ' . ($order->billing->last_name ?? '')),
            'email' => $order->billing->email ?? null,
            'phone' => $order->billing->phone ?? null,
            'address_line_1' => $order->billing->address_1 ?? null,
            'address_line_2' => $order->billing->address_2 ?? null,
            'city' => $order->billing->city ?? null,
            'state' => $order->billing->state ?? null,
            'zip' => $order->billing->postcode ?? null,
            'country' => $order->billing->country ?? null,
        ];

        // Shipping info
        $shipping = [
            'name' => trim(($order->shipping->first_name ?? '') . ' ' . ($order->shipping->last_name ?? '')),
            'email' => $order->shipping->email ?? null,
            'phone' => $order->shipping->phone ?? null,
            'address_line_1' => $order->shipping->address_1 ?? null,
            'address_line_2' => $order->shipping->address_2 ?? null,
            'city' => $order->shipping->city ?? null,
            'state' => $order->shipping->state ?? null,
            'zip' => $order->shipping->postcode ?? null,
            'country' => $order->shipping->country ?? null,
            'shipping_total' => $order->shipping_total ?? 0,
        ];

        // Save or update invoice
        $invoice = Invoice::updateOrCreate(
            ['token' => $order->id],
            [
                'date' => Carbon::parse($order->date_created)->format('Y-m-d H:i:s'),
                'due_date' => Carbon::parse($order->date_created)->format('Y-m-d H:i:s'),
                'customer_id' => null, // map local customer if exists
                'customer' => [
                    'name' => $billing['name'],
                    'email' => $billing['email'],
                    'phone' => $billing['phone'],
                ],
                'billing_info' => $billing,
                'shipping_info' => $shipping,
                'items_data' => $order->line_items ?? [],
                'tax_amount' => $order->total_tax ?? 0,
                'discount_amount' => $order->discount_total ?? 0,
                'global_discount' => $order->discount_total ?? 0,
                'global_discount_type' => 'fixed',
                'total' => $total,
                'total_paid' => $total,
                'last_paid' => $total,
                'payment_type' => $order->payment_method_title ?? null,
                'notes' => $order->customer_note ?? null,
                'status' => $order->status ?? 'pending',
                'warehouse_id' => $warehouseId,
                'created_by' => $userId,
            ]
        );

        // Save InvoicePlatform mapping
        InvoicePlatform::updateOrCreate(
            [
                'ecommerce_id' => $order->id,
                'platform_id' => $platformId,
            ],
            [
                'invoice_id' => $invoice->id,
                'platform_data' => (array)$order,
                'is_active'   => true
            ]
        );
        InvoiceItem::where('invoice_id', $invoice->id)->delete();
        // Save invoice items
        foreach ($order->line_items ?? [] as $item) {
            $wooProductId = $item->product_id;
            $variationId = $item->variation_id;
            $productName = strip_tags($item->name ?? 'N/A');
            logger('product name' . $productName);
            $stockId = null;
            $localProductId = null;
            $sku = null;

            if ($wooProductId) {
                // Find parent product mapping
                $productPlatform = ProductPlatform::where('ecommerce_id', $wooProductId)
                    ->where('platform_id', $platformId)
                    ->first();

                $localProductId = $productPlatform?->product_id;
                $sku            = $productPlatform?->product?->sku;

                if ($variationId > 0) {
                    // Find variation mapping
                    $variationPlatform = VariationPlatform::where('ecommerce_id', $variationId)
                        ->where('platform_id', $platformId)
                        ->first();

                    $productVariation = $variationPlatform?->variation;

                    if ($productVariation) {
                        $sku = $productVariation->sku ?? $sku;

                        // Directly match stock by variation_id
                        $stockId = ProductStock::where('product_id', $localProductId)
                            ->where('variation_id', $productVariation->id)
                            ->where('warehouse_id', $warehouseId)
                            ->first()?->id;
                    }
                } else {
                    // Simple product, no variation
                    $stockId = ProductStock::where('product_id', $localProductId)
                        ->whereNull('variation_id')
                        ->where('warehouse_id', $warehouseId)
                        ->first()?->id;
                }
            }
            $quantity   = $item->quantity ?? 0;
            $lineTotal  = $item->total ?? 0;       // after discount (final charge)
            $lineTax    = $item->total_tax ?? 0;   // total tax for the line
            $unitPrice  = $quantity > 0 ? ($lineTotal / $quantity) : ($item->price ?? 0);

            InvoiceItem::create([
                'invoice_id'       => $invoice->id,
                'product_id'       => $localProductId,
                'product_stock_id' => $stockId,
                'product_name'     => $productName,
                'sku'              => $sku,
                'quantity'         => $item->quantity ?? 0,
                'price'            => $unitPrice,
                'tax'              => $lineTax,
                'discount'         => ($item->subtotal - $lineTotal) ?? 0,
                'discount_type'    => 'fixed',
                'sub_total'        => $lineTotal,
                'created_by'       => $userId,
            ]);



            // logger('saved invoice item: ' , [$saved_item]);
        }

        // Save payment if order is paid/processing
        if (in_array($order->status, ['completed', 'processing'], true)) {
            $paidAt = $order->date_paid ?? $order->date_paid_gmt ?? $order->date_created;

            InvoicePayment::updateOrCreate(
                [
                    'invoice_id' => $invoice->id,
                    'date' => Carbon::parse($paidAt)->format('Y-m-d H:i:s'),
                ],
                [
                    'payment_type' => $order->payment_method_title ?? null,
                    'amount' => $order->total ?? 0,
                    'notes' => 'WooCommerce Payment',
                    'created_by' => $userId,
                ]
            );
        }

        return $invoice;
    }


    public function fetchAndStoreAttributes($platformId, $credential, $progress_id = null)
    {
        logger()->info('fetchAndStoreAttributes: ' . $platformId);
        $woocommerce = $this->woocommerceConnection($credential);
        $allAttributes = [];
        $page = 1;
        $perPage = 100;

        try {
            // Fetch all attributes page by page
            do {
                $attributes = $woocommerce->get('products/attributes', [
                    'per_page' => $perPage,
                    'page' => $page
                ]);
                if (!empty($attributes)) {
                    $allAttributes = array_merge($allAttributes, $attributes);
                }
                if (count($attributes) < $perPage) {
                    break;
                }
                $page++;
            } while (true);
            // logger('all attribiutes', $allAttributes);
            $total = count($allAttributes);
            $progress = $progress_id ? ProgressTracking::find($progress_id) : null;

            if ($progress) {
                $progress->total = $total;
                // If there are no orders at all, mark as completed immediately
                if ($total === 0) {
                    $progress->status = 'completed';
                }
                $progress->save();
            }
            $processed = 0;

            foreach ($allAttributes as $attr) {
                // Update or create Attribute
                $attributeModel = \App\Models\Attribute::updateOrCreate(
                    ['name' => $attr->name], // Match by name
                    ['status' => Attribute::STATUS_ACTIVE]
                );

                // Update or create platform linkage
                \App\Models\AttributePlatform::updateOrCreate(
                    [
                        'attribute_id' => $attributeModel->id,
                        'platform_id' => $platformId,
                        'ecommerce_id' => $attr->id
                    ],
                    ['platform_data' => $attr]
                );

                // Fetch attribute items/terms
                $pageTerm = 1;
                do {
                    $terms = $woocommerce->get("products/attributes/{$attr->id}/terms", [
                        'per_page' => $perPage,
                        'page' => $pageTerm
                    ]);

                    foreach ($terms as $term) {
                        \App\Models\AttributeItem::updateOrCreate(
                            [
                                'attribute_id' => $attributeModel->id,
                                'name' => $term->name
                            ],
                            [
                                'color' => $term->color ?? null,
                                'image' => $term->image ?? null
                            ]
                        );
                    }

                    if (count($terms) < $perPage) {
                        break;
                    }

                    $pageTerm++;
                } while (true);

                $processed++;
                if ($progress && $progress->status === 'running') {
                    $progress->increment('processed');
                    // calculate % if total > 0
                    if ($progress->total > 0 && $progress->processed >= $progress->total) {
                        $progress->status = 'completed';
                    }

                    $progress->save();
                }
            }


            return $total;
        } catch (\Exception $e) {
            logger("WooCommerce attribute sync error: " . $e->getMessage());
            return 0;
        }
    }


    public function fetchAttributeItems($platform_id, $credential)
    {

        $attributes = Attribute::whereHas('attributePlatforms', function ($query) use ($platform_id) {
            $query->where('platform_id', $platform_id);
        })
            ->get();


        foreach ($attributes as $attribute) {
            $wooCommercePlatform = $attribute->attributePlatforms()
                ->where('platform_id', $platform_id)
                ->first();

            if ($wooCommercePlatform) {
                $ecommerce_id = $wooCommercePlatform->ecommerce_id;

                // Fetch terms using WooCommerce API
                $terms = $this->woocommerceConnection($credential)->get(
                    "products/attributes/{$ecommerce_id}/terms"
                );

                foreach ($terms as $term) {
                    $data = [
                        'name' => $term->name,
                        'attribute_id' => $attribute->id,
                    ];

                    // Sync terms in AttributeItem table
                    AttributeItem::updateOrCreate(
                        ['attribute_id' => $attribute->id, 'name' => $term->name],
                        $data
                    );
                }
            }
        }

        return count($attributes);
    }
    public function createOrDeleteWebhook($is_enabled, $credential)
    {
        if (!$credential->is_connected) {
            $this->removeReference($credential->id);
        }
        try {
            $woocommerce = $this->woocommerceConnection($credential);

            $topics = [
                'order.created',
                'order.updated',
                'order.deleted',
                'product.created',
                'product.updated',
                'product.deleted',
                'customer.created',
                'customer.updated',
                'customer.deleted',
            ];

            $delivery_url = route('woocommerce-webhook.receive', ['platform_id' => $credential->id]);
            // Get existing webhooks with error handling
            try {
                $existing_webhooks = $woocommerce->get('webhooks');
            } catch (\Exception $e) {
                logger("Failed to fetch existing webhooks: " . $e->getMessage());
                $existing_webhooks = [];
            }

            // Normalize to array
            if (is_object($existing_webhooks)) {
                $existing_webhooks = json_decode(json_encode($existing_webhooks), true);
            }

            if (!is_array($existing_webhooks)) {
                $existing_webhooks = [];
            }
            foreach ($topics as $topic) {
                $webhook_id = $this->findExistingWebhook($existing_webhooks, $delivery_url, $topic);

                if ($is_enabled && !$webhook_id) {
                    // Create new webhook if it doesn’t exist
                    $this->createWebhook($woocommerce, $topic, $delivery_url);
                } elseif (!$is_enabled && $webhook_id) {
                    // Delete webhook if it exists
                    $this->deleteWebhook($woocommerce, $webhook_id, $topic);
                }
            }

            return true;
        } catch (\Exception $e) {
            logger("Webhook error: " . $e->getMessage() . " on line " . $e->getLine());
            return false;
        }
    }
    public function removeReference($platform_id)
    {
        DB::transaction(function () use ($platform_id) {

            $tables = [
                \App\Models\ProductPlatform::class,
                \App\Models\InvoicePlatform::class,
                \App\Models\CustomerPlatform::class,
                \App\Models\ProductCategoryPlatform::class,
                \App\Models\AttributePlatform::class,
            ];

            foreach ($tables as $model) {
                $updated = $model::where('platform_id', $platform_id)
                    ->update(['is_active' => false]);

                Log::info("Deactivated {$updated} records in {$model} for platform_id {$platform_id}");
            }
        });
    }
    private function deleteWebhook($woocommerce, $webhook_id, $topic)
    {
        try {
            $woocommerce->delete("webhooks/{$webhook_id}", ['force' => true]);
        } catch (\Exception $e) {
            logger("Failed to delete webhook {$webhook_id} for {$topic}: " . $e->getMessage());
        }
    }
    public function deleteProduct($recordId, $storeId)
    {
        if ($pp = ProductPlatform::where(['id' => $storeId, 'ecommerce_id' => $recordId])->first()) {
            $pp->delete();
            optional($pp->product)->delete(); // soft-delete if you prefer
        }
        return response()->json(['message' => 'Product deleted']);
    }
    public function deleteOrder($recordId, $storeId)
    {
        $ip = InvoicePlatform::where('ecommerce_id', $recordId)
            ->where('platform_id', $storeId)
            ->first();

        if ($ip) {
            $invoice = $ip->invoice;
            if ($invoice) {
                $invoice->delete();
            }
            $ip->delete();
        }

        return response()->json(['message' => 'Order deleted']);
    }

    private function findExistingWebhook($webhooks, $delivery_url, $topic)
    {
        foreach ($webhooks as $webhook) {
            // Convert object to array if needed
            if (is_object($webhook)) {
                $webhook = (array) $webhook;
            }

            if (
                isset($webhook['delivery_url'], $webhook['topic']) &&
                $webhook['delivery_url'] === $delivery_url &&
                $webhook['topic'] === $topic
            ) {
                return $webhook['id'] ?? null;
            }
        }
        return null;
    }


    private function updateWebhook($woocommerce, $webhook_id, $is_enabled, $topic)
    {
        try {
            $response = $woocommerce->put("webhooks/{$webhook_id}", [
                'status' => $is_enabled ? 'active' : 'disabled',
            ]);
        } catch (\Exception $e) {
            logger("Failed to update webhook {$webhook_id} for {$topic}: " . $e->getMessage());
        }
    }

    private function createWebhook($woocommerce, $topic, $delivery_url)
    {
        try {
            $woocommerce->post('webhooks', [
                'name'         => 'Webhook for ' . $topic,
                'topic'        => $topic,
                'delivery_url' => $delivery_url,
                'status'       => 'active',
            ]);
            logger("Created webhook for {$topic}");
        } catch (\Exception $e) {
            logger("Failed to create webhook for {$topic}: " . $e->getMessage());
        }
    }




    //Customers
    public function fetchCustomers($platform_id, $credential)
    {
        $page = 1;
        $customers = [];
        $all_customers = [];
        do {
            try {
                $count_customers = [];


                $customers = $this->woocommerceConnection($credential)->get('customers', array('per_page' => 100, 'page' => $page));
                if ($customers) {
                    $all_customers = array_merge($all_customers, $customers);
                    $count_customers = array_merge($count_customers, $customers);
                }


                // sleep(1);

            } catch (HttpClientException $e) {
                die("Can't get orders: $e");
            }

            $page++;
        } while (count($count_customers) > 0);

        return $all_customers;
    }
    public function storeOrUpdateCustomer($webhook_customer = null, $platform_id, $credential, $progress_id)
    {
        $wooCustomers = $webhook_customer ? [$webhook_customer] : $this->fetchCustomers($platform_id, $credential);
        // logger($wooCustomers);
        $totalCustomers = count($wooCustomers);
        $progress = ProgressTracking::find($progress_id);

        if ($progress) {
            $progress->total = $totalCustomers;
            // If there are no orders at all, mark as completed immediately
            if ($totalCustomers === 0) {
                $progress->status = 'completed';
            }
            $progress->save();
        }
        $processed = 0;

        foreach ($wooCustomers as $wooCustomer) {
            if (!$wooCustomer || empty($wooCustomer->email)) {
                $processed++;
                $progress->total = $totalCustomers;
                $progress->status = 'completed';
                $progress->save();
                continue; // Skip invalid/no-email customers
            }

            DB::beginTransaction();
            try {
                // Save or update customer
                $customer = Customer::updateOrCreate(
                    ['email' => $wooCustomer->email],
                    [
                        'first_name'      => $wooCustomer->first_name ?? ($wooCustomer->billing->first_name ?? 'N/A'),
                        'last_name'       => $wooCustomer->last_name ?? ($wooCustomer->billing->last_name ?? 'N/A'),
                        'phone'           => $wooCustomer->billing->phone ?? 'N/A',
                        'avatar'          => $wooCustomer->avatar_url ? $this->downloadWooImageAndUpload($wooCustomer->avatar_url, null, Customer::FILE_STORE_PATH) : '',
                        // 'company'         => $wooCustomer->billing->company ?? null,
                        // 'designation'     => null,
                        'address_line_1'  => $wooCustomer->billing->address_1 ?? 'N/A',
                        'address_line_2'  => $wooCustomer->billing->address_2 ?? null,
                        'zipcode'         => $wooCustomer->billing->postcode ?? '00000',
                        'billing_same'    => $this->isBillingSame($wooCustomer) ? 1 : 0,
                        'b_first_name'    => $wooCustomer->shipping->first_name ?? null,
                        'b_last_name'     => $wooCustomer->shipping->last_name ?? null,
                        'b_email'         => $wooCustomer->email ?? null,
                        'b_phone'         => $wooCustomer->billing->phone ?? null,
                        'b_address_line_1' => $wooCustomer->shipping->address_1 ?? null,
                        'b_address_line_2' => $wooCustomer->shipping->address_2 ?? null,
                        'b_zipcode'       => $wooCustomer->shipping->postcode ?? null,
                        'status'          => Customer::STATUS_ACTIVE,
                    ]
                );

                // Store relation with platform
                $platformCustomer = CustomerPlatform::firstOrNew([
                    'customer_id' => $customer->id,
                    'platform_id' => $platform_id,
                ]);

                $platformCustomer->ecommerce_id  = $wooCustomer->id;
                $platformCustomer->platform_data = json_encode($wooCustomer);
                $platformCustomer->is_active     = true;
                $platformCustomer->save();
                

                DB::commit();

                // Update progress after each customer
                $processed++;
                if ($progress && $progress->status === 'running') {
                    $progress->increment('processed');

                    // calculate % if total > 0
                    if ($progress->total > 0 && $progress->processed >= $progress->total) {
                        $progress->status = 'completed';
                    }

                    $progress->save();
                }
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error("Failed to store WooCommerce customer (ID: {$wooCustomer->id}): " . $e->getMessage());
            }
        }
    }

    /**
     * Check if billing and shipping are the same
     */
    private function isBillingSame($wooCustomer): bool
    {
        if (!$wooCustomer->billing || !$wooCustomer->shipping) {
            return false;
        }

        return (
            $wooCustomer->billing->first_name === $wooCustomer->shipping->first_name &&
            $wooCustomer->billing->last_name === $wooCustomer->shipping->last_name &&
            $wooCustomer->billing->address_1 === $wooCustomer->shipping->address_1 &&
            $wooCustomer->billing->address_2 === $wooCustomer->shipping->address_2 &&
            $wooCustomer->billing->postcode === $wooCustomer->shipping->postcode &&
            $wooCustomer->billing->country === $wooCustomer->shipping->country
        );
    }
    public function syncWooCommerceStock($platform_id, $productId, $warehouse_stock)
    {
        $platform = Platform::find($platform_id);
        if (!$platform) return;

        $wc = $this->woocommerceConnection($platform);

        $productPlatform = ProductPlatform::where('product_id', $productId)
            ->where('platform_id', $platform_id)
            ->first();

        if (!$productPlatform) return;

        $wooProductId = $productPlatform->ecommerce_id;

        // ✅ For variable products
        if ($productPlatform->product->is_variant) {

            foreach ($warehouse_stock as $warehouse) {
                foreach ($warehouse['quantity'] as $variationId => $quantity) {

                    // Get the ProductStock row for this variation & warehouse
                    $productStock = ProductStock::where('variation_id', $variationId)
                        ->where('warehouse_id', $warehouse['warehouse'])
                        ->first();



                    if (!$productStock) continue;

                    // Find corresponding WooCommerce variation
                    $variationPlatform = VariationPlatform::where('variation_id', $variationId)
                        ->where('platform_id', $platform_id)
                        ->first();



                    if ($variationPlatform && $productStock->manage_stock) {
                        //    logger('variationPlatform', [$variationPlatform]);

                        try {
                            $wcVariation = $wc->get("products/{$wooProductId}/variations/{$variationPlatform->ecommerce_id}");
                            $currentStock = $wcVariation->stock_quantity ?? 0;

                            // Handle stock adjustment type
                            if ($warehouse['adjust_type'] === "Add") {
                                $newStock = $currentStock + (int) $quantity;
                            } elseif ($warehouse['adjust_type'] === "Subtract") {
                                $newStock = max(0, $currentStock - (int) $quantity);
                            } else {
                                $newStock = (int) $quantity; // overwrite
                            }

                            // Update variation stock in WooCommerce
                            $wc->put("products/{$wooProductId}/variations/{$variationPlatform->ecommerce_id}", [
                                'stock_quantity' => $newStock,
                                'manage_stock'   => true,
                            ]);
                        } catch (\Exception $e) {
                            \Log::error("WooCommerce stock sync failed", [
                                'product_id'       => $productId,
                                'woo_product_id'   => $wooProductId,
                                'woo_variation_id' => $variationPlatform->ecommerce_id,
                                'error'            => $e->getMessage()
                            ]);
                        }
                    } else {
                        \Log::warning("No WooCommerce variation mapping found", [
                            'product_id'    => $productId,
                            'variation_id'  => $variationId,
                            'warehouse_id'  => $warehouse['warehouse']
                        ]);
                    }
                }
            }
        } else {
            // ✅ For simple products
            try {
                foreach ($warehouse_stock as $item) {
                    $stock = ProductStock::where('product_id', $productId)
                        ->where('warehouse_id', $item['warehouse'])
                        ->first();

                    if ($stock && $stock->manage_stock) {
                        $wcProduct = $wc->get("products/{$wooProductId}");
                        $currentStock = $wcProduct->stock_quantity ?? 0;

                        if ($item['adjust_type'] === "Add") {
                            $newStock = $currentStock + (int) $item['quantity'];
                        } elseif ($item['adjust_type'] === "Subtract") {
                            $newStock = max(0, $currentStock - (int) $item['quantity']);
                        } else {
                            $newStock = (int) $item['stock']; // overwrite
                        }

                        $wc->put("products/{$wooProductId}", [
                            'stock_quantity' => $newStock,
                            'manage_stock'   => true,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error("WooCommerce stock sync failed", [
                    'product_id'     => $productId,
                    'woo_product_id' => $wooProductId,
                    'error'          => $e->getMessage()
                ]);
            }
        }
    }





    public function updateWooCommerceStoresStock($platform_id, $data)
    {
        // 1️⃣ Get all products linked to this platform
        $productPlatforms = \App\Models\ProductPlatform::with('product')
            ->where('platform_id', $platform_id)
            ->get();

        if ($productPlatforms->isEmpty()) {
            return false; // nothing to sync
        }
        $credential = Platform::find($platform_id);
        $wc = $this->woocommerceConnection($credential);

        // 2️⃣ Loop through each product platform
        foreach ($data['product_stock_id'] as $key => $product_stock_id) {
            $product_id  = $data['product_id'][$key];
            $receivedQty = $data['receive_quantity'][$key] ?? 0;

            if ($receivedQty <= 0) continue;

            $pp = $productPlatforms->firstWhere('product_id', $product_id);
            if (!$pp || !$pp->product) continue;

            $product = $pp->product;

            try {
                $productStock = ProductStock::find($product_stock_id);
                // Simple product
                if (!$product->is_variant && $productStock->manage_stock) {
                    $wcProduct = $wc->get('products/' . $pp->ecommerce_id);
                    $currentStock = $wcProduct->stock_quantity ?? 0;

                    $wc->put('products/' . $pp->ecommerce_id, [
                        'stock_quantity' => $currentStock + $receivedQty,

                    ]);
                }
                // Variable product
                else {
                    // Get the stock row (already contains product_id, attribute_id, attribute_item_id)
                    if ($productStock) {
                        // Find the WooCommerce variation using attribute mapping
                        $variationPlatform = VariationPlatform::where('variation_id', $productStock->variation_id)
                            ->where('platform_id', $platform_id)
                            ->first();


                        if ($variationPlatform && $productStock->manage_stock) {
                            $wcVariation = $wc->get("products/{$pp->ecommerce_id}/variations/{$variationPlatform->ecommerce_id}");
                            $currentStock = $wcVariation->stock_quantity ?? 0;
                            logger("stock id" . $productStock->id . "currentStock" . $currentStock . 'receivedQty' . $receivedQty);
                            $wc->put("products/{$pp->ecommerce_id}/variations/{$variationPlatform->ecommerce_id}", [
                                'stock_quantity' => $currentStock + $receivedQty,

                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error("WooCommerce stock update failed for product {$product->id}: " . $e->getMessage());
            }
        }

        return true;
    }
    public function updateWooCommerceStoresStockForItem($platform_id, $credential, $product_stock_id, $quantity)
    {
        $productStock = ProductStock::find($product_stock_id);
        $pp = ProductPlatform::with('product')
            ->where('platform_id', $platform_id)
            ->where('product_id', $productStock->product_id)
            ->first();

        if (!$pp || !$pp->product) return;

        $wc = $this->woocommerceConnection($credential);
        $product = $pp->product;

        if (!$product->is_variant && $productStock->manage_stock) {
            $wcProduct = $wc->get('products/' . $pp->ecommerce_id);
            $currentStock = $wcProduct->stock_quantity ?? 0;
            $wc->put('products/' . $pp->ecommerce_id, [
                'stock_quantity' => $currentStock - $quantity,

            ]);
        } else {

            if ($productStock && $productStock->manage_stock) {
                $variationPlatform = VariationPlatform::where('variation_id', $productStock->variation_id)
                    ->where('platform_id', $platform_id)
                    ->first();

                if ($variationPlatform) {
                    $wcVariation = $wc->get("products/{$pp->ecommerce_id}/variations/{$variationPlatform->ecommerce_id}");
                    $currentStock = $wcVariation->stock_quantity ?? 0;
                    logger("stock id" . $productStock->id . "currentStock" . $currentStock . 'requestedQty' . $quantity);
                    $wc->put("products/{$pp->ecommerce_id}/variations/{$variationPlatform->ecommerce_id}", [
                        'stock_quantity' => $currentStock - $quantity,

                    ]);
                }
            }
        }
    }


   
}
