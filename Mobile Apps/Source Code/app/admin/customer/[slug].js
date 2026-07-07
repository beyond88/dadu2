import React, { useState } from "react";
import Topbar from "../../../components/Topbar/Topbar";
import { Pressable, ScrollView, StyleSheet, View } from "react-native";
import { useLocalSearchParams } from "expo-router";
import { useGetSingleCustomerQuery } from "../../../redux/features/customer/customerApi";
import { Image } from "expo-image";
import { colors } from "../../../themes/colors";
import Text from "../../../components/text/Text";
import { generate8DigitNumber } from "../../../utils/helper";
import { useSelector } from "react-redux";

const CustomerDetails = () => {
  const [tab, setTab] = useState("invoice_history");
  const { slug } = useLocalSearchParams();

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  //get customer
  const { data: customer } = useGetSingleCustomerQuery(slug);

  const {
    avatar_url,
    full_name,
    email,
    phone,
    company,
    designation,
    address_line_1,
    address_line_2,
    city,
    state,
    country,
    zipcode,
    b_first_name,
    b_last_name,
    b_email,
    b_country,
    b_state,
    b_city,
    b_address_line_1,
    b_address_line_2,
    b_short_address,
    b_zipcode,
    b_phone,
    status,
    invoices,
    products,
    not_paid_invoices,
  } = customer?.data || {};

  //handle tab

  const handleTab = (tab) => {
    setTab(tab);
  };
  return (
    <>
      <Topbar title="Customer Details" />
      <ScrollView style={{ paddingHorizontal: 20, marginBottom: 20 }}>
        <View style={styles.card}>
          <View style={styles.customerBasicInfoWrap}>
            <View style={styles.customerImgWrap}>
              <Image source={avatar_url} style={styles.userImg} />
            </View>
            <View styles={styles.customerBasicInfo}>
              <Text
                preset="h5_m"
                style={{ color: colors.black, marginBottom: 8 }}
              >
                Basic Info
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {full_name}
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {email}
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {phone}
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {company}
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {designation}
              </Text>
            </View>
          </View>
          <View style={styles.customerInfo}>
            <View style={styles.infoItemLeft}>
              <Text
                preset="h5_m"
                style={{ color: colors.black, marginBottom: 8 }}
              >
                Billing info
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {b_first_name} {b_last_name}
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {b_email}
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {b_phone}
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {b_city?.name},{b_state?.name}, {b_country?.name},{b_zipcode}
              </Text>

              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {b_address_line_1}, {b_address_line_2}
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {b_short_address}
              </Text>
              <Text
                style={[
                  styles.customerStatus,
                  status === "active"
                    ? { backgroundColor: colors.green }
                    : { backgroundColor: colors.red },
                ]}
              >
                {status}
              </Text>
            </View>
            <View style={styles.infoItemRight}>
              <Text
                preset="h5_m"
                style={{ color: colors.black, marginBottom: 8 }}
              >
                Address
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {address_line_1}, {address_line_2}
              </Text>
              <Text style={{ color: colors.black, marginBottom: 4 }}>
                {city?.name}, {state?.name},{country?.name},{zipcode}
              </Text>
            </View>
          </View>
          <View style={styles.tabWrap}>
            <Pressable
              style={styles.tabItem}
              onPress={() => handleTab("invoice_history")}
            >
              <Text
                preset="h5_m"
                style={
                  tab === "invoice_history" && { color: colors.themeColor }
                }
              >
                Invoice History
              </Text>
            </Pressable>
            <Pressable
              style={styles.tabItem}
              onPress={() => handleTab("product_history")}
            >
              <Text
                preset="h5_m"
                style={
                  tab === "product_history" && { color: colors.themeColor }
                }
              >
                Product History
              </Text>
            </Pressable>
            <Pressable
              style={styles.tabItem}
              onPress={() => handleTab("to_pay")}
            >
              <Text
                preset="h5_m"
                style={tab === "to_pay" && { color: colors.themeColor }}
              >
                To Pay
              </Text>
            </Pressable>
          </View>
          {tab === "invoice_history" && (
            <View>
              {invoices?.map((invoice) => (
                <View style={styles.itemWrap} key={invoice?.id}>
                  <View style={styles.item}>
                    <View style={styles.left}>
                      <Text
                        preset="h5_m"
                        style={{ marginBottom: 3, color: colors.black }}
                      >
                        {generate8DigitNumber(invoice?.id || 0)}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        Date: {invoice?.date}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        Payment type: {invoice?.payment_type}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        Total paid: {currency_symbol} {invoice?.total_paid}
                      </Text>
                      <Text
                        preset="h6_m"
                        style={{ color: colors.black }}
                      ></Text>
                    </View>
                    <View style={{ alignItems: "flex-end" }}>
                      <Text preset="h6_m" style={{ marginBottom: 4 }}>
                        {currency_symbol} {invoice?.total}
                      </Text>
                      <Text
                        style={[
                          styles.statusBtn,
                          invoice?.status === "pending"
                            ? { backgroundColor: colors.yellow }
                            : { backgroundColor: colors.green },
                        ]}
                      >
                        {invoice?.status}
                      </Text>
                    </View>
                  </View>
                </View>
              ))}
            </View>
          )}

          {tab === "product_history" && (
            <View>
              {products?.map((product, index) => (
                <View style={styles.itemWrap} key={index}>
                  <View style={styles.item}>
                    <View style={styles.left}>
                      <Text
                        preset="h5_m"
                        style={{ marginBottom: 3, color: colors.black }}
                      >
                        {generate8DigitNumber(product?.product_id || 0)}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        {product?.product_name}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        SKU: {product?.sku}
                      </Text>

                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        Quantity: {product?.quantity}
                      </Text>
                      <Text
                        preset="h6_m"
                        style={{ color: colors.black }}
                      ></Text>
                    </View>
                    <View style={{ alignItems: "flex-end" }}>
                      <Text preset="h6_m" style={{ marginBottom: 4 }}>
                        {currency_symbol} {product?.price}
                      </Text>
                    </View>
                  </View>
                </View>
              ))}
            </View>
          )}
          {tab === "to_pay" && (
            <View>
              {not_paid_invoices?.map((invoice) => (
                <View style={styles.itemWrap} key={invoice?.id}>
                  <View style={styles.item}>
                    <View style={styles.left}>
                      <Text
                        preset="h5_m"
                        style={{ marginBottom: 3, color: colors.black }}
                      >
                        {generate8DigitNumber(invoice?.id || 0)}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        Date: {invoice?.date}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        Payment type: {invoice?.payment_type}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        Total paid: {currency_symbol} {invoice?.total_paid}
                      </Text>
                      <Text
                        preset="h6_m"
                        style={{ color: colors.black }}
                      ></Text>
                    </View>
                    <View style={{ alignItems: "flex-end" }}>
                      <Text preset="h6_m" style={{ marginBottom: 4 }}>
                        {currency_symbol} {invoice?.total}
                      </Text>
                      <Text
                        style={[
                          styles.statusBtn,
                          invoice?.status === "pending"
                            ? { backgroundColor: colors.yellow }
                            : { backgroundColor: colors.green },
                        ]}
                      >
                        {invoice?.status}
                      </Text>
                    </View>
                  </View>
                </View>
              ))}
            </View>
          )}
        </View>
      </ScrollView>
    </>
  );
};

