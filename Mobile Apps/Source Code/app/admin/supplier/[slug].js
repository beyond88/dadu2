import React, { useState } from "react";
import Topbar from "../../../components/Topbar/Topbar";
import { useLocalSearchParams } from "expo-router";
import { Pressable, ScrollView, StyleSheet, View } from "react-native";
import { useSelector } from "react-redux";
import { useGetSingleSupplierQuery } from "../../../redux/features/supplier/supplierApi";
import { colors } from "../../../themes/colors";
import Text from "../../../components/text/Text";
import { Image } from "expo-image";
import { generate8DigitNumber } from "../../../utils/helper";

const ViewSupplier = () => {
  const [tab, setTab] = useState("purchase_history");
  const { slug } = useLocalSearchParams();

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  const { data: supplier } = useGetSingleSupplierQuery(slug);
  console.log(supplier);
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
    short_address,
    status,
    purchases,
    products,
    not_paid_invoices,
  } = supplier?.data || {};

  //handle tab

  const handleTab = (tab) => {
    setTab(tab);
  };
  return (
    <>
      <Topbar title="Supplier Details" />
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
              <Text style={{ color: colors.black, marginBottom: 8 }}>
                {city?.name}, {state?.name},{country?.name},{zipcode}
              </Text>
              <Text style={{ color: colors.black, marginBottom: 8 }}>
                Short Address: {short_address}
              </Text>
            </View>
          </View>
          <View style={styles.tabWrap}>
            <Pressable
              style={styles.tabItem}
              onPress={() => handleTab("purchase_history")}
            >
              <Text
                preset="h5_m"
                style={
                  tab === "purchase_history" && { color: colors.themeColor }
                }
              >
                Purchase History
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
          </View>
          {tab === "purchase_history" && (
            <View>
              {purchases?.map((purchase) => (
                <View style={styles.itemWrap} key={purchase?.id}>
                  <View style={styles.item}>
                    <View style={styles.left}>
                      <Text
                        preset="h5_m"
                        style={{ marginBottom: 3, color: colors.black }}
                      >
                        {purchase?.purchase_number}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        Date: {purchase?.date}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        Total product: {purchase?.total_product}
                      </Text>
                      <Text
                        preset="h6"
                        style={{ color: colors.fontColor, marginBottom: 4 }}
                      >
                        Missing item:{" "}
                        {purchase?.is_missing && (
                          <Text
                            style={[
                              styles.statusBtn,
                              { backgroundColor: colors.red },
                            ]}
                          >
                            Missing
                          </Text>
                        )}
                      </Text>
                    </View>
                    <View style={{ alignItems: "flex-end" }}>
                      <Text preset="h6_m" style={{ marginBottom: 4 }}>
                        {currency_symbol} {purchase?.total}
                      </Text>
                      <Text
                        style={[
                          styles.statusBtn,
                          purchase?.status === "REQUESTED" && {
                            backgroundColor: colors.yellow,
                          },
                          purchase?.status === "CONFIRMED" && {
                            backgroundColor: colors.green,
                          },
                          purchase?.status === "CANCEL" && {
                            backgroundColor: colors.red,
                          },
                        ]}
                      >
                        {purchase?.status}
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
        </View>
      </ScrollView>
    </>
  );
};

export default ViewSupplier;

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
    width: "100%",
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
