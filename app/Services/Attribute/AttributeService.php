<?php

namespace App\Services\Attribute;

use Throwable;
use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Utils\FileUploadService;

/**
 * AttributeService
 */
class AttributeService extends BaseService
{
    protected $fileUploadService;

    /**
     * __construct
     *
     * @param mixed $model
     * @return void
     */
    public function __construct(Attribute $model)
    {
        parent::__construct($model);
        $this->fileUploadService = app(FileUploadService::class);
    }
    public function all()
    {
        return $this->model->orderBy('status','asc')->paginate(10);
    }
    /**
     * getItemsByAttributeId
     *
     * @param mixed $id
     * @return void
     */
    public function getItemsByAttributeId($id)
    {
        return AttributeItem::where('attribute_id', $id)->get();
    }

    /**
     * createOrUpdate
     *
     * @param mixed $data
     * @param mixed $id
     * @return void
     */
    public function createOrUpdate(array $data, $id = null)
    {
        try {
            if ($id) {
                try {
                    DB::beginTransaction();
                    $data['updated_by'] = Auth::id();
                    $attribute = $this->model->findOrFail($id)->update($data);

                    // Upload attribute items
                      if (!empty($data['item_data'])) {
                        $submittedIds = collect($data['item_data'])->pluck('id')->filter()->toArray();

                            AttributeItem::where('attribute_id', $id)
                                ->whereNotIn('id', $submittedIds)
                                ->delete();

                            foreach ($data['item_data'] as $item) {
                                AttributeItem::updateOrCreate(
                                    [
                                        'id' => $item['id'] ?? null,
                                        'attribute_id' => $id,
                                    ],
                                    [
                                        'name' => $item['name'],
                                        'color' => $item['color'],
                                        'image' => isset($item['image'])
                                            ? (strpos($item['image'], 'base64') !== false
                                                ? $this->fileUploadService->uploadBase64($item['image'], AttributeItem::FILE_STORE_PATH)
                                                : $this->uploadFile($item['image'], null, AttributeItem::FILE_STORE_PATH))
                                            : ($item['old_image'] ?? ''),
                                    ]
                                );
                            }

                        } else {
                            // If no items submitted, delete all existing items
                            AttributeItem::where('attribute_id', $id)->delete();
                        }


                    DB::commit();

                    return $attribute;
                } catch (Throwable $th) {
                    DB::rollback();
                    throw $th;
                }
            } else {

                try {
                    DB::beginTransaction();

                    $data['created_by'] = Auth::id();
                    $attribute = $this->model::create($data);

                    // Upload attribute items
                    if (isset($data['item_data'])) {
                        foreach ($data['item_data'] as $item) {
                            $attribute_item = new AttributeItem();
                            $attribute_item->attribute_id = $attribute->id;
                            $attribute_item->name = $item['name'];
                            $attribute_item->color = $item['color'];
                            // If has image
                            if (isset($item['image'])) {
                                if (strpos($item['image'], 'base64') !== false) {
                                    $attribute_item->image = $this->fileUploadService->uploadBase64($item['image'], AttributeItem::FILE_STORE_PATH);
                                }else{
                                    $attribute_item->image = $this->uploadFile($item['image'], null, AttributeItem::FILE_STORE_PATH);

                                }
                                //$attribute_item->image = $this->uploadFile($item['image'], null, AttributeItem::FILE_STORE_PATH);
                            }

                            $attribute_item->save();
                        }
                    }

                    DB::commit();

                    return $attribute;
                } catch (Throwable $th) {
                    DB::rollback();
                    throw $th;
                }
            }
        } catch (Throwable $th) {
            throw $th;
        }
    }

    /**
     * delete
     *
     * @param mixed $id
     * @return void
     */
    public function delete($id)
    {
        try {
            // Delete attribute item
            $items = AttributeItem::where('attribute_id', $id)->get();
            foreach ($items as $item) {
                $item->delete();
            }
            // Delete attribute
            $data = $this->model::findOrFail($id);
            return $data->delete();
        } catch (Throwable $th) {
            throw $th;
        }
    }
}
