import React from "react";
import { ScrollView, View, StyleSheet } from "react-native";
import Topbar from "../../../../components/Topbar/Topbar";
import { useGetReturnRequestDetailsQuery } from "../../../../redux/features/invoice-return/invoiceReturnApi";
import { useLocalSearchParams } from "expo-router";
import Text from "../../../../components/text/Text";

const ReturnRequestDetails = () => {
  const { slug } = useLocalSearchParams();
  //get return request details
  const { data: returnRequestDetailData, isSuccess: returnDetailsIsSuccess } =
    useGetReturnRequestDetailsQuery(slug);
  var { sales, warehouse } = returnRequestDetailData?.data || {};

  return (
    <>
      <Topbar title="Return Request Details" customer={true} />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 20 }}>
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
        </View>
        <View style={styles.returnCreateWrap}>
          <View style={styles.returnCreateItem}>
            <Text preset="h5" style={styles.itemLeft}>
              SKU
            </Text>
            <Text style={styles.itemRight}>2222</Text>
          </View>
          <View style={styles.returnCreateItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Product Name
            </Text>
            <Text style={styles.itemRight}>aaaaa</Text>
          </View>
          <View style={styles.returnCreateItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Quantity
            </Text>
            <Text style={styles.itemRight}>12</Text>
          </View>
          <View style={styles.returnCreateItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Price
            </Text>
            <Text style={styles.itemRight}>12000</Text>
          </View>
          <View style={styles.returnCreateItem}>
            <Text preset="h5" style={styles.itemLeft}>
              Sub Total
            </Text>
            <Text style={styles.itemRight}>12000</Text>
          </View>
        </View>
      </ScrollView>
    </>
  );
};

export default ReturnRequestDetails;

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
});
