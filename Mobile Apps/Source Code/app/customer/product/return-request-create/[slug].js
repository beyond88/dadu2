import { useEffect, useState } from "react";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import Topbar from "../../../../components/Topbar/Topbar";
import Text from "../../../../components/text/Text";
import FormDate from "../../../../components/FormDate/FormDate";
import { colors } from "../../../../themes/colors";
import {
  useGetReturnRequestDetailsQuery,
  useProductReturnRequestMutation,
} from "../../../../redux/features/invoice-return/invoiceReturnApi";
import { useLocalSearchParams, useRouter } from "expo-router";
import Loading from "../../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";
import { LinearGradient } from "expo-linear-gradient";

const ReturnRequestCreate = () => {
  const [selectedDate, setSelectedDate] = useState("2023-11-20");
  const [notes, setNotes] = useState("");
  const [salesItems, setSalesItems] = useState([]);
  const [returnTotal, setReturnTotal] = useState(0);
  const [salesTotalAmount, setSalesTotalAmount] = useState(0);
  const { slug } = useLocalSearchParams();

  const router = useRouter();
  //product return request

  const [
    productReturnRequest,
    {
      data: productReturnRequestData,
      isSuccess: productReturnIsSuccess,
      isLoading: productReturnIsLoading,
      isError: productReturnIsError,
      error: productReturnError,
    },
  ] = useProductReturnRequestMutation();

  //get return request details
  const { data: returnRequestDetailData, isSuccess: returnDetailsIsSuccess } =
    useGetReturnRequestDetailsQuery(slug);
  var { sales, warehouse } = returnRequestDetailData?.data || {};

  useEffect(() => {
    if (returnDetailsIsSuccess) {
      setSalesItems(sales);
    }
  }, [returnDetailsIsSuccess, returnRequestDetailData]);

  //handle notes
  const handleNotes = (text) => {
    setNotes(text);
  };

  //calculate tax price total

  const calculateTaxDiscountPrice = (
    price,
    vatRate,
    discountAmount,
    discount_type
  ) => {
    // Calculate the VAT amount
    let vatAmount = (Number(price) * Number(vatRate)) / 100;
    let discount_amount = 0;
    if (discount_type == "percentage") {
      discount_amount = (Number(price) * Number(discountAmount)) / 100;
    } else {
      discount_amount = Number(discountAmount);
    }
    var totalPrice =
      Number(price) + Number(vatAmount) - Number(discount_amount);

    return totalPrice.toFixed(2);
  };

  //calculate return total quantity
  const calReturnTotalQuantity = (salesReturnItems) => {
    let totalQuantity = salesReturnItems.reduce(
      (a, b) => Number(a) + Number(b.return_qty),
      0
    );

    return totalQuantity;
  };

  //handle return quantity
  const handleQuantity = (text, item) => {
    let quantity = Number(text);
    let totalReturnQuantity = item?.sales_return_items?.reduce(
      (a, b) => Number(a) + Number(b.return_qty),
      0
    );
    const stock = item?.quantity - totalReturnQuantity;
    if (Number(quantity) > Number(stock)) {
      quantity = stock;
    }

    //calculated return price
    const calTotalPrice = calculateTaxDiscountPrice(
      item?.price,
      item?.tax,
      item?.discount,
      item?.discount_type
    );
    const returnPrice = Number(calTotalPrice) * Number(quantity);
    const returnSubTotal = Number(returnPrice) * Number(quantity);
    //add return quantity to  item object

    setSalesItems((prevState) => ({
      ...prevState,
      items: prevState.items.map((sItem) => ({
        ...sItem,
        return_qty:
          sItem.id === item.id ? quantity.toString() : sItem.return_qty,
        return_price:
          sItem.id === item.id ? returnPrice.toFixed(2) : sItem.return_price,
        return_sub_total:
          sItem.id === item.id
            ? returnSubTotal.toFixed(2)
            : sItem.return_sub_total,
      })),
    }));
  };

  //return total calculation

  useEffect(() => {
    let returnTotal = salesItems?.items?.reduce(
      (a, b) => Number(a) + Number(b.return_sub_total || 0),
      0
    );

    setReturnTotal((returnTotal || 0).toFixed(2));
  }, [salesItems]);

  //total sales amount calculation
  useEffect(() => {
    if (returnDetailsIsSuccess) {
      let salesTotalAmount = sales?.items?.reduce(
        (a, b) =>
          Number(a) +
          Number(
            calculateTaxDiscountPrice(
              b?.price,
              b?.tax,
              b?.discount,
              b?.discount_type
            )
          ),
        0
      );
      setSalesTotalAmount((salesTotalAmount || 0).toFixed(2));
    }
  }, [sales, returnDetailsIsSuccess]);

  //const create return request

  const productReturnData = {
    invoice_id: sales?.id,
    warehouse_id: warehouse?.id,
    return_date: selectedDate,
    return_note: notes,
    invoice_details_id: sales?.items?.map((item) => {
      return item?.id;
    }),
    product_id: sales?.items?.map((item) => {
      return item?.product_id;
    }),
    product_stock_id: sales?.items?.map((item) => {
      return item?.product_stock_id;
    }),
    attribute_id: sales?.items_data?.map((item) => {
      return item?.attribute?.id;
    }),
    attribute_item_id: sales?.items_data?.map((item) => {
      return item?.attribute_item?.id;
    }),
    product_sku: sales?.items?.map((item) => {
      return item?.sku;
    }),
    price: sales?.items?.map((item) => {
      return item?.price;
    }),
    discount: sales?.items?.map((item) => {
      return item?.discount;
    }),
    discount_type: sales?.items?.map((item) => {
      return item?.discount_type;
    }),
    product_name: sales?.items?.map((item) => {
      return item?.product_name;
    }),
    return_qty: salesItems?.items?.map((item) => {
      return item?.return_qty || 0;
    }),
    return_price: salesItems?.items?.map((item) => {
      return item?.return_price || 0;
    }),
    return_sub_total: salesItems?.items?.map((item) => {
      return item?.return_sub_total || 0;
    }),
    total: returnTotal,
  };

  //submit return request

  const handleReturnRequest = () => {
    productReturnRequest(productReturnData);
  };

  useEffect(() => {
    if (productReturnIsSuccess) {
      showMessage({
        message: productReturnRequestData?.message,
        type: "success",
      });
      router.push(`/customer/product/return-request`);
    } else if (productReturnIsError) {
      showMessage({
        message: productReturnError?.data?.message,
        type: "danger",
      });
    }
  }, [
    productReturnIsError,
    productReturnError,
    productReturnIsSuccess,
    productReturnRequestData,
  ]);

  return (
    <View>
      {productReturnIsLoading && <Loading />}
      <Topbar title="Return Request Create" customer={true} />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 100 }}>
        <View style={styles.invoiceCard}>
          <View style={styles.salesInfo}>
            <View style={styles.salesInfoItem}>
              <Text preset="h5">Sale Number:</Text>
              <Text>0000006</Text>
            </View>
            <View style={styles.salesInfoItem}>
              <Text preset="h5">Sale Date :</Text>
              <Text>{sales?.date}</Text>
            </View>
            <View style={styles.salesInfoItem}>
              <Text preset="h5">Customer Name :</Text>
              <Text>{sales?.customer?.full_name}</Text>
            </View>
            <View style={styles.salesInfoItem}>
              <Text preset="h5">Customer Phone :</Text>
              <Text>{sales?.customer?.phone}</Text>
            </View>
            <View style={styles.salesInfoItem}>
              <Text preset="h5">Customer Email :</Text>
              <Text>{sales?.customer?.email}</Text>
            </View>
            <View style={styles.salesInfoItem}>
              <Text preset="h5">Warehouse :</Text>
              <Text>{warehouse?.name}</Text>
            </View>
          </View>
          <View style={styles.billingShippingWrap}>
            <View style={styles.billingShippingItem}>
              <Text preset="h3" style={{ marginBottom: 10 }}>
                Billing Info
              </Text>
              <View>
                <Text style={styles.infoItem}>{sales?.billing_info?.name}</Text>
                <Text style={styles.infoItem}>
                  {sales?.billing_info?.email}
                </Text>
                <Text style={styles.infoItem}>
                  {sales?.billing_info?.phone}
                </Text>
                <Text style={styles.infoItem}>
                  {sales?.billing_info?.address_line_1}
                </Text>
                <Text style={styles.infoItem}>
                  {sales?.billing_info?.address_line_2}
                </Text>
                <Text style={styles.infoItem}>
                  {sales?.billing_info?.country}
                </Text>
                <Text style={styles.infoItem}>
                  {sales?.billing_info?.state}
                </Text>
                <Text style={styles.infoItem}>{sales?.billing_info?.city}</Text>
                <Text style={styles.infoItem}>{sales?.billing_info?.zip}</Text>
              </View>
            </View>
            <View style={styles.billingShippingItem}>
              <Text
                preset="h3"
                style={{ marginBottom: 10, textAlign: "right" }}
              >
                Shipping Info
              </Text>
              <View>
                <Text style={[styles.infoItem, { textAlign: "right" }]}>
                  {sales?.billing_info?.name}
                </Text>
                <Text style={[styles.infoItem, { textAlign: "right" }]}>
                  {sales?.shipping_info?.email}
                </Text>
                <Text style={[styles.infoItem, { textAlign: "right" }]}>
                  {sales?.shipping_info?.phone}
                </Text>
                <Text style={[styles.infoItem, { textAlign: "right" }]}>
                  {sales?.shipping_info?.address_line_1}
                </Text>
                <Text style={[styles.infoItem, { textAlign: "right" }]}>
                  {sales?.shipping_info?.address_line_2}
                </Text>
                <Text style={[styles.infoItem, { textAlign: "right" }]}>
                  {sales?.shipping_info?.country}
                </Text>
                <Text style={[styles.infoItem, { textAlign: "right" }]}>
                  {sales?.shipping_info?.state}
                </Text>
                <Text style={[styles.infoItem, { textAlign: "right" }]}>
                  {sales?.shipping_info?.city}
                </Text>
                <Text style={[styles.infoItem, { textAlign: "right" }]}>
                  {sales?.shipping_info?.zip}
                </Text>
              </View>
            </View>
          </View>
          <View style={styles.returnDateNote}>
            <View style={styles.returnDateNoteItem}>
              <Text preset="h5" style={{ marginBottom: 8 }}>
                Return Date
              </Text>
              <FormDate setSelectedDate={setSelectedDate} />
            </View>
            <View style={styles.returnDateNoteItem}>
              <Text preset="h5" style={{ marginBottom: 8 }}>
                Return Note
              </Text>
              <TextInput
                multiline={true}
                style={styles.notesInput}
                onChangeText={(text) => handleNotes(text)}
              />
            </View>
          </View>
        </View>
        {salesItems?.items?.map((item) => (
          <View style={styles.returnCreateWrap} key={item?.id}>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                SKU
              </Text>
              <Text style={styles.itemRight}>{item?.sku}</Text>
            </View>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Product Name
              </Text>
              <Text style={styles.itemRight}>{item?.product_name}</Text>
            </View>
            <View
              style={[styles.returnCreateItem, { justifyContent: "center" }]}
            >
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
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Quantity
              </Text>
              <Text style={styles.itemRight}>{item?.quantity}</Text>
            </View>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Price
              </Text>
              <Text style={styles.itemRight}>
                {calculateTaxDiscountPrice(
                  item?.price,
                  item?.tax,
                  item?.discount,
                  item?.discount_type
                )}
              </Text>
            </View>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Sub Total
              </Text>
              <Text style={styles.itemRight}>
                {(
                  calculateTaxDiscountPrice(
                    item?.price,
                    item?.tax,
                    item?.discount,
                    item?.discount_type
                  ) * item?.quantity || 0
                ).toFixed(2)}
              </Text>
            </View>
            <View
              style={[styles.returnCreateItem, { justifyContent: "center" }]}
            >
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
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Quantity
              </Text>
              <Text style={styles.itemRight}>
                {item?.sales_return_items.length == 0
                  ? item?.quantity
                  : Number(item?.quantity) -
                    calReturnTotalQuantity(item?.sales_return_items)}
              </Text>
            </View>
            <View
              style={[styles.returnCreateItem, { justifyContent: "center" }]}
            >
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
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Quantity
              </Text>
              <View style={styles.itemRight}>
                <TextInput
                  keyboardType="numeric"
                  style={styles.quantityInput}
                  value={item?.return_qty}
                  onChangeText={(text) => handleQuantity(text, item)}
                />
              </View>
            </View>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Price
              </Text>
              <Text style={styles.itemRight}>
                {item?.return_price || "0.00"}
              </Text>
            </View>
            <View style={styles.returnCreateItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Subtotal
              </Text>
              <Text style={styles.itemRight}>
                {item?.return_sub_total || "0.00"}
              </Text>
            </View>
          </View>
        ))}
        <View style={styles.returnCreateWrap}>
          <View style={styles.returnCreateItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Sales Total
            </Text>
            <Text style={styles.itemRight}>{salesTotalAmount || "0.00"}</Text>
          </View>
          <View style={styles.returnCreateItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Return Total
            </Text>
            <Text style={styles.itemRight}>
              {returnTotal ? returnTotal : "0.00"}
            </Text>
          </View>
        </View>
        <View style={{ marginTop: 20, justifyContent: "flex-end" }}>
          <Pressable
            onPress={handleReturnRequest}
            style={{ width: 150, marginLeft: "auto" }}
          >
            <LinearGradient
              colors={["#37DBD9", "#008AA1"]}
              style={styles.submitBtn}
            >
              <Text preset="h3" style={{ color: colors.white }}>
                Submit
              </Text>
            </LinearGradient>
          </Pressable>
        </View>
      </ScrollView>
    </View>
  );
};

export default ReturnRequestCreate;

const styles = StyleSheet.create({
  invoiceCard: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    position: "relative",
    padding: 20,
  },
  salesInfo: {
    justifyContent: "flex-end",
    flex: 1,
    marginBottom: 10,
  },
  salesInfoItem: {
    flexDirection: "row",
    gap: 8,
    alignItems: "center",
    flexWrap: "wrap",
    marginBottom: 10,
  },

  infoItem: {
    marginBottom: 5,
  },
  billingShippingWrap: {
    flexDirection: "row",
    justifyContent: "space-between",
    flexWrap: "wrap",
    gap: 20,
    marginBottom: 20,
  },
  billingShippingItem: {
    width: "46%",
  },
  returnDateNote: {
    flexDirection: "row",
    justifyContent: "space-between",
    flexWrap: "wrap",
  },
  returnDateNoteItem: {
    width: "100%",
  },
  notesInput: {
    width: "100%",
    height: 60,
    paddingHorizontal: 16,
    borderColor: colors.lineBorder,
    borderWidth: 1,
    backgroundColor: colors.white,
    borderRadius: 5,
    paddingTop: 10,
  },
  returnCreateWrap: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    marginTop: 20,
  },
  returnCreateItem: {
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
  quantityInput: {
    height: 30,
    borderColor: colors.lineBorder,
    borderWidth: 1,
    width: 80,
    marginLeft: "auto",
  },
  submitBtn: {
    height: 48,
    width: 150,
    alignItems: "center",
    justifyContent: "center",
    borderRadius: 5,
  },
});
