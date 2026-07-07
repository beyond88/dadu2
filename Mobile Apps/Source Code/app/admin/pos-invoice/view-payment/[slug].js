import { useLocalSearchParams } from "expo-router";
import React from "react";
import { StyleSheet, View, ScrollView, Pressable } from "react-native";
import Topbar from "../../../../components/Topbar/Topbar";
import Text from "../../../../components/text/Text";
import { useSelector } from "react-redux";
import { useAdminInvoiceViewPaymentQuery } from "../../../../redux/features/pos-invoice/posInvoiceApi";
import { colors } from "../../../../themes/colors";

const ViewPayment = () => {
  const { slug } = useLocalSearchParams();

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  //get payment data

  const { data: paymentData } = useAdminInvoiceViewPaymentQuery(slug);
  return (
    <>
      <Topbar title="Payment History" />
      <ScrollView style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <View style={styles.card}>
          {paymentData?.data?.map((item) => (
            <View style={styles.item} key={item?.id}>
              <View style={styles.left}>
                <Text
                  preset="h5_m"
                  style={{ marginBottom: 3, color: colors.black }}
                >
                  Date: {item?.date}
                </Text>
                <Text preset="h6" style={{ color: colors.fontColor }}>
                  <Text preset="h6_m" style={{ color: colors.themeColor }}>
                    Notes:
                  </Text>{" "}
                  {item?.notes}
                </Text>
              </View>
              <View style={styles.right}>
                <Text
                  preset="h5"
                  style={{ marginBottom: 5, color: colors.themeColor }}
                >
                  {currency_symbol} {item?.amount}
                </Text>

                <Text preset="h5">{item?.payment_type}</Text>
              </View>
            </View>
          ))}
        </View>
      </ScrollView>
    </>
  );
};

export default ViewPayment;

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.white,
    borderRadius: 5,
    height: "100%",
  },
  item: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 10,
    paddingVertical: 12,
    flexWrap: "wrap",
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
  },
  left: {
    width: "70%",
  },
  right: {
    width: "30%",
  },
});
