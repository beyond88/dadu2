import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  View,
  StyleSheet,
  ActivityIndicator,
  TouchableOpacity,
  FlatList,
  Animated,
  Pressable,
  Alert,
} from "react-native";
import { showMessage } from "react-native-flash-message";
import { LinearGradient } from "expo-linear-gradient";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import {
  useDeleteCouponProductMutation,
  useGetCouponProductsQuery,
} from "../../../../redux/features/coupon/couponApi";
import { colors } from "../../../../themes/colors";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";
import Loading from "../../../../components/Loading/Loading";
import { AntDesign } from "@expo/vector-icons";
import Text from "../../../../components/text/Text";
import { Link, router, useLocalSearchParams } from "expo-router";

const CouponProduct = () => {
  const [page, setPage] = useState(1);
  const [couponProductList, setCouponProductList] = useState([]);

  const { slug } = useLocalSearchParams();
  //handle delete
  const [
    deleteCouponProduct,
    {
      data: deleteData,
      isSuccess: deleteIsSuccess,
      isLoading: deleteIsLoading,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeleteCouponProductMutation();
  const handleDelete = (id) => {
    Alert.alert(
      "Confirm Deletion",
      "Are you sure you want to delete this item?",
      [
        {
          text: "No",
          style: "cancel",
        },
        {
          text: "Yes",
          onPress: () => {
            deleteCouponProduct(id);
          },
        },
      ],
      { cancelable: false }
    );
  };
  useEffect(() => {
    if (deleteIsSuccess) {
      showMessage({
        message: deleteData.message,
        type: "success",
      });
      setPage(1);
      setCouponProductList([]);
    }

    if (deleteIsError) {
      showMessage({
        message: deleteError?.data?.message,
        type: "danger",
      });
    }
  }, [deleteData, deleteIsSuccess, deleteError, deleteIsError]);

  //Swipeable right swipe

  const rightSwipe = (progress, dragX, item) => {
    const scale = dragX.interpolate({
      inputRange: [-100, 0],
      outputRange: [1.5, 0],
    });

    // Interpolate the translation based on dragX
    const translateX = dragX.interpolate({
      inputRange: [0, 100],
      outputRange: [0, 100],
      extrapolate: "clamp",
    });

    return (
      <Animated.View
        style={{
          flexDirection: "row",
          transform: [{ translateX }, { scale }],
        }}
      >
        <TouchableOpacity onPress={() => handleDelete(item?.id)}>
          <LinearGradient
            colors={["#EC4561", "#EC4561"]}
            style={styles.swipeBtn}
          >
            <AntDesign name="delete" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Delete
            </Text>
          </LinearGradient>
        </TouchableOpacity>
      </Animated.View>
    );
  };
  // render item
  const renderItem = ({ item }) => (
    <GestureHandlerRootView>
      <Swipeable
        renderRightActions={(progress, dragX) =>
          rightSwipe(progress, dragX, item)
        }
        key={item?.id}
      >
        <View style={styles.itemWrap}>
          <View style={styles.item}>
            <View style={styles.left}>
              <View style={styles.pContent}>
                <Text
                  preset="h5_m"
                  style={{ marginBottom: 3, color: colors.black }}
                >
                  {item?.coupon_product_name}
                </Text>
              </View>
            </View>
          </View>
        </View>
      </Swipeable>
    </GestureHandlerRootView>
  );

  //get coupon list
  const {
    data: getCouponProductList,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetCouponProductsQuery({ slug, page });

  const { current_page, to, total } =
    getCouponProductList?.data?.coupon_products.meta || {};

  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getCouponProductList?.data?.coupon_products?.data) &&
      page == current_page
    ) {
      setCouponProductList((prevData) => [
        ...prevData,
        ...getCouponProductList?.data?.coupon_products?.data,
      ]);
    }
  }, [isSuccess, getCouponProductList]);

  const fetchMoreData = () => {
    // Increment the page number to fetch the next set of data
    if (to != total) {
      setPage((prevPage) => prevPage + 1);
    }
  };
  //render loader
  const renderLoader = () => {
    return (
      <View style={{ paddingVertical: 10 }}>
        {to == total ? (
          <Text
            preset="h4"
            style={{
              textAlign: "center",
              justifyContent: "center",
              marginVertical: 10,
            }}
          >
            No More Data
          </Text>
        ) : (
          <ActivityIndicator size="large" color={colors.themeColor} />
        )}
      </View>
    );
  };

  //render content
  let content = null;
  if (isLoading) {
    content = <TableLoader />;
  } else if (isError) {
    content = <ErrorMessage message={error?.data?.message} />;
  } else if (couponProductList?.length === 0) {
    content = <NotFoundMessage message="Coupon Products Not Found" />;
  } else if (couponProductList?.length > 0) {
    content = (
      <FlatList
        data={couponProductList}
        renderItem={renderItem}
        keyExtractor={(item) => item.id}
        ListFooterComponent={renderLoader}
        onEndReached={fetchMoreData}
        onEndReachedThreshold={0}
      />
    );
  }

  return (
    <>
      {deleteIsLoading && <Loading />}
      <Topbar title="Coupon products List" />
      <View style={{ marginHorizontal: 20, marginBottom: 180 }}>
        <View>
          <Pressable
            onPress={() => router.push(`/admin/coupon/products/add/${slug}`)}
            style={{
              backgroundColor: colors.themeColor,
              alignItems: "center",
              padding: 10,
              borderRadius: 5,
            }}
          >
            <Text preset="h5" style={{ color: colors.white }}>
              Coupon Product Add
            </Text>
          </Pressable>
        </View>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default CouponProduct;

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.white,
    borderRadius: 5,
    height: "100%",
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
    paddingVertical: 20,
    paddingBottom: 0,
    flexWrap: "wrap",
  },
  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 65,
    height: "100%",
  },
});
