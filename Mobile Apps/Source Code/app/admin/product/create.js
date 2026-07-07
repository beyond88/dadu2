import React, { useEffect, useState } from "react";
import {
  ScrollView,
  View,
  StyleSheet,
  Pressable,
  TextInput,
} from "react-native";
import Topbar from "../../../components/Topbar/Topbar";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { colors } from "../../../themes/colors";
import { Link, useRouter } from "expo-router";
import Text from "../../../components/text/Text";
import { capitalize, generateUniqueId } from "../../../utils/helper";
import FormSelect from "../../../components/Form/FormSelect";
import FormCheckbox from "../../../components/Form/FormCheckbox";
import { Entypo } from "@expo/vector-icons";
import { AntDesign } from "@expo/vector-icons";
import FormRadio from "../../../components/Form/FormRadio";
import * as ImagePicker from "expo-image-picker";
import { Image } from "expo-image";
import BarCode from "../../../components/BarCode/BarCode";
import {
  useAdminGetProductCreateInfoQuery,
  useAdminProductCreateMutation,
} from "../../../redux/features/product/productApi";
import TextEditor from "../../../components/Form/TextEditor";
import Loading from "../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";

//form validation schema
const schema = yup
  .object({
    name: yup.string().required("Name is required"),
    sku: yup.string().required("SKU is required"),
    category: yup.string().required("Category is required"),
    price: yup.string().required("Price is required"),
    status: yup.string().required("Status is required"),
    available_for: yup.string().required("Available for is required"),
  })
  .required();

