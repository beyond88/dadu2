import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import { useForm, Controller, set } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import Text from "../../../../components/text/Text";
import { capitalize } from "../../../../utils/helper";
import { Link, router, useLocalSearchParams } from "expo-router";
import { colors } from "../../../../themes/colors";

import FormSelect from "../../../../components/Form/FormSelect";
import {
  useGetCitiesQuery,
  useGetCountriesQuery,
  useGetStatesQuery,
  useGetWarehouseQuery,
} from "../../../../redux/features/common/commonApi";
import FormDate from "../../../../components/FormDate/FormDate";
import { AntDesign } from "@expo/vector-icons";
import {
  useGetSearchProductQuery,
  usePurchaseCreateMutation,
  usePurchaseUpdateMutation,
} from "../../../../redux/features/purchase/purchaseApi";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";
import { usePurchaseDetailsQuery } from "../../../../redux/features/purchase/purchaseApi";
import { useGetSuppliersQuery } from "../../../../redux/features/supplier/supplierApi";
//form validation schema
const schema = yup
  .object({
    supplier: yup.string().required(),
    warehouse: yup.string().required(),
  })
  .required();
const PurchaseCreate = () => {
  //component state
  const [selectedDate, setSelectedDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [productSearchSkip, setProductSearchSkip] = useState(true);
  const [productKey, setProductKey] = useState("");
  const [productList, setProductList] = useState([]);
  const [selectedProduct, setSelectedProduct] = useState([]);
  const [productNote, setProductNote] = useState([]);
  const [supplierItems, setSupplierItems] = useState([]);
  const [countryItems, setCountryItems] = useState([]);
  const [stateItems, setStateItems] = useState([]);
  const [cityItems, setCityItems] = useState([]);
  const [warehouseItems, setWarehouseItems] = useState([]);

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
  //get purchase id from params
  const { slug } = useLocalSearchParams();
  //get single purchase data
  const { data: purchaseData } = usePurchaseDetailsQuery(slug);

  useEffect(() => {
    if (purchaseData) {
      setSelectedDate(purchaseData?.data?.date);
      setValue("supplier", purchaseData?.data?.supplier?.id);
      setValue("warehouse", purchaseData?.data?.warehouse?.id);
      setValue("company", purchaseData?.data?.company);
      setValue("address_line_1", purchaseData?.data?.address?.address_line_1);
      setValue("address_line_2", purchaseData?.data?.address?.address_line_2);
      setValue("country", `${purchaseData?.data?.address?.country?.id}`);
      setValue("state", purchaseData?.data?.address?.state?.id);
      setValue("city", purchaseData?.data?.address?.city?.id);
      setValue("zipcode", purchaseData?.data?.zipcode);
      setValue("short_address", purchaseData?.data?.short_address);
      setValue("note", purchaseData?.data?.notes);
      setSelectedProduct(
        purchaseData?.data?.purchase_items?.map((item) => ({
          id: item?.id,
          purchase_item_id: item?.id,
          product: {
            sku: item?.product?.sku,
            name: item?.product?.name,
            id: item?.product?.id,
            stock_id: item?.product?.stock_id,
          },
          addedQuantity: item?.quantity,
          price: item?.price,
          calSubTotal: item?.price * item?.quantity,
        }))
      );
      setProductNote(
        purchaseData?.data?.purchase_items?.map(
          (item) => item?.product_note || ""
        )
      );
    }
  }, [purchaseData]);

  const [
    purchaseUpdate,
    { data: updatedData, isLoading, isSuccess, isError, error },
  ] = usePurchaseUpdateMutation();
  const onSubmit = (data) => {
    const updateData = {
      _method: "PUT",
      supplier: data.supplier,
      warehouse: data.warehouse,
      company: data.company,
      date: selectedDate,
      address_line_1: data.address_line_1,
      address_line_2: data.address_line_2,
      country: data.country,
      state: data.state,
      city: data.city,
      zipcode: data.zipcode,
      short_address: data.short_address,
      note: data.note,
      purchase_item_id: selectedProduct?.map(
        (item) => item?.purchase_item_id || null
      ),
      product_stock_id: selectedProduct?.map(
        (item) => item?.product?.stock_id || item?.product?.id
      ),
      product_id: selectedProduct?.map((item) => item?.product?.id),
      quantity: selectedProduct?.map((item) => item?.addedQuantity),
      price: selectedProduct?.map((item) => item?.price),
      product_note: productNote,
      sub_total: selectedProduct?.map((item) => item?.calSubTotal),
      total: selectedProduct?.reduce(
        (acc, cur) => Number(acc) + Number(cur?.calSubTotal),
        0
      ),
    };
    if (selectedProduct?.length > 0) {
      purchaseUpdate({ id: slug, data: updateData });
    } else {
      showMessage({
        message: "Please select product",
        type: "danger",
      });
    }
  };

  useEffect(() => {
    if (isSuccess) {
      reset();
      setSelectedDate(new Date());
      setSelectedProduct([]);
      setProductNote([]);
      showMessage({
        message: updatedData.message,
        type: "success",
      });
      router.push("/admin/purchase");
    }
    if (isError) {
      showMessage({
        message: error.data.message,
        type: "danger",
      });
    }
  }, [updatedData, isSuccess, isError, error]);

  //get suppliers list

  const { data: suppliers } = useGetSuppliersQuery();

  useEffect(() => {
    if (suppliers?.data?.data) {
      const newSupplierItems = suppliers?.data?.data?.map((item) => ({
        label: `${item.fist_name} ${item.last_name}`,
        value: item.id,
      }));
      setSupplierItems(newSupplierItems);
    }
  }, [suppliers]);

  //get warehouses list
  const { data: warehouses } = useGetWarehouseQuery();
  useEffect(() => {
    if (warehouses?.data) {
      const newWarehouseItems = warehouses?.data?.map((item) => ({
        label: `${item.name}`,
        value: item.id,
      }));
      setWarehouseItems(newWarehouseItems);
    }
  }, [warehouses]);

  //get specific form field value

  const country = watch("country");
  const state = watch("state");

  //get country list

  const { data: countryList, isLoading: countryListLoading } =
    useGetCountriesQuery();

  useEffect(() => {
    if (countryList?.data) {
      const newCountryItems = countryList?.data?.map((item) => ({
        label: item.name,
        value: item.id,
      }));
      setCountryItems(newCountryItems);
    }
  }, [countryList]);

  //get state list

  const { data: stateList, isLoading: stateListLoading } =
    useGetStatesQuery(country);

  useEffect(() => {
    if (stateList?.data) {
      const newStateItems = stateList?.data?.map((item) => ({
        label: item.name,
        value: item.id,
      }));
      setStateItems(newStateItems);
    }
  }, [stateList]);

  //get city list

  const { data: cityList, isLoading: cityListLoading } =
    useGetCitiesQuery(state);

  useEffect(() => {
    if (cityList?.data) {
      const newCityItems = cityList?.data?.map((item) => ({
        label: item.name,
        value: item.id,
      }));
      setCityItems(newCityItems);
    }
  }, [cityList]);

  //set product search
  const productKeyChange = (value) => {
    setProductKey(value);
    setProductSearchSkip(false);
  };
  //get search product list

  const { data: searchProductList, isSuccess: searchProductIsSuccess } =
    useGetSearchProductQuery(
      { query: productKey },
      { skip: productSearchSkip }
    );

  useEffect(() => {
    if (searchProductIsSuccess) {
      setProductList(searchProductList?.data);
    }
  }, [searchProductList, searchProductIsSuccess]);

  //handle select product
  const handleSelectProduct = (item) => {
    setSelectedProduct([...selectedProduct, item]);
    setProductList([]);
    setProductKey("");
    setProductSearchSkip(true);
  };
  //handle delete
  const handleDelete = (id) => {
    const newProduct = selectedProduct.filter((item) => item?.id !== id);
    setSelectedProduct(newProduct);
  };

  //handle quantity change

  const handleQuantityChange = (value, price, index) => {
    const subTotal = Number(value) * Number(price);
    const newProduct = [...selectedProduct];
    newProduct[index] = { ...newProduct[index] };
    newProduct[index].addedQuantity = value;
    newProduct[index].calSubTotal = subTotal;
    setSelectedProduct(newProduct);
  };

  //handle product note
  const handleProductNote = (value, index) => {
    const newProductNote = [...productNote];
    newProductNote[index] = value;
    setProductNote(newProductNote);
  };

  return (
    <>
      {isLoading && <Loading />}
      <Topbar title="Edit Purchase" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Company
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    value={value || ""}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="company"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Date <Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <FormDate
                setSelectedDate={setSelectedDate}
                selectedDate={selectedDate}
                bg={"gray"}
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Supplier <Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <View style={{ zIndex: 1 }}>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <FormSelect
                      placeholder="Select Suppliers"
                      items={supplierItems}
                      value={value}
                      selectedValue={value}
                      onChange={onChange}
                      searchable={true}
                      height={42}
                      bg={colors.grayBg}
                      zIndex={3000}
                      zIndexInverse={2000}
                    />
                  )}
                  name="supplier"
                />
              </View>
              {errors.supplier && (
                <Text style={styles.validationError}>
                  {capitalize(errors.supplier?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Warehouse <Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <View style={{ zIndex: 0 }}>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <FormSelect
                      placeholder="Select Warehouse"
                      items={warehouseItems}
                      value={value}
                      selectedValue={value}
                      onChange={onChange}
                      searchable={true}
                      height={42}
                      bg={colors.grayBg}
                      zIndex={1000}
                      zIndexInverse={3000}
                    />
                  )}
                  name="warehouse"
                />
              </View>
              {errors.warehouse && (
                <Text style={styles.validationError}>
                  {capitalize(errors.warehouse?.message)}
                </Text>
              )}
            </View>

            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Address Line 1
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    value={value}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="address_line_1"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Address Line 2
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    value={value}
                    onChangeText={onChange}
                  />
                )}
                name="address_line_2"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Country
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select Country"
                    items={countryItems}
                    value={value}
                    selectedValue={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="country"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                State
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select State"
                    items={stateItems}
                    value={value}
                    selectedValue={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="state"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                City
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select City"
                    items={cityItems}
                    value={value}
                    selectedValue={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="city"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Zip code
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    value={value}
                    onChangeText={onChange}
                  />
                )}
                name="zipcode"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Short address{" "}
                <Text>
                  (if you are not fill up this above address then you can fill
                  this short address)
                </Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={[styles.textInput, styles.textInputDetails]}
                    onBlur={onBlur}
                    value={value}
                    onChangeText={onChange}
                    multiline={true}
                  />
                )}
                name="short_address"
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
                    style={[styles.textInput, styles.textInputDetails]}
                    onBlur={onBlur}
                    value={value}
                    onChangeText={onChange}
                    multiline={true}
                  />
                )}
                name="note"
              />
            </View>

            <View style={{ position: "relative" }}>
              <View style={[styles.formGroup, { marginBottom: 0 }]}>
                <Text style={styles.label} preset="h2_sb">
                  Search Products
                </Text>
                <TextInput
                  style={styles.textInput}
                  value={productKey}
                  onChangeText={(text) => productKeyChange(text)}
                />
              </View>
              <View style={{ marginBottom: 20 }}>
                {productList?.map((item, index) => (
                  <Pressable
                    key={item?.id}
                    style={[
                      styles.productItem,
                      index == productList?.length - 1 &&
                        styles.productLastChild,
                    ]}
                    onPress={() => handleSelectProduct(item)}
                  >
                    <Text>
                      ({item?.product?.sku}) {item?.product?.name}
                    </Text>
                  </Pressable>
                ))}
              </View>
            </View>
          </View>
          <Text style={[styles.label, { marginTop: 20 }]} preset="h2_sb">
            Product <Text style={{ color: "#ff0000" }}> *</Text>
          </Text>
          <View>
            {selectedProduct?.map((item, index) => (
              <View
                style={[styles.formWrap, { marginBottom: 20 }]}
                key={item?.id}
              >
                <View style={styles.tableItem}>
                  <Text preset="h5" style={styles.itemLeft}>
                    SKU
                  </Text>
                  <Text style={styles.itemRight}>{item?.product?.sku}</Text>
                </View>
                <View style={styles.tableItem}>
                  <Text preset="h5" style={styles.itemLeft}>
                    Name
                  </Text>
                  <Text style={styles.itemRight}>{item?.product?.name}</Text>
                </View>
                <View style={styles.tableItem}>
                  <Text preset="h5" style={styles.itemLeft}>
                    Quantity
                  </Text>
                  <View style={styles.itemRight}>
                    <TextInput
                      style={[styles.textInput]}
                      value={selectedProduct[index]?.addedQuantity || ""}
                      onChangeText={(text) =>
                        handleQuantityChange(text, item?.price, index)
                      }
                    />
                  </View>
                </View>
                <View style={styles.tableItem}>
                  <Text preset="h5" style={styles.itemLeft}>
                    Price
                  </Text>
                  <Text style={styles.itemRight}>{item?.price}</Text>
                </View>
                <View style={styles.tableItem}>
                  <Text preset="h5" style={styles.itemLeft}>
                    Note
                  </Text>
                  <View style={styles.itemRight}>
                    <TextInput
                      style={[styles.textInput]}
                      onChangeText={(text) => handleProductNote(text, index)}
                      value={productNote[index] || ""}
                    />
                  </View>
                </View>
                <View style={styles.tableItem}>
                  <Text preset="h5" style={styles.itemLeft}>
                    Sub total
                  </Text>
                  <Text style={styles.itemRight}>
                    {item?.calSubTotal?.toFixed(2)}
                  </Text>
                </View>
                <View style={styles.tableItem}>
                  <Text preset="h5" style={styles.itemLeft}>
                    Action
                  </Text>
                  <Text style={styles.itemRight}>
                    <Pressable onPress={() => handleDelete(item?.id)}>
                      <AntDesign name="delete" size={20} color="red" />
                    </Pressable>
                  </Text>
                </View>
              </View>
            ))}
          </View>
          <View style={styles.formActionBtn}>
            <View style={{ flexDirection: "row", gap: 10 }}>
              <Pressable
                onPress={handleSubmit(onSubmit)}
                style={styles.formBtn}
              >
                <Text preset="h3" style={styles.btnText}>
                  Submit
                </Text>
              </Pressable>
            </View>
            <Link href="/admin/purchase">
              <View style={[styles.formBtn, styles.cancelBtn]}>
                <Text preset="h3" style={styles.btnText}>
                  Cancel
                </Text>
              </View>
            </Link>
          </View>
        </ScrollView>
      </View>
    </>
  );
};

export default PurchaseCreate;

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
  textInputDetails: {
    height: 80,
    paddingTop: 10,
    textAlignVertical: "top",
  },
  validationError: {
    color: "#ff0000",
    marginTop: 5,
  },
  tableItem: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingVertical: 10,
    borderBottomColor: "#E9ECF2",
    borderBottomWidth: 1,
    alignItems: "center",
  },
  itemLeft: {
    width: "32%",
  },
  itemRight: {
    flex: 1,
    textAlign: "right",
  },
  productItem: {
    borderWidth: 1,
    borderColor: colors.border,
    borderTopColor: "transparent",
    padding: 10,
  },
  productLastChild: {
    borderBottomRightRadius: 5,
    borderBottomLeftRadius: 5,
  },
  formActionBtn: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  formBtn: {
    backgroundColor: colors.themeColor,
    borderRadius: 5,
    paddingVertical: 10,
    paddingHorizontal: 20,
  },
  btnText: {
    color: colors.white,
  },
  cancelBtn: {
    backgroundColor: colors.red,
  },
});
