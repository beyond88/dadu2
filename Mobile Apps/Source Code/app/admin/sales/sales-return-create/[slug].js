import React, { useEffect, useMemo, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import Text from "../../../../components/text/Text";
import { Link, router, useLocalSearchParams } from "expo-router";
import {
  useCreateSalesReturnMutation,
  useGetCreateReturnDetailsQuery,
} from "../../../../redux/features/sales-return/salesReturnApi";
import { colors } from "../../../../themes/colors";
import FormDate from "../../../../components/FormDate/FormDate";
import FormSelect from "../../../../components/Form/FormSelect";
import { LinearGradient } from "expo-linear-gradient";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";

const SalesReturnCreate = () => {
  const [selectedDate, setSelectedDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [availableQuantity, setAvailableQuantity] = useState([]);
  const [salesPrice, setSalesPrice] = useState([]); //sales price calculation
  const [returnQuantity, setReturnQuantity] = useState([]);
  const [notes, setNotes] = useState("");
  const [warehouse_id, setWarehouse_id] = useState("");
  //slug
  const { slug } = useLocalSearchParams();
  //get create return details
  const { data: getCreateReturnDetails } = useGetCreateReturnDetailsQuery(
    Number(slug)
  );

  const {
    sale_number,
    sale_date,
    customer_name,
    customer_phone,
    customer_email,
    warehouse,
    warehouses,
    billing_info,
    shipping_info,
    items,
  } = getCreateReturnDetails?.data || {};

  //warehouse items

  const warehouseListItems = [];
  warehouses?.map((item) => {
    warehouseListItems.push({
      label: item.name,
      value: item.id,
    });
  });

  //warehouse onchange
  const wareHouseOnchange = (value) => {
    setWarehouse_id(value);
  };

  //sales price calculation
  const salesPriceCalculation = (price, discount, discountType, taxVat) => {
    if (discountType == "percent") {
      let cal = Number(price) - (Number(price) * Number(discount)) / 100;
      return cal + Number(taxVat);
    } else {
      let cal = Number(price) - Number(discount);
      return cal + Number(taxVat);
    }
  };

  useEffect(() => {
    let salesPriceArray = [];
    items?.map((item) => {
      salesPriceArray.push(
        salesPriceCalculation(
          item?.price,
          item?.discount,
          item?.discount_type,
          item?.tax
        )
      );
    });
    setSalesPrice(salesPriceArray);
  }, [items]);

  //available quantity calculation

  useEffect(() => {
    let availableQuantityArray = [];
    items?.map((item) => {
      let availableQuantity = 0;
      let itemQuantity = Number(item?.quantity);
      if (item?.sales_return_items?.length > 0) {
        item?.sales_return_items?.map((item) => {
          if (itemQuantity == item?.return_qty) {
            availableQuantity = itemQuantity;
          } else {
            availableQuantity =
              itemQuantity - (availableQuantity + Number(item.return_qty));
          }
        });
      } else {
        availableQuantity = itemQuantity;
      }
      availableQuantityArray.push(availableQuantity);
    });
    setAvailableQuantity(availableQuantityArray);
  }, [items]);

  //handle quantity
  const handleQuantity = (text, index) => {
    let quantity = Number(text);

    if (quantity >= Number(availableQuantity[index])) {
      //set return quantity
      let returnQuantityArray = [...returnQuantity];
      returnQuantityArray[index] = availableQuantity[index];
      setReturnQuantity(returnQuantityArray);
    } else {
      //set return quantity
      let returnQuantityArray = [...returnQuantity];
      returnQuantityArray[index] = quantity;
      setReturnQuantity(returnQuantityArray);
    }
  };

  //Create sales return

  const [
    createSalesReturn,
    {
      data: createSalesReturnData,
      isLoading: createIsLoading,
      isSuccess: createIsSuccess,
      isError: createIsError,
      error: createError,
    },
  ] = useCreateSalesReturnMutation();

  const handleCreate = () => {
    const invoice_details_id = items?.map((item) => {
      item?.sales_return_items?.map((item2) => item2?.id);
    });
    const totalPrice = items?.map(
      (item, index) => Number(item?.price) * Number(returnQuantity[index])
    );

    const data = {
      invoice_id: slug,
      return_date: selectedDate,
      return_note: notes,
      warehouse_id:
        warehouse_id || warehouses?.find((item) => item.name == warehouse)?.id,
      invoice_details_id: items?.map((item) => item?.id),
      product_id: items?.map((item) => item?.product_id),
      product_stock_id: items?.map((item) => item?.product_stock_id),
      attribute_id: items?.map((item) => item?.attribute_id),
      attribute_item_id: items?.map((item) => item?.attribute_item_id),
      product_sku: items?.map((item) => item?.sku),
      price: items?.map((item) => item?.price),
      discount: items?.map((item) => item?.discount),
      discount_type: items?.map((item) => item?.discount_type),
      product_name: items?.map((item) => item?.product_name),
      return_qty: returnQuantity,
      return_price: items?.map(
        (item, index) => Number(item?.price) * Number(returnQuantity[index])
      ),
      return_sub_total: items?.map(
        (item, index) => Number(item?.price) * Number(returnQuantity[index])
      ),
      total: totalPrice?.reduce((a, b) => a + b, 0),
    };

    createSalesReturn(data);
  };

  //success & error message
  useEffect(() => {
    if (createIsSuccess) {
      showMessage({
        message: createSalesReturnData?.message,
        type: "success",
      });
      router.push("/admin/sales/return");
    }
    if (createIsError) {
      showMessage({
        message: createError?.data?.message || "Something went wrong",
        type: "danger",
      });
    }
  }, [createSalesReturnData, createError, createIsSuccess, createIsError]);

  return (
    <>
      {createIsLoading && <Loading />}
      <Topbar title="Return Sales" />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 20 }}>
        <View style={[styles.invoiceCard, { marginBottom: 20 }]}>
          <View style={{ padding: 20, paddingBottom: 0 }}>
            <Text preset="h5" style={{ color: "#142A3E", marginBottom: 4 }}>
              Sale Number :{" "}
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {sale_number}
              </Text>
            </Text>
            <Text preset="h5" style={{ color: "#142A3E", marginBottom: 4 }}>
              Sale Date:{" "}
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {sale_date}
              </Text>
            </Text>
            <Text preset="h5" style={{ color: "#142A3E", marginBottom: 4 }}>
              Customer Name:{" "}
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {customer_name}
              </Text>
            </Text>
            <Text preset="h5" style={{ color: "#142A3E", marginBottom: 4 }}>
              Customer phone:{" "}
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {customer_phone}
              </Text>
            </Text>
            <Text preset="h5" style={{ color: "#142A3E", marginBottom: 4 }}>
              Customer Email:{" "}
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {customer_email}
              </Text>
            </Text>
            <Text preset="h5" style={{ color: "#142A3E", marginBottom: 4 }}>
              Warehouse:{" "}
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {warehouse}
              </Text>
            </Text>
          </View>
          <View style={styles.address}>
            <View style={{ width: "45%" }}>
              <Text preset="h5_m" style={{ color: "#727F8B", marginBottom: 8 }}>
                Billed To :
              </Text>
              <Text preset="h3" style={{ color: "#142A3E", marginBottom: 4 }}>
                {billing_info?.name}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {billing_info?.email}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {billing_info?.phone}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {billing_info?.address_line_1} {billing_info?.address_line_2}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {billing_info?.country} {billing_info?.state}{" "}
                {billing_info?.city}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {billing_info?.zip}
              </Text>
            </View>
            <View style={{ width: "45%" }}>
              <Text
                preset="h5_m"
                style={{
                  color: "#727F8B",
                  marginBottom: 8,
                  textAlign: "right",
                }}
              >
                Shipped To :
              </Text>

              <Text preset="h3" style={{ color: "#142A3E", marginBottom: 4 }}>
                {shipping_info?.name}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {shipping_info?.email}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {shipping_info?.phone}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {shipping_info?.address_line_1} {shipping_info?.address_line_2}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {shipping_info?.country} {shipping_info?.state}{" "}
                {shipping_info?.city}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {shipping_info?.zip}
              </Text>
            </View>
          </View>
          <View style={{ padding: 20, paddingBottom: 0 }}>
            <View style={[styles.formGroup, { marginBottom: 0 }]}>
              <Text style={styles.label} preset="h2_sb">
                Return Date <Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <FormDate setSelectedDate={setSelectedDate} />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Return Note
              </Text>
              <TextInput
                style={styles.textInput}
                multiline={true}
                placeholder="Note"
                value={notes}
                onChangeText={(text) => setNotes(text)}
              />
            </View>
            <View>
              <Text style={styles.label} preset="h2_sb">
                Warehouse
              </Text>
              <FormSelect
                items={warehouseListItems}
                placeholder="Select warehouse"
                selectedValue={
                  warehouses?.find((item) => item.name == warehouse)?.id
                }
                onChange={wareHouseOnchange}
                searchable={true}
                zIndex={3000}
                zIndexInverse={2000}
                position={"TOP"}
              />
            </View>
          </View>
        </View>
        <View style={{ paddingBottom: 20 }}>
          {items?.map((item, index) => (
            <View
              key={item?.id}
              style={[styles.invoiceCard, { marginBottom: 20 }]}
            >
              <View style={styles.tableItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  SKU
                </Text>
                <Text style={styles.itemRight}>{item?.sku}</Text>
              </View>
              <View style={styles.tableItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Product Name
                </Text>
                <Text style={styles.itemRight}>{item?.product_name}</Text>
              </View>
              <View style={styles.tableItem}>
                <Text
                  preset="h5"
                  style={[
                    styles.itemLeft,
                    { width: "100%", textAlign: "center" },
                  ]}
                >
                  Sales
                </Text>
              </View>
              <View style={styles.tableItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Quantity
                </Text>
                <Text style={styles.itemRight}>{item?.quantity}</Text>
              </View>
              <View style={styles.tableItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Price <Text>(With Tax And Discount)</Text>
                </Text>
                <Text style={styles.itemRight}>
                  {salesPriceCalculation(
                    item?.price,
                    item?.discount,
                    item?.discount_type,
                    item?.tax
                  )}
                </Text>
              </View>
              <View style={styles.tableItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Sub Total
                </Text>
                <Text style={styles.itemRight}>{item?.sub_total}</Text>
              </View>
              <View style={styles.tableItem}>
                <Text
                  preset="h5"
                  style={[
                    styles.itemLeft,
                    { width: "100%", textAlign: "center" },
                  ]}
                >
                  Available
                </Text>
              </View>
              <View style={styles.tableItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Quantity
                </Text>
                <Text style={styles.itemRight}>
                  {availableQuantity[index] || ""}
                </Text>
              </View>
              <View style={styles.tableItem}>
                <Text
                  preset="h5"
                  style={[
                    styles.itemLeft,
                    { width: "100%", textAlign: "center" },
                  ]}
                >
                  Return
                </Text>
              </View>
              <View style={styles.tableItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Quantity
                </Text>
                <View style={styles.itemRight}>
                  <TextInput
                    style={[styles.textInput]}
                    onChangeText={(text) => handleQuantity(text, index)}
                    keyboardType="numeric"
                    value={returnQuantity[index]?.toString() || ""}
                  />
                </View>
              </View>
              <View style={styles.tableItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Price
                </Text>
                <Text style={styles.itemRight}>
                  {salesPriceCalculation(
                    item?.price,
                    item?.discount,
                    item?.discount_type,
                    item?.tax
                  )}
                </Text>
              </View>
              <View style={styles.tableItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Subtotal
                </Text>
                <Text style={styles.itemRight}>
                  {(
                    Number(salesPrice[index] || 0) *
                    Number(returnQuantity[index] || 0)
                  ).toFixed(2) || 0}
                </Text>
              </View>
            </View>
          ))}
          <View style={styles.download_back_btn}>
            <Link href="/admin/pos-invoice" asChild>
              <View style={styles.back_btn}>
                <Text preset="h3" style={styles.buttonText}>
                  Back
                </Text>
              </View>
            </Link>
            <Pressable onPress={handleCreate}>
              <LinearGradient
                colors={["#37DBD9", "#008AA1"]}
                style={styles.authButton}
              >
                <Text preset="h3" style={styles.buttonText}>
                  Submit
                </Text>
              </LinearGradient>
            </Pressable>
          </View>
        </View>
      </ScrollView>
    </>
  );
};

export default SalesReturnCreate;

const styles = StyleSheet.create({
  invoiceCard: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    position: "relative",
    borderRadius: 5,
  },
  address: {
    padding: 20,
    borderBottomColor: "#adb5bd4d",
    borderBottomWidth: 1,
    flexDirection: "row",
    justifyContent: "space-between",
    gap: 20,
    flexWrap: "wrap",
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

  tableItem: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingHorizontal: 20,
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
  download_back_btn: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  back_btn: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    paddingHorizontal: 20,
    borderRadius: 5,
    elevation: 3,
    height: 40,
    backgroundColor: colors.red,
  },
  authButton: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    paddingHorizontal: 20,
    borderRadius: 5,
    elevation: 3,
    height: 40,
  },
  buttonText: {
    color: colors.white,
  },
});
