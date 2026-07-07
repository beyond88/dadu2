import React, { useEffect, useState } from "react";
import { Pressable, ScrollView, TextInput, View } from "react-native";
import { StyleSheet } from "react-native";
import { Link, useLocalSearchParams, useRouter } from "expo-router";
import Topbar from "../../../../components/Topbar/Topbar";
import { colors } from "../../../../themes/colors";
import Text from "../../../../components/text/Text";
import {
  useAdminProductStockShowQuery,
  useAdminProductStockUpdateMutation,
} from "../../../../redux/features/product/productApi";
import Loading from "../../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";

const StockUpdate = () => {
  //component state
  const [quantity, setQuantity] = useState([]);
  //get slug
  const { slug } = useLocalSearchParams();
  const router = useRouter();
  //get stock data
  const { data: stockData } = useAdminProductStockShowQuery(slug);

  const { stock_info } = stockData?.data || {};

  //update stock mutation

  const [
    adminProductStockUpdate,
    { data: stockUpdateData, isLoading, isSuccess, isError, error },
  ] = useAdminProductStockUpdateMutation();

  //handle quantity change
  const handleQuantityChange = (value, index) => {
    const newQuantity = [...quantity];
    newQuantity[index] = value;
    setQuantity(newQuantity);
  };

  //handle update stock
  const handleStockUpdate = () => {
    const data = {
      id: slug,
      stock_id: stock_info?.map((item) => item?.stock_id),
      quantity,
    };
    adminProductStockUpdate(data);
  };

  //set stock quantity
  useEffect(() => {
    const newQuantity = [];
    stock_info?.map((item) => {
      newQuantity.push(item.quantity);
    });
    setQuantity(newQuantity);
  }, [stockData]);
  //show success & error message
  useEffect(() => {
    if (isSuccess) {
      showMessage({
        message: stockUpdateData.message,
        type: "success",
      });
      router.push("/admin/product");
    }
    if (isError) {
      showMessage({
        message: error.data.message,
        type: "danger",
      });
    }
  }, [stockUpdateData, isSuccess, isError, error]);
  return (
    <>
      {isLoading && <Loading />}
      <Topbar title="Stock Update" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          {stock_info?.map((item, index) => (
            <View style={styles.tableCardWrap} key={index}>
              <View style={styles.tableCardItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Warehouse
                </Text>
                <Text style={styles.itemRight}>{item?.warehouse}</Text>
              </View>
              <View style={styles.tableCardItem}>
                <Text preset="h5" style={styles.itemLeft}>
                  Quantity
                </Text>
                <View style={styles.itemRight}>
                  <TextInput
                    style={styles.textInput}
                    keyboardType="numeric"
                    value={quantity[index] || ""}
                    onChangeText={(value) => handleQuantityChange(value, index)}
                  />
                </View>
              </View>
            </View>
          ))}

          <View style={styles.formActionBtn}>
            <Pressable style={styles.formBtn} onPress={handleStockUpdate}>
              <Text preset="h3" style={styles.btnText}>
                Submit
              </Text>
            </Pressable>
            <Link href="/admin/product">
              <View style={[styles.formBtn, styles.cancelBtn]}>
                <Text preset="h3" style={styles.btnText}>
                  Cancel
                </Text>
              </View>
            </Link>
          </View>
        </ScrollView>
      </View>
    </>
  );
};

export default StockUpdate;

const styles = StyleSheet.create({
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
  textInput: {
    backgroundColor: colors.grayBg,
    height: 42,
    paddingHorizontal: 15,
    borderRadius: 5,
  },
  formActionBtn: {
    flexDirection: "row",
    justifyContent: "flex-end",
    gap: 10,
    marginTop: 20,
  },
  formBtn: {
    backgroundColor: colors.themeColor,
    borderRadius: 5,
    paddingVertical: 10,
    paddingHorizontal: 25,
  },
  btnText: {
    color: colors.white,
  },
  cancelBtn: {
    backgroundColor: colors.red,
  },
});
