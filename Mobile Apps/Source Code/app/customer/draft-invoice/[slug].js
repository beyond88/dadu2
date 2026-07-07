import { StyleSheet, View, Pressable, TextInput } from "react-native";
import Text from "../../../components/text/Text";
import { useLocalSearchParams } from "expo-router";
import Topbar from "../../../components/Topbar/Topbar";
import { Image } from "react-native";
import { ScrollView } from "react-native";
import { colors } from "../../../themes/colors";
import { useGetSingleDraftInvoiceQuery } from "../../../redux/features/pos-invoice/posInvoiceApi";
import { generate8DigitNumber } from "../../../utils/helper";
import { useSelector } from "react-redux";
import { useEffect } from "react";

const ViewInvoice = () => {
  const { slug } = useLocalSearchParams();
  //get invoice data
  const {
    data: invoiceData,
    isLoading,
    error,
    isError,
    refetch,
  } = useGetSingleDraftInvoiceQuery(slug);

  const {
    id,
    date,
    billing_info,
    shipping_info,
    items,
    discount_amount,
    tax_amount,
    total,
    total_paid,
    payments,
    notes,
    token,
  } = invoiceData?.data || {};

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  //refetch data
  useEffect(() => {
    refetch();
  }, [slug]);

  return (
    <View>
      <Topbar title="Draft Invoice View" customer={true} />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 20 }}>
        <View style={styles.invoiceCard}>
          <View style={styles.topTile}>
            <View style={{ width: 120 }}>
              <Image
                source={require("../../../assets/images/logo.png")}
                style={styles.logo}
              />
            </View>
            <View style={{ flex: 1 }}>
              <Text
                preset="h2_sb"
                style={{
                  textAlign: "right",
                  color: "#10A0B1",
                  marginBottom: 5,
                }}
              >
                Invoice#{generate8DigitNumber(id ? id : 0)}
              </Text>
              <Text
                preset="h5_m"
                style={{ textAlign: "right", color: "#727F8B" }}
              >
                Date : {date ? date : "N/A"}
              </Text>
            </View>
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
                {billing_info?.address_line_1}, {billing_info?.address_line_2}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B", marginBottom: 4 }}>
                {billing_info?.email}
              </Text>
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {billing_info?.phone}
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
                preset="h3"
                style={{
                  color: "#142A3E",
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                {shipping_info?.name}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: "#727F8B",
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                {shipping_info?.address_line_1}, {shipping_info?.address_line_2}
              </Text>
              <Text
                preset="h6"
                style={{
                  color: "#727F8B",
                  marginBottom: 4,
                  textAlign: "right",
                }}
              >
                {shipping_info?.email}
              </Text>
              <Text
                preset="h6"
                style={{ color: "#727F8B", textAlign: "right" }}
              >
                {shipping_info?.phone}
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
              {items?.map((item, index) => (
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
                    {}
                    {item?.price}
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
                    {currency_symbol}
                    {item?.sub_total}
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
                Discount
              </Text>
              <Text
                preset="h6_m"
                style={{ color: colors.black, marginBottom: 8 }}
              >
                {currency_symbol}
                {discount_amount}
              </Text>
            </View>
            <View style={styles.itemCalculation}>
              <Text
                preset="h5"
                style={{ color: colors.pcolor, marginBottom: 8 }}
              >
                TAX
              </Text>
              <Text
                preset="h6_m"
                style={{ color: colors.black, marginBottom: 8 }}
              >
                {currency_symbol}
                {tax_amount}
              </Text>
            </View>
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
                {currency_symbol}
                {total}
              </Text>
            </View>
            <View style={styles.itemCalculation}>
              <Text
                preset="h5"
                style={{ color: colors.pcolor, marginBottom: 8 }}
              >
                Total Paid
              </Text>
              <Text
                preset="h6_m"
                style={{ color: colors.black, marginBottom: 8 }}
              >
                {currency_symbol}
                {total_paid}
              </Text>
            </View>
            <View style={styles.itemCalculation}>
              <Text
                preset="h5"
                style={{ color: colors.pcolor, marginBottom: 8 }}
              >
                Total Due
              </Text>
              <Text
                preset="h6_m"
                style={{ color: colors.black, marginBottom: 8 }}
              >
                {currency_symbol}
                {Number(total) - Number(total_paid)}
              </Text>
            </View>
          </View>

          <View style={{ paddingHorizontal: 20, marginBottom: 10 }}>
            <Text preset="h3" style={{ marginBottom: 8 }}>
              Notes
            </Text>
            <Text>{notes}</Text>
          </View>
        </View>
      </ScrollView>
    </View>
  );
};

export default ViewInvoice;

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
  logo: {
    width: 120,
    height: 50,
    objectFit: "contain",
  },
  invoiceCard: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    position: "relative",
  },
  invoiceShape: {
    position: "absolute",
    bottom: -20,
    left: 0,
    width: "100%",
  },
  shapeImg: {
    minWidth: "100%",
    width: "100%",
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
  tableBody: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingVertical: 10,
  },
  paymentTable: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingVertical: 10,
    paddingHorizontal: 20,
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
  totalGrand: {
    paddingVertical: 20,
    paddingBottom: 30,
  },
  totalGrandItem: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 10,
  },
  authButton: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    paddingHorizontal: 32,
    borderRadius: 5,
    elevation: 3,
    height: 48,
  },
  buttonText: {
    color: colors.white,
  },
  paymentInput: {
    height: 48,
    borderColor: colors.border,
    borderWidth: 1,
    borderRadius: 5,
    paddingHorizontal: 20,
    marginBottom: 20,
    flex: 1,
    borderTopEndRadius: 0,
    borderBottomEndRadius: 0,
  },
  copyBtn: {
    width: 150,
    borderTopStartRadius: 0,
    borderBottomStartRadius: 0,
  },
});