const CreateProduct = () => {
  //component state
  const [barcodeValue, setBarcodeValue] = useState();
  const [barcodeImage, setBarcodeImage] = useState();
  const [desc, setDesc] = useState();
  const [createUpdateStock, setCreateUpdateStock] = useState(false);
  const [addedAttributeValue, setAddedAttributeValue] = useState([]);
  const [isVariant, setIsVariant] = useState(false);
  const [isSplitSale, setIsSplitSale] = useState(false);
  const [image, setImage] = useState();
  const [fileName, setFileName] = useState(null);
  const [attributeVariant, setAttributeVariant] = useState([]);

  const [addedAttributeItems, setAddedAttributeItems] = useState([
    {
      id: generateUniqueId(),
    },
  ]);

  //router

  const router = useRouter();

  //get product create info
  const { data: productCreateInfo } = useAdminGetProductCreateInfoQuery();
  const {
    attributes,
    categories,
    brands,
    manufacturers,
    weight_units,
    measurement_units,
    skuSetting,
    barcode,
  } = productCreateInfo?.data || {};

  //create product mutation
  const [
    adminProductCreate,
    {
      data: productCreateData,
      isSuccess: pCreateIsSuccess,
      isError: pCreateIsError,
      error: pCreateError,
      isLoading: pCreateIsLoading,
    },
  ] = useAdminProductCreateMutation();
  //get form data
  const {
    control,
    handleSubmit,
    formState: { errors },
    reset,
    setValue,
    watch,
  } = useForm({
    resolver: yupResolver(schema),
  });

  const onSubmit = (data, params) => {
    if (params === "createUpdateStock") {
      setCreateUpdateStock(true);
    } else {
      setCreateUpdateStock(false);
    }

    const formData = new FormData();
    formData.append("category_id", data?.category);
    formData.append("name", data?.name);
    formData.append("sku", data?.sku);
    formData.append("barcode", data?.barcode || "");
    formData.append("barcode_image", barcodeImage);
    formData.append("brand_id", data?.brand || "");
    formData.append("manufacturer_id", data?.manufacture || "");
    formData.append("model", data?.model || "");
    formData.append("price", data?.price || "");
    formData.append("weight", data?.weight || "");
    formData.append("weight_unit_id", data?.weight_unit_id || "");
    formData.append("dimension_l", data?.dimension_l || "");
    formData.append("dimension_w", data?.dimension_w || "");
    formData.append("dimension_d", data?.dimension_d || "");
    formData.append("measurement_unit_id", data?.measurement_unit_id || "");
    formData.append("notes", data?.notes || "");
    formData.append("desc", desc || "");
    formData.append("is_variant", isVariant === true ? 1 : 0);
    formData.append("tax_status", data?.tax_status || "");
    formData.append("status", data?.status || "");
    formData.append("available_for", data?.available_for || "");
    formData.append("is_split_sale", isSplitSale === true ? 1 : 0 || "");
    formData.append("customer_buying_price", data?.customer_buying_price || "");
    if (fileName !== null) {
      formData.append("thumb", {
        uri: image,
        name: fileName,
        type: "image/jpg",
      });
    }
    if (data?.tax_status === "include") {
      formData.append("custom_tax", data?.custom_tax);
    }

    //attribute array append
    const att_data = [];

    for (let i = 0; i < addedAttributeValue.length; i++) {
      const attribute = {
        attribute: addedAttributeValue[i],
        attribute_items: attributeVariant[i],
      };
      att_data.push(attribute);
    }

    formData.append("attribute_data", JSON.stringify(att_data));

    adminProductCreate(formData);
  };

  //watch

  const getBarcodeValue = watch("barcode");
  const taxStatus = watch("tax_status");

  useEffect(() => {
    if (productCreateInfo?.data?.barcode) {
      setValue("barcode", barcode);
      setBarcodeValue(getBarcodeValue || barcode);
    }
  }, [productCreateInfo]);

  useEffect(() => {
    if (getBarcodeValue) {
      setBarcodeValue(getBarcodeValue);
    }
  }, [getBarcodeValue]);

  useEffect(() => {
    if (skuSetting) {
      setValue(
        "sku",
        skuSetting?.auto === "yes" ? skuSetting?.generated_sku : ""
      );
    }
  }, [productCreateInfo]);

  //get categories list

  const categoryItems = [];
  if (categories?.length > 0) {
    categories?.map((category) => {
      categoryItems.push({ label: category.name, value: category.id });
    });
  }

  //get brands list

  const brandItems = [];
  if (brands?.length > 0) {
    brands?.map((brand) => {
      brandItems.push({ label: brand.name, value: brand.id });
    });
  }

  //get manufacture list

  const manufactureItems = [];
  if (manufacturers?.length > 0) {
    manufacturers?.map((manufacture) => {
      manufactureItems.push({ label: manufacture.name, value: manufacture.id });
    });
  }

  //get weight unit list

  const weightUnitItems = [];
  if (weight_units?.length > 0) {
    weight_units?.map((weightUnit) => {
      weightUnitItems.push({ label: weightUnit.name, value: weightUnit.id });
    });
  }

  //get measurement unit list

  const measurementUnitItems = [];
  if (measurement_units?.length > 0) {
    measurement_units?.map((measurementUnit) => {
      measurementUnitItems.push({
        label: measurementUnit.name,
        value: measurementUnit.id,
      });
    });
  }

  //get attribute list

  const attributeItems = [];
  if (attributes?.length > 0) {
    attributes?.map((attribute) => {
      attributeItems.push({
        label: attribute.name,
        value: attribute.id,
      });
    });
  }

  //handle add attribute list

  const handleAddAttributeList = () => {
    setAddedAttributeItems([
      ...addedAttributeItems,
      { id: generateUniqueId() },
    ]);
  };

  const handleDeleteAttributeList = (id, attId, index) => {
    const newAddedAttributeItems = addedAttributeItems.filter(
      (item) => item.id !== id
    );
    //attribute value delete
    const newAddedAttributeValue = addedAttributeValue.filter(
      (item) => item !== attId
    );
    //attribute variant delete with index
    //remove array item from index

    const newAttributeVariant = attributeVariant.filter(
      (item, i) => i !== index
    );

    setAddedAttributeValue(newAddedAttributeValue);
    setAddedAttributeItems(newAddedAttributeItems);
    setAttributeVariant(newAttributeVariant);
  };

  //handle attribute change

  const handleAttributeChange = (value, index) => {
    const newAddedAttributeValue = [...addedAttributeValue];
    newAddedAttributeValue[index] = value;
    setAddedAttributeValue(newAddedAttributeValue);

    //set attribute variant
    const newAttributeVariant = attributeVariant.filter(
      (item, i) => i !== index
    );
    setAttributeVariant(newAttributeVariant);
  };

  //handle attribute variant change

  const attributeVariantChange = (attributeItemId, index) => {
    // Create a new array with the updated checkbox values for the specified index
    const newAttributeVariants = [...attributeVariant];

    // Toggle the checkbox value for the specified index
    if (newAttributeVariants[index]) {
      // If the array already exists, toggle the checkbox value
      const indexOfItem = newAttributeVariants[index].indexOf(attributeItemId);
      if (indexOfItem === -1) {
        newAttributeVariants[index].push(attributeItemId);
      } else {
        newAttributeVariants[index].splice(indexOfItem, 1);
      }
    } else {
      // If the array doesn't exist, create a new array with the checkbox value
      newAttributeVariants[index] = [attributeItemId];
    }

    // Update the state with the new array
    setAttributeVariant(newAttributeVariants);
  };

  //handle is variant change
  const isVariantOnChange = () => {
    setIsVariant(!isVariant);
  };
  //handle is split sale change
  const isSplitSaleOnChange = () => {
    setIsSplitSale(!isSplitSale);
  };

  const pickImage = async () => {
    // No permissions request is necessary for launching the image library
    let result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.All,
      allowsEditing: true,
      aspect: [4, 3],
      quality: 1,
    });

    var filename = result.assets[0].uri.substring(
      result.assets[0].uri.lastIndexOf("/") + 1,
      result.assets[0].uri.length
    );

    if (!result.canceled) {
      setImage(result.assets[0].uri);
      setFileName(filename);
    }
  };

  //show error & success message

  useEffect(() => {
    if (pCreateIsSuccess) {
      showMessage({
        message: productCreateData.message,
        type: "success",
      });

      if (createUpdateStock) {
        router.push(`/admin/product/stock-price/${productCreateData.data.id}`);
      } else {
        router.push("/admin/product");
      }
    }
    if (pCreateIsError) {
      showMessage({
        message: pCreateError.data.message,
        type: "danger",
      });
    }
  }, [productCreateData, pCreateIsSuccess, pCreateIsError, pCreateError]);

  return (
    <>
      {pCreateIsLoading && <Loading />}
      <Topbar title="Create Product" />

      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Name<Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="name"
              />
              {errors.name && (
                <Text style={styles.validationError}>
                  {capitalize(errors.name?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                SKU<Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    editable={skuSetting?.editable == "yes" ? true : false}
                    onBlur={onBlur}
                    value={value == "undefined" ? "" : value || ""}
                    onChangeText={onChange}
                  />
                )}
                name="sku"
              />
              {errors.sku && (
                <Text style={styles.validationError}>
                  {capitalize(errors.sku?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Barcode
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    value={value == "undefined" ? "" : `${value}` || ""}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="barcode"
              />

              <View style={{ marginTop: 10 }}>
                <BarCode
                  barcodeData={`${barcodeValue}`}
                  setBarcodeImage={setBarcodeImage}
                />
              </View>
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Category<Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select Category"
                    items={categoryItems}
                    value={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="category"
              />
              {errors.category && (
                <Text style={styles.validationError}>
                  {capitalize(errors.category?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Brand
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select Brand"
                    items={brandItems}
                    value={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="brand"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Manufacture
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select Manufacture"
                    items={manufactureItems}
                    value={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="manufacture"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Model
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="model"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Price<Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="price"
              />
              {errors.price && (
                <Text style={styles.validationError}>
                  {capitalize(errors.price?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Customer Buying Price
                <Text style={{ color: "#9ca8b3" }}>
                  (If blank then actual price will be buying price)
                </Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="customer_buying_price"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Weight
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="weight"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Weight Unit
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select weight unit"
                    items={weightUnitItems}
                    value={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="weight_unit_id"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Dimension
              </Text>
              <View
                style={{
                  flexDirection: "row",
                  justifyContent: "space-between",
                }}
              >
                <View style={{ width: "32.5%" }}>
                  <Text style={[styles.label, styles.textMuted]} preset="h2_sb">
                    Length
                  </Text>
                  <Controller
                    control={control}
                    render={({ field: { onChange, onBlur, value } }) => (
                      <TextInput
                        style={styles.textInput}
                        onBlur={onBlur}
                        onChangeText={onChange}
                      />
                    )}
                    name="dimension_l"
                  />
                </View>
                <View style={{ width: "32.5%" }}>
                  <Text style={[styles.label, styles.textMuted]} preset="h2_sb">
                    Width
                  </Text>
                  <Controller
                    control={control}
                    render={({ field: { onChange, onBlur, value } }) => (
                      <TextInput
                        style={styles.textInput}
                        onBlur={onBlur}
                        onChangeText={onChange}
                      />
                    )}
                    name="dimension_w"
                  />
                </View>
                <View style={{ width: "32.5%" }}>
                  <Text style={[styles.label, styles.textMuted]} preset="h2_sb">
                    Depth
                  </Text>
                  <Controller
                    control={control}
                    render={({ field: { onChange, onBlur, value } }) => (
                      <TextInput
                        style={styles.textInput}
                        onBlur={onBlur}
                        onChangeText={onChange}
                      />
                    )}
                    name="dimension_d"
                  />
                </View>
              </View>
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Measurement Unit
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select Measurement Unit"
                    items={measurementUnitItems}
                    value={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="measurement_unit_id"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Notes
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                    multiline={true}
                  />
                )}
                name="notes"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Description
              </Text>
              <TextEditor desc={desc} setDesc={setDesc} />
            </View>
            <View style={styles.formGroup}>
              <View
                style={{
                  flexDirection: "row",
                  justifyContent: "space-between",
                  alignItems: "center",
                  marginBottom: 10,
                }}
              >
                <Text
                  style={[styles.label, { marginBottom: "0" }]}
                  preset="h2_sb"
                >
                  Attributes
                </Text>
                <Pressable
                  style={styles.addBtn}
                  onPress={handleAddAttributeList}
                >
                  <Entypo name="plus" size={16} color="white" />
                </Pressable>
              </View>
              {addedAttributeItems?.map((item, index) => (
                <View
                  style={{
                    borderColor: colors.border,
                    borderWidth: 1,
                    borderRadius: 5,
                    padding: 10,
                    marginBottom: 10,
                  }}
                  key={item?.id}
                >
                  <FormSelect
                    placeholder="Select"
                    items={attributeItems}
                    index={index}
                    onChange={handleAttributeChange}
                    height={42}
                    bg={colors.grayBg}
                  />
                  {addedAttributeValue[index] && (
                    <View key={addedAttributeValue[index]}>
                      {attributes
                        ?.filter(
                          (item) => addedAttributeValue[index] === item.id
                        )
                        ?.map((filteredItem) => (
                          <View
                            key={filteredItem.id}
                            style={styles.attributeItemWrap}
                          >
                            {filteredItem.attribute_items?.map(
                              (attributeItem) => (
                                <FormCheckbox
                                  key={attributeItem.id}
                                  label={attributeItem.name}
                                  checked={
                                    attributeVariant[index] &&
                                    attributeVariant[index].includes(
                                      attributeItem.id
                                    )
                                  }
                                  toggleCheckbox={() =>
                                    attributeVariantChange(
                                      attributeItem.id,
                                      index
                                    )
                                  }
                                />
                              )
                            )}
                          </View>
                        ))}
                    </View>
                  )}

                  <Pressable
                    style={[styles.addBtn, styles.deleteBtn]}
                    onPress={() =>
                      handleDeleteAttributeList(
                        item?.id,
                        addedAttributeValue[index],
                        index
                      )
                    }
                  >
                    <AntDesign name="delete" size={16} color="white" />
                  </Pressable>
                </View>
              ))}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Thumb{" "}
                <Text>
                  (Supported type: png, jpg, jpeg | Max size: 300kb |
                  Width:500px, Height:500px)
                </Text>
              </Text>
              <Pressable onPress={pickImage}>
                <Text preset="h3_r" style={styles.chooseFile}>
                  Choose File
                </Text>
              </Pressable>
              {image && (
                <Image
                  source={{ uri: image }}
                  style={{
                    width: "100%",
                    height: 150,
                    borderRadius: 5,
                    marginTop: 10,
                    objectFit: "cover",
                  }}
                />
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Tax/Vat
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormRadio
                    onChange={onChange}
                    value={value}
                    items={[
                      {
                        label: "Include",
                        value: "include",
                      },
                      {
                        label: "Exclude",
                        value: "exclude",
                      },
                    ]}
                  />
                )}
                name="tax_status"
              />
            </View>
            {taxStatus === "include" && (
              <View style={styles.formGroup}>
                <Text style={styles.label} preset="h2_sb">
                  Custom tax amount (%)
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      style={styles.textInput}
                      onBlur={onBlur}
                      onChangeText={onChange}
                    />
                  )}
                  name="custom_tax"
                />
              </View>
            )}

            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Status<Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormRadio
                    onChange={onChange}
                    value={value}
                    items={[
                      {
                        label: "Active",
                        value: "active",
                      },
                      {
                        label: "Inactive",
                        value: "inactive",
                      },
                    ]}
                  />
                )}
                name="status"
              />
              {errors.status && (
                <Text style={styles.validationError}>
                  {capitalize(errors.status?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Available For<Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormRadio
                    onChange={onChange}
                    value={value}
                    items={[
                      {
                        label: "Customer",
                        value: "customer",
                      },
                      {
                        label: "Warehouse",
                        value: "warehouse",
                      },
                      {
                        label: "Both",
                        value: "all",
                      },
                    ]}
                  />
                )}
                name="available_for"
              />
              {errors.available_for && (
                <Text style={styles.validationError}>
                  {capitalize(errors.available_for?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Is variant product
              </Text>
              <FormCheckbox
                checked={isVariant}
                toggleCheckbox={isVariantOnChange}
                label="Is Variant Product"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Is Split sale
              </Text>
              <FormCheckbox
                checked={isSplitSale}
                toggleCheckbox={isSplitSaleOnChange}
                label="Is Split sale"
              />
            </View>
            <View style={styles.formActionBtn}>
              <View style={{ flexDirection: "row", gap: 10 }}>
                <Pressable
                  onPress={() =>
                    handleSubmit((data) => onSubmit(data, "create"))()
                  }
                  style={styles.formBtn}
                >
                  <Text preset="h3" style={styles.btnText}>
                    Submit
                  </Text>
                </Pressable>
                <Pressable
                  onPress={() =>
                    handleSubmit((data) =>
                      onSubmit(data, "createUpdateStock")
                    )()
                  }
                  style={styles.formBtn}
                >
                  <Text preset="h3" style={styles.btnText}>
                    Submit & stock update
                  </Text>
                </Pressable>
              </View>
              <Link href="/admin/product">
                <View style={[styles.formBtn, styles.cancelBtn]}>
                  <Text preset="h3" style={styles.btnText}>
                    Cancel
                  </Text>
                </View>
              </Link>
            </View>
          </View>
        </ScrollView>
      </View>
    </>
  );
};

export default CreateProduct;

const styles = StyleSheet.create({
  formWrap: {
    backgroundColor: "#fff",
    padding: 20,
    borderRadius: 5,
  },
  formGroup: {
    marginBottom: 16,
  },
  label: {
    color: colors.black,
    marginBottom: 10,
  },
  textInput: {
    backgroundColor: colors.grayBg,
    height: 42,
    paddingHorizontal: 15,
    borderRadius: 5,
  },
  validationError: {
    color: "#ff0000",
    marginTop: 5,
  },
  formActionBtn: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  formBtn: {
    backgroundColor: colors.themeColor,
    borderRadius: 5,
    paddingVertical: 10,
    paddingHorizontal: 12,
  },
  btnText: {
    color: colors.white,
  },
  cancelBtn: {
    backgroundColor: colors.red,
  },
  textMuted: {
    color: "#9ca8b3",
  },
  addBtn: {
    backgroundColor: colors.themeColor,
    width: 30,
    height: 30,
    borderRadius: 5,
    justifyContent: "center",
    alignItems: "center",
  },
  deleteBtn: {
    backgroundColor: colors.red,
    width: "100%",
  },
  attributeItemWrap: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 10,
    marginBottom: 15,
  },
  chooseFile: {
    backgroundColor: colors.themeColor,
    color: colors.white,
    paddingVertical: 12,
    paddingHorizontal: 20,
    borderRadius: 5,
    textAlign: "center",
  },
});
