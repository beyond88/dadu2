import React from "react";
import {
  Dimensions,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  View,
} from "react-native";
import Topbar from "../../../components/Topbar/Topbar";
import { useGetAdminSingleWarehouseQuery } from "../../../redux/features/warehouse/warehouseApi";
import { useLocalSearchParams } from "expo-router";
import Text from "../../../components/text/Text";
import { Image } from "expo-image";
import { useSelector } from "react-redux";
import { colors } from "../../../themes/colors";
import * as FileSystem from "expo-file-system";

const WarehouseDetails = () => {
  //get slug from router
  const { slug } = useLocalSearchParams();
  const apiUrl = process.env.EXPO_PUBLIC_API_URL;
  const windowHeight = Dimensions.get("window").height - 150;
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  //get warehouse details
  const { data: warehouseDetailsData, isSuccess } =
    useGetAdminSingleWarehouseQuery(slug);
  const { name, email, phone, company_name, address_1, product_stocks } =
    warehouseDetailsData?.data || {};

  //total quantity
  const totalQuantity = product_stocks?.reduce(
    (a, b) => Number(a) + Number(b?.quantity),
    0
  );
  //total amount calculation
  const totalAmount = product_stocks?.reduce(
    (a, b) => Number(a) + Number(b?.quantity) * Number(b?.product?.price),
    0
  );
  //Download pdf
  //Download pdf

  const handleDownloadPDF = async () => {
    const filename = "warehouse";
    const result = await FileSystem.downloadAsync(
      `${apiUrl}/admin/warehouses/${slug}/download`,
      FileSystem.documentDirectory + filename
    );
    save(result.uri, filename, result.headers["Content-Type"]);
  };

  const save = async (uri, filename, mimetype) => {
    if (Platform.OS === "android") {
      const permissions =
        await FileSystem.StorageAccessFramework.requestDirectoryPermissionsAsync();
      if (permissions.granted) {
        const base64 = await FileSystem.readAsStringAsync(uri, {
          encoding: FileSystem.EncodingType.Base64,
        });
        await FileSystem.StorageAccessFramework.createFileAsync(
          permissions.directoryUri,
          filename,
          mimetype
        )
          .then(async (uri) => {
            await FileSystem.writeAsStringAsync(uri, base64, {
              encoding: FileSystem.EncodingType.Base64,
            });
          })
          .catch((e) => console.log(e));
      } else {
        shareAsync(uri);
      }
    } else {
      shareAsync(uri);
    }
  };
  return (
    <>
      <Topbar title="Warehouse Details" />
      <View style={{ marginHorizontal: 20, height: windowHeight }}>
        <ScrollView>
          <View style={styles.infoCard}>
            <Pressable style={styles.downloadBtn} onPress={handleDownloadPDF}>
              <Text
                preset="h3"
                style={{ color: colors.white, textAlign: "center" }}
              >
                Download
              </Text>
            </Pressable>
            <Text preset="h3" style={{ marginBottom: 8 }}>
              {name}
            </Text>
            <Text style={{ marginBottom: 4 }}>Email:{email}</Text>
            <Text style={{ marginBottom: 4 }}>Phone:{phone}</Text>
            <Text style={{ marginBottom: 4 }}>Company:{company_name}</Text>
            <Text>Address:{address_1}</Text>
          </View>
          <View>
            <Text preset="h2">Product Stock</Text>
          </View>
          {product_stocks?.map((item) => (
            <View style={styles.tableCardWrap} key={item?.id}>
              <View style={styles.tableCardItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Product
                </Text>
                <View style={styles.itemRight}>
                  <View
                    style={{
                      flexDirection: "row",
                      alignItems: "center",
                      gap: 10,
                      justifyContent: "flex-end",
                    }}
                  >
                    <View>
                      <Image
                        source={item?.product?.thumb_url}
                        style={styles.thumbImg}
                      />
                    </View>
                    <Text>
                      {item?.product?.name}{" "}
                      {item?.attribute &&
                        `(${item?.attribute.name} : ${item?.attribute_item?.name})`}
                    </Text>
                  </View>
                </View>
              </View>
              <View style={styles.tableCardItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  SKU
                </Text>
                <Text style={styles.itemRight}>{item?.product?.sku}</Text>
              </View>
              <View style={styles.tableCardItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Category
                </Text>
                <Text style={styles.itemRight}>
                  {item?.product?.category?.name}
                </Text>
              </View>
              <View style={styles.tableCardItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Manufacturer
                </Text>
                <Text style={styles.itemRight}>
                  {item?.product?.manufacturer?.name}
                </Text>
              </View>
              <View style={styles.tableCardItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Quantity
                </Text>
                <Text style={styles.itemRight}>
                  {item?.quantity} {item?.product?.weight_unit?.name}
                </Text>
              </View>
              <View style={styles.tableCardItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Price
                </Text>
                <Text style={styles.itemRight}>
                  {currency_symbol} {item?.product?.price}
                </Text>
              </View>
              <View style={styles.tableCardItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Total
                </Text>
                <Text style={styles.itemRight}>
                  {currency_symbol}{" "}
                  {Number(item?.quantity) * Number(item?.product?.price)}
                </Text>
              </View>
            </View>
          ))}
          <View style={styles.tableCardWrap}>
            <View style={styles.tableCardItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Total Quantity
              </Text>
              <Text style={styles.itemRight}>{totalQuantity}</Text>
            </View>
            <View style={styles.tableCardItem}>
              <Text preset="h5" style={styles.itemLeft}>
                Total Amount
              </Text>
              <Text style={styles.itemRight}>{totalAmount}</Text>
            </View>
          </View>
        </ScrollView>
      </View>
    </>
  );
};

export default WarehouseDetails;

const styles = StyleSheet.create({
  infoCard: {
    backgroundColor: "#fff",
    padding: 20,
    borderRadius: 5,
    marginBottom: 20,
  },
  downloadBtn: {
    backgroundColor: colors.themeColor,
    paddingVertical: 10,
    paddingHorizontal: 20,
    borderRadius: 5,
    marginBottom: 10,
    textAlign: "center",
    flex: 1,
  },
  tableCardWrap: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    marginTop: 20,
  },
  tableCardItem: {
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
  thumbImg: {
    width: 40,
    height: 40,
    borderRadius: 5,
  },
});