export default CustomerDetails;

const styles = StyleSheet.create({
  card: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    position: "relative",
    borderRadius: 5,
    padding: 20,
  },
  customerBasicInfoWrap: {
    flexDirection: "row",
    gap: 20,
  },
  customerImgWrap: {
    width: 100,
  },
  userImg: {
    width: 100,
    height: 100,
    borderRadius: 5,
  },
  customerBasicInfo: {
    flex: 1,
    paddingLeft: 20,
  },
  customerStatus: {
    paddingHorizontal: 12,
    color: colors.white,
    borderRadius: 2,
    paddingVertical: 4,
    textAlign: "center",

    textTransform: "capitalize",
  },
  customerInfo: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 20,
    gap: 10,
    flexWrap: "wrap",
  },
  infoItemLeft: {
    width: "48%",
  },
  infoItemRight: {
    width: "48%",
  },
  tabWrap: {
    flexDirection: "row",
    marginTop: 20,
    gap: 20,
    flexWrap: "wrap",
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
  },
  tabItem: {
    backgroundColor: colors.white,
    paddingVertical: 10,
  },
  itemWrap: {
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
    paddingBottom: 10,
  },
  item: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 10,
    paddingVertical: 12,
    paddingBottom: 0,
    flexWrap: "wrap",
  },
  left: {
    width: "55%",
  },
  statusBtn: {
    backgroundColor: colors.green,
    paddingHorizontal: 8,
    color: colors.white,
    borderRadius: 2,
    paddingVertical: 3,
    textTransform: "capitalize",
  },
});
