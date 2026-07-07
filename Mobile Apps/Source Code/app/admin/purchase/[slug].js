import React from "react";
import Topbar from "../../../components/Topbar/Topbar";
import { ScrollView, StyleSheet, View } from "react-native";
import { useLocalSearchParams } from "expo-router";
import { usePurchaseDetailsQuery } from "../../../redux/features/purchase/purchaseApi";
import Text from "../../../components/text/Text";
import { colors } from "../../../themes/colors";
import { useSelector } from "react-redux";

const PurchaseView = () => {
  //get purchase id from params
  const { slug } = useLocalSearchParams();

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  //get purchase details
  const { data: purchaseDetails } = usePurchaseDetailsQuery(slug);
  const {
    purchase_number,
    supplier,
    warehouse,
    company,
    date,
    notes,
    short_address,
    address,
    status,
    is_received,
    purchase_items,
    total,
  } = purchaseDetails?.data || {};
  return (
    <>
      <Topbar title="View Purchase" />
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
                Note: {notes}
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
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                City: {address?.city}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: colors.black,
                  marginBottom: 8,
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
              {purchase_items?.map((item, index) => (
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
                {currency_symbol} {total}
              </Text>
            </View>
          </View>
        </View>
      </ScrollView>
    </>
  );
};

export default PurchaseView;

const styles = StyleSheet.create({
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
