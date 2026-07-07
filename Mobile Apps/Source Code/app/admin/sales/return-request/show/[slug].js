import React from "react";
import Topbar from "../../../../../components/Topbar/Topbar";
import { useLocalSearchParams } from "expo-router";
import { ScrollView, StyleSheet, View } from "react-native";
import {
  useSalesReturnListDetailsQuery,
  useSalesReturnRequestListDetailsQuery,
} from "../../../../../redux/features/sales-return/salesReturnApi";
import Text from "../../../../../components/text/Text";
import { generate8DigitNumber } from "../../../../../utils/helper";
import { useSelector } from "react-redux";
import { colors } from "../../../../../themes/colors";

const ReturnListShow = () => {
  const { slug } = useLocalSearchParams();
  //sales return list details
  const { data: salesReturnListDetails } =
    useSalesReturnRequestListDetailsQuery(slug);
  const { invoice, sale_return_request_items } =
    salesReturnListDetails?.data || {};

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  const returnTotal = () => {
    let total = 0;
    sale_return_request_items?.map((item) => {
      total += parseFloat(item?.return_sub_total);
    });
    return total;
  };
  return (
    <>
      <Topbar title="Return Request Details" />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 180 }}>
        <View style={styles.invoiceCard}>
          <View style={styles.topTile}>
            <View style={{ flex: 1 }}>
              <Text
                preset="h2_sb"
                style={{
                  textAlign: "right",
                  color: "#10A0B1",
                  marginBottom: 5,
                }}
              >
                Invoice#{generate8DigitNumber(slug)}
              </Text>
              <Text
                preset="h5_m"
                style={{
                  textAlign: "right",
                  color: "#727F8B",
                  marginBottom: 4,
                }}
              >
                Customer name: {invoice?.customer?.full_name}
              </Text>
              <Text
                preset="h5_m"
                style={{
                  textAlign: "right",
                  color: "#727F8B",
                  marginBottom: 4,
                }}
              >
                Customer phone: {invoice?.customer?.phone}
              </Text>
              <Text
                preset="h5_m"
                style={{ textAlign: "right", color: "#727F8B" }}
              >
                Date : {invoice?.date ? invoice?.date : "N/A"}
              </Text>
            </View>
          </View>
          <View style={styles.address}>
            <View style={{ width: "45%" }}>
              <Text preset="h5_m" style={{ color: "#727F8B", marginBottom: 8 }}>
                Billed To :
              </Text>

              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {invoice?.billing_info?.address_line_1},{" "}
                {invoice?.billing_info?.address_line_2}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {invoice?.billing_info?.country}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {invoice?.billing_info?.state}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {invoice?.billing_info?.zip}
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

              <Text
                preset="h6"
                style={{
                  color: "#727F8B",
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                {invoice?.shipping_info?.address_line_1 ||
                  invoice?.billing_info?.address_line_1}
                ,{" "}
                {invoice?.shipping_info?.address_line_2 ||
                  invoice?.billing_info?.address_line_2}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: "#727F8B",
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                {invoice?.shipping_info?.country ||
                  invoice?.billing_info?.country}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: "#727F8B",
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                {invoice?.shipping_info?.state || invoice?.billing_info?.state}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: "#727F8B",
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                {invoice?.shipping_info?.zip || invoice?.billing_info?.zip}
              </Text>
            </View>
          </View>
          <View>
            <View style={styles.invoiceTableHead}>
              <Text preset="h4" style={{ width: 80, color: colors.pcolor }}>
                DESCRIPTION
              </Text>
              <Text preset="h4" style={{ width: 60, color: colors.pcolor }}>
                RATE
              </Text>
              <Text preset="h4" style={{ width: 30, color: colors.pcolor }}>
                QTY
              </Text>
              <Text
                preset="h4"
                style={{ width: 70, color: colors.pcolor, textAlign: "right" }}
              >
                SUBTOTAL
              </Text>
            </View>
            <View
              style={{
                paddingHorizontal: 20,
                paddingVertical: 10,
                borderBottomColor: "#adb5bd4d",
                borderBottomWidth: 1,
              }}
            >
              {sale_return_request_items?.map((item, index) => (
                <View style={styles.tableBody} key={index}>
                  <Text
                    preset="h6_m"
                    style={{ width: 80, color: colors.black }}
                  >
                    {item?.product_name}
                  </Text>
                  <Text
                    preset="h6_m"
                    style={{ width: 60, color: colors.black }}
                  >
                    {currency_symbol} {item?.return_price}
                  </Text>
                  <Text
                    preset="h6_m"
                    style={{ width: 30, color: colors.black }}
                  >
                    {item?.return_qty}
                  </Text>
                  <Text
                    preset="h6_m"
                    style={{
                      width: 70,
                      color: colors.black,
                      textAlign: "right",
                    }}
                  >
                    {currency_symbol} {item?.return_sub_total}
                  </Text>
                </View>
              ))}
            </View>
          </View>
          <View style={styles.calculation}>
            <View style={styles.itemCalculation}>
              <Text
                preset="h5"
                style={{ color: colors.pcolor, marginBottom: 8 }}
              >
                Total
              </Text>
              <Text
                preset="h6_m"
                style={{ color: colors.black, marginBottom: 8 }}
              >
                {currency_symbol} {returnTotal().toFixed(2)}
              </Text>
            </View>
          </View>
        </View>
      </ScrollView>
    </>
  );
};

export default ReturnListShow;

const styles = StyleSheet.create({
  topTile: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 20,
    gap: 20,
    borderBottomColor: "#adb5bd4d",
    borderBottomWidth: 1,
  },
  invoiceCard: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    position: "relative",
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
  invoiceTableHead: {
    paddingHorizontal: 20,
    paddingVertical: 15,
    flexDirection: "row",
    justifyContent: "space-between",
    borderBottomColor: "#adb5bd4d",
    borderBottomWidth: 1,
  },
  invoiceTableHead: {
    paddingHorizontal: 20,
    paddingVertical: 15,
    flexDirection: "row",
    justifyContent: "space-between",
    borderBottomColor: "#adb5bd4d",
    borderBottomWidth: 1,
  },
  tableBody: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingVertical: 10,
  },
  calculation: {
    padding: 20,
    borderBottomColor: "#adb5bd4d",
    borderBottomWidth: 1,
  },
  itemCalculation: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 5,
  },
});
