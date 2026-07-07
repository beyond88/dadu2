<?php

namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\DataTables\AttributeDataTable;
use App\Http\Requests\AttributeRequest;
use App\Services\Attribute\AttributeService;

class AttributesController extends Controller
{
    protected $attributeService;

    /**
     * __construct
     *
     * @param  mixed $attributeService
     * @return void
     */
    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;


        $this->middleware(['permission:List Attribute'])->only(['index']);
        $this->middleware(['permission:Add Attribute'])->only(['create']);
        $this->middleware(['permission:Edit Attribute'])->only(['edit']);
        $this->middleware(['permission:Delete Attribute'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AttributeDataTable $dataTable)
    {
        set_page_meta(__('custom.attribute'));
        return $dataTable->render('admin.attributes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        set_page_meta(__('custom.add_attribute'));
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
    */
    public function store(AttributeRequest $request)
    {
        $data = $request->validated();
        if ($attribute =$this->attributeService->createOrUpdate($data)) {
            flash(__('custom.attribute_create_successful'))->success();
         // 2. Sync to WooCommerce (if store_ids selected)
            if ($request->has('store_ids')) {
                foreach ($request->store_ids as $storeId) {
                    dispatch(new \App\Jobs\Products\SyncAttributesJob($storeId,null,$attribute->id));
                    logger('calling sync product attributes to woo commerce job '.$storeId);
                }
            }
        } else {
            flash(__('custom.attribute_create_failed'))->error();
        }

        return redirect()->route('admin.attributes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $attribute = $this->attributeService->get($id, ['items']);

        set_page_meta(__('custom.edit_attribute'));
        return view('admin.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AttributeRequest $request, $id)
    {
        $data = $request->validated();
        if ($this->attributeService->createOrUpdate($data, $id)) {
            flash(__('custom.attribute_updated_successful'))->success();
                   $wooStores = getConnectedWooCommerceStores();
                    if($wooStores->isNotEmpty())
                    {
                        $woocomController = resolve('App\Http\Controllers\Admin\Addon\WooCommerce\WooCommerceController');
                        $woocomController->syncAttributeToStores($id,$request->store_ids ?? []);
                    }
        } else {
            flash(__('custom.attribute_updated_failed'))->error();
        }

        return redirect()->route('admin.attributes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->attributeService->delete($id)) {
            flash(__('custom.attribute_deleted_successful'))->success();
        } else {
            flash(__('custom.attribute_deleted_failed'))->error();
        }

        return redirect()->route('admin.attributes.index');
    }

    // HANDLE AJAX
    /**
     * attributeItems
     *
     * @param  mixed $id
     * @return void
     */
    public function attributeItems($id)
    {
        return $this->attributeService->getItemsByAttributeId($id);
    }
}
