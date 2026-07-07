import React from "react";
import Topbar from "../../../../../components/Topbar/Topbar";
import { ScrollView, StyleSheet, View } from "react-native";
import { useLocalSearchParams } from "expo-router";
import { usePurchaseReceivedDetailsQuery } from "../../../../../redux/features/purchase/purchaseApi";
import { colors } from "../../../../../themes/colors";
import Text from "../../../../../components/text/Text";
import { useSelector } from "react-redux";

const PurchaseReceiveDetails = () => {
  const { slug } = useLocalSearchParams();

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  //get purchases data
  const { data: purchaseReceiveDetails } =
    usePurchaseReceivedDetailsQuery(slug);

  const {
    purchase_number,
    supplier_name,
    supplier_phone,
    warehouse_name,
    company,
    receive_date,
    address_line_1,
    address_line_2,
    country,
    state,
    city,
    short_address,
    received_items,
  } = purchaseReceiveDetails?.data || {};

  const total = received_items?.reduce((acc, item) => {
    return acc + Number(item?.sub_total);
  }, 0);
  return (
    <>
      <Topbar title="Purchase Receive Details" />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 180 }}>
        <View style={styles.invoiceCard}>
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
                Supplier: {supplier_name}
              </Text>
              <Text
                preset="h6"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Supplier No: {supplier_phone}
              </Text>
              <Text
                preset="h6"
                style={{ color: colors.black, marginBottom: 4 }}
              >
                Warehouse: {warehouse_name}
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
                Receive Date: {receive_date}
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
                Address Line 1: {address_line_1}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                Address Line 2: {address_line_2}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                Country: {country}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                State: {state}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                City: {city}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                Short address: {short_address}
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
              {received_items?.map((item, index) => (
                <View style={styles.tableBody} key={index}>
                  <Text
                    preset="h6_m"
                    style={{ width: 80, color: colors.black }}
                  >
                    {item?.product?.name}
                  </Text>
                  <Text
                    preset="h6_m"
                    style={{ width: 60, color: colors.black }}
                  >
                    {currency_symbol} {item?.price}
                  </Text>
                  <Text
                    preset="h6_m"
                    style={{ width: 30, color: colors.black }}
                  >
                    {item?.quantity}
                  </Text>
                  <Text
                    preset="h6_m"
                    style={{
                      width: 70,
                      color: colors.black,
                      textAlign: "right",
                    }}
                  >
                    {currency_symbol} {item?.sub_total}
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
                {currency_symbol} {total?.toFixed(2)}
              </Text>
            </View>
          </View>
        </View>
      </ScrollView>
    </>
  );
};

export default PurchaseReceiveDetails;

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
  statusBadge: {
    backgroundColor: colors.green,
    paddingHorizontal: 8,
    color: colors.white,
    borderRadius: 2,
    paddingVertical: 3,
    textTransform: "capitalize",
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
  itemCalculation: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 5,
  },
  calculation: {
    padding: 20,
    borderBottomColor: "#adb5bd4d",
    borderBottomWidth: 1,
  },
});
