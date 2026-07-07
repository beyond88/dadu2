import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import Text from "../../../../components/text/Text";
import {
  usePurchaseDetailsQuery,
  usePurchaseReceivedMutation,
  usePurchaseReturnMutation,
} from "../../../../redux/features/purchase/purchaseApi";
import { Link, router, useLocalSearchParams } from "expo-router";
import { colors } from "../../../../themes/colors";
import FormDate from "../../../../components/FormDate/FormDate";
import { useSelector } from "react-redux";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";

const PurchaseReturn = () => {
  const [selectedDate, setSelectedDate] = useState(
    new Date().toISOString().slice(0, 10)
  );
  const [quantity, setQuantity] = useState([]);
  const [notes, setNotes] = useState("");
  const [notesError, setNotesError] = useState("");
  const { slug } = useLocalSearchParams();
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  const { data: purchaseDetails } = usePurchaseDetailsQuery(slug);
  const {
    purchase_number,
    supplier,
    warehouse,
    company,
    date,
    notes: purchaseNote,
    short_address,
    address,
    status,
    is_received,
    purchase_items,
    total,
  } = purchaseDetails?.data || {};

  //handle quantity
  const handleQuantity = (
    text,
    index,
    purchaseOrderQty,
    purchaseOrderReceiveQty
  ) => {
    let purchaseOrderReceiveQtyTotal = purchaseOrderReceiveQty?.reduce(
      (a, b) => Number(a) + Number(b.quantity),
      0
    );
    let availableQty =
      Number(purchaseOrderQty) - Number(purchaseOrderReceiveQtyTotal);
    if (Number(text) > availableQty) {
      const newQuantity = [...quantity];
      newQuantity[index] = `${availableQty}`;
      setQuantity(newQuantity);
    } else {
      const newQuantity = [...quantity];
      newQuantity[index] = text;
      setQuantity(newQuantity);
    }
  };

  //handle receive purchase
  const [
    purchaseReturn,
    { data: purchaseReturnData, isLoading, isSuccess, isError, error },
  ] = usePurchaseReturnMutation();
  const handleSubmit = () => {
    const data = {
      return_date: "2024-01-16",
      return_note: notes,
      product_id: purchase_items?.map((item) => `${item?.product?.id}`),
      purchase_item_id: purchase_items?.map((item) => `${item?.id}`),
      product_stock_id: purchase_items?.map(
        (item) => `${item?.product?.stock_id}`
      ),
      return_quantity: quantity,
      receive_quantity: purchase_items?.map((item) =>
        item?.receive_items?.reduce(
          (a, b) => `${Number(a) + Number(b.quantity)}`,
          0
        )
      ),
      return_price: purchase_items?.map(
        (item, index) => `${Number(item?.price) * Number(quantity[index])}`
      ),
      return_sub_total: purchase_items?.map(
        (item, index) => `${Number(item?.price) * Number(quantity[index])}`
      ),
      total: purchase_items?.reduce(
        (total, item, index) =>
          Number(total) + Number(item?.price) * Number(quantity[index]),
        0
      ),
    };
    if (notes && selectedDate) {
      purchaseReturn({ id: slug, body: data });
    } else {
      setNotesError("Note is required");
    }
  };

  //show error & success message

  useEffect(() => {
    if (isSuccess) {
      showMessage({
        message: purchaseReturnData.message,
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
  }, [purchaseReturnData, isSuccess, isError, error]);

  return (
    <>
      {isLoading && <Loading />}
      <Topbar title="Return Purchase" />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 20 }}>
        <View style={[styles.invoiceCard, { marginBottom: 20 }]}>
          <View style={styles.address}>
            <View style={{ width: "45%" }}>
              <Text
                preset="h6"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Purchase No: {purchase_number}
              </Text>
              <Text
                preset="h6"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Supplier: {supplier?.full_name}
              </Text>
              <Text
                preset="h6"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Supplier No: {supplier?.phone}
              </Text>
              <Text
                preset="h6"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Warehouse: {warehouse}
              </Text>
              <Text
                preset="h6"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Company: {company}
              </Text>
              <Text
                preset="h6"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Date: {date}
              </Text>
              <Text
                preset="h6"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Note: {purchaseNote}
              </Text>
              <Text
                preset="h6"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Short Address: {short_address}
              </Text>
            </View>
            <View style={{ width: "45%" }}>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                Address Line 1: {address?.address_line_1}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                Address Line 2: {address?.address_line_2}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                Country: {address?.country}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                State: {address?.state}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 8,
                  textAlign: "right",
                }}
              >
                City: {address?.city}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 10,
                  textAlign: "right",
                }}
              >
                Status:{" "}
                {status === "Confirmed" ? (
                  <Text style={styles.statusBadge}>{status}</Text>
                ) : (
                  <Text style={[styles.statusBadge, styles.cancel]}>
                    {status}
                  </Text>
                )}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 8,
                  textAlign: "right",
                }}
              >
                <Text style={{ marginBottom: 8 }}>Received:</Text>{" "}
                {is_received === "received" ? (
                  <Text style={[styles.statusBadge, { marginTop: 8 }]}>
                    {is_received}
                  </Text>
                ) : (
                  <Text style={[styles.statusBadge, styles.not_received]}>
                    {is_received}
                  </Text>
                )}{" "}
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
                Return Note <Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <TextInput
                style={[styles.textInput, styles.notesInput]}
                multiline={true}
                placeholder="Note"
                onChangeText={(text) => setNotes(text)}
              />
              {notesError && (
                <Text style={styles.validationError}>{notesError}</Text>
              )}
            </View>
          </View>
        </View>
        {purchase_items?.map((item, index) => (
          <View
            style={[styles.invoiceCard, { marginBottom: 20 }]}
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
                Product Name
              </Text>
              <Text style={styles.itemRight}>{item?.product?.name}</Text>
            </View>
            <View style={styles.tableItem}>
              <Text
                preset="h5"
                style={[
                  styles.itemLeft,
                  { width: "100%", textAlign: "center" },
                ]}
              >
                Purchase Receive
              </Text>
            </View>
            <View style={styles.tableItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Quantity
              </Text>
              <Text style={styles.itemRight}>
                {item?.receive_items?.reduce(
                  (a, b) => Number(a) + Number(b.quantity),
                  0
                ) || item?.quantity}
              </Text>
            </View>
            <View style={styles.tableItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Price
              </Text>
              <Text style={styles.itemRight}>
                {currency_symbol} {item?.price}
              </Text>
            </View>
            <View style={styles.tableItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Sub total
              </Text>
              <Text style={styles.itemRight}>
                {currency_symbol} {item?.sub_total}
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
                Product stock
              </Text>
            </View>
            <View style={styles.tableItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Quantity
              </Text>
              <Text style={styles.itemRight}>{item?.stock_quantity}</Text>
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
              <Text style={styles.itemRight}>
                {item?.return_items?.reduce(
                  (a, b) => Number(a) + Number(b.quantity),
                  0
                )}
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
                Purchase Return
              </Text>
            </View>
            <View style={styles.tableItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Quantity
              </Text>
              <Text style={styles.itemRight}>
                <View>
                  <TextInput
                    style={styles.textInput}
                    value={`${
                      quantity[index] == undefined ? 0 : quantity[index]
                    }`}
                    onChangeText={(text) =>
                      handleQuantity(
                        text,
                        index,
                        item?.quantity,
                        item?.return_items
                      )
                    }
                    editable={
                      item?.quantity ==
                      item?.return_items?.reduce(
                        (a, b) => Number(a) + Number(b.quantity),
                        0
                      )
                        ? false
                        : true
                    }
                  />
                  <Text style={{ color: colors.red, marginTop: 5 }}>
                    {item?.quantity ==
                      item?.return_items?.reduce(
                        (a, b) => Number(a) + Number(b.quantity),
                        0
                      ) && "No Available Quantity for Receive"}
                  </Text>
                </View>
              </Text>
            </View>
            <View style={styles.tableItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Price
              </Text>
              <Text style={styles.itemRight}>
                {" "}
                {currency_symbol}{" "}
                {(Number(item?.price) * Number(quantity[index] || 0)).toFixed(
                  2
                ) || 0}
              </Text>
            </View>
            <View style={styles.tableItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Sub total
              </Text>
              <Text style={styles.itemRight}>
                {currency_symbol}{" "}
                {(Number(item?.price) * Number(quantity[index] || 0)).toFixed(
                  2
                )}
              </Text>
            </View>
          </View>
        ))}
        <View style={styles.formActionBtn}>
          <View style={{ flexDirection: "row", gap: 10 }}>
            <Pressable style={styles.formBtn} onPress={handleSubmit}>
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
    </>
  );
};

export default PurchaseReturn;

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
  notesInput: {
    height: 80,
    paddingTop: 10,
  },
  statusBadge: {
    backgroundColor: colors.green,
    paddingHorizontal: 8,
    color: colors.white,
    borderRadius: 2,
    paddingVertical: 3,
    textTransform: "capitalize",
  },
  cancel: {
    backgroundColor: colors.red,
  },
  not_received: {
    backgroundColor: colors.yellow,
    marginTop: 8,
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
  validationError: {
    color: "#ff0000",
    marginTop: 5,
  },
});
