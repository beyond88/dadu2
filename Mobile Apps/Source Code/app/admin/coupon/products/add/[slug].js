import React, { useEffect, useState } from "react";
import Topbar from "../../../../../components/Topbar/Topbar";
import { View, ScrollView, StyleSheet, Pressable } from "react-native";
import Text from "../../../../../components/text/Text";
import {
  useAddedCouponProductMutation,
  useGetCouponProductsQuery,
} from "../../../../../redux/features/coupon/couponApi";
import { useLocalSearchParams } from "expo-router";
import FormSelect from "../../../../../components/Form/FormSelect";
import { colors } from "../../../../../themes/colors";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../../components/Loading/Loading";

const CouponProductAdd = () => {
  const [selectProductId, setSelectProductId] = useState(null);
  const { slug } = useLocalSearchParams();
  const { data: getCouponProductList } = useGetCouponProductsQuery({ slug });
  const productList = [];
  if (getCouponProductList?.data?.products) {
    getCouponProductList?.data?.products?.map((item) => {
      productList.push({
        label: item.name,
        value: item.id,
      });
    });
  }
  //product onchange

  const productOnchange = (value) => {
    setSelectProductId(value);
  };

  //handle product add

  const [
    addedCouponProduct,
    { data: addedData, isLoading, isError, isSuccess, error },
  ] = useAddedCouponProductMutation();

  const handleProductAdd = () => {
    addedCouponProduct({ coupon_id: slug, product_id: selectProductId });
  };

  useEffect(() => {
    if (isSuccess) {
      showMessage({
        message: addedData?.message,
        type: "success",
      });
      setSelectProductId(null);
    }
    if (isError) {
      showMessage({
        message: error?.data?.message,
        type: "danger",
      });
    }
  }, [addedData, isSuccess, isError, error]);

  return (
    <>
      {isLoading && <Loading />}
      <Topbar title="Add Coupon Product" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <Text style={styles.label} preset="h2_sb">
              Product<Text style={{ color: "#ff0000" }}> *</Text>
            </Text>
            <FormSelect
              items={productList}
              placeholder="Select product"
              onChange={productOnchange}
              selectedValue={selectProductId}
              searchable={true}
              position="Bottom"
            />
            <View
              style={{
                flexDirection: "row",
                gap: 10,
                justifyContent: "flex-end",
              }}
            >
              <Pressable style={styles.formBtn} onPress={handleProductAdd}>
                <Text preset="h3" style={styles.btnText}>
                  Submit
                </Text>
              </Pressable>
            </View>
          </View>
        </ScrollView>
      </View>
    </>
  );
};

export default CouponProductAdd;

const styles = StyleSheet.create({
  formWrap: {
    backgroundColor: "#fff",
    padding: 20,
    borderRadius: 5,
    minHeight: 300,
  },
  label: {
    color: colors.black,
    marginBottom: 10,
  },
  formBtn: {
    backgroundColor: colors.themeColor,
    borderRadius: 5,
    paddingVertical: 10,
    paddingHorizontal: 20,
  },
  btnText: {
    color: colors.white,
  },
});
