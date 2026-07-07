import React, { useEffect, useState } from "react";
import Topbar from "../../../components/Topbar/Topbar";
import {
  ActivityIndicator,
  Alert,
  Animated,
  FlatList,
  Pressable,
  StyleSheet,
  TouchableOpacity,
  View,
} from "react-native";
import {
  useDeleteCouponMutation,
  useGetCouponListQuery,
} from "../../../redux/features/coupon/couponApi";
import { colors } from "../../../themes/colors";
import TableLoader from "../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../components/CustomMessage/ErrorMessage";
import Text from "../../../components/text/Text";
import { Image } from "expo-image";
import { AntDesign, FontAwesome } from "@expo/vector-icons";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { LinearGradient } from "expo-linear-gradient";
import Loading from "../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";
import { Link, router } from "expo-router";

const Coupon = () => {
  const [page, setPage] = useState(1);
  const [couponList, setCouponList] = useState([]);

  //handle edit navigation
  const handleEditNavigation = (id) => {
    router.push(`/admin/coupon/edit/${id}`);
  };
  //handle navigation
  const handleProductNavigation = (id) => {
    router.push(`/admin/coupon/products/${id}`);
  };
  //handle delete
  const [
    deleteCoupon,
    {
      data: deleteData,
      isSuccess: deleteIsSuccess,
      isLoading: deleteIsLoading,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeleteCouponMutation();
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
            deleteCoupon(id);
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
      setCouponList([]);
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
      outputRange: [0.5, 0],
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
        <TouchableOpacity onPress={() => handleEditNavigation(item?.id)}>
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={styles.swipeBtn}
          >
            <AntDesign name="edit" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Edit
            </Text>
          </LinearGradient>
        </TouchableOpacity>
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
        <TouchableOpacity onPress={() => handleProductNavigation(item?.id)}>
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={styles.swipeBtn}
          >
            <FontAwesome name="product-hunt" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Products
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
              <View style={styles.pImage}>
                <Image source={item?.banner_url} style={styles.thumbImg} />
              </View>
              <View style={styles.pContent}>
                <Text
                  preset="h5_m"
                  style={{ marginBottom: 3, color: colors.black }}
                >
                  {item?.title}
                </Text>
                <Text
                  preset="h6"
                  style={{ color: colors.fontColor, marginBottom: 4 }}
                >
                  Coupon code- {item?.code}
                </Text>
                <Text
                  preset="h6"
                  style={{ color: colors.fontColor, marginBottom: 4 }}
                >
                  Discount- {item?.discount}
                </Text>
                <Text preset="h6_m" style={{ color: colors.black }}>
                  Minimum Qty-{item?.minimum_shopping}
                </Text>
              </View>
            </View>
            <View style={{ alignItems: "flex-end" }}>
              <Text
                preset="h6_m"
                style={{
                  marginBottom: 5,
                  color:
                    item?.status === "ACTIVE" ? colors.themeColor : colors.red,
                }}
              >
                {item?.validity}
              </Text>
              <Text
                style={[
                  styles.activeBtn,
                  {
                    backgroundColor:
                      item?.status === "ACTIVE"
                        ? colors.themeColor
                        : colors.red,
                  },
                ]}
              >
                {item?.status}
              </Text>
            </View>
          </View>
        </View>
      </Swipeable>
    </GestureHandlerRootView>
  );
  //get coupon list
  const {
    data: getCouponList,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetCouponListQuery(page);

  const { current_page, to, total } = getCouponList?.data?.meta || {};
  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getCouponList?.data?.data) &&
      page == current_page
    ) {
      setCouponList((prevData) => [...prevData, ...getCouponList?.data?.data]);
    }
  }, [isSuccess, getCouponList]);

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
  } else if (getCouponList?.length === 0) {
    content = <NotFoundMessage message="Coupon Not Found" />;
  } else if (couponList?.length > 0) {
    content = (
      <FlatList
        data={couponList}
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
      <Topbar title="Coupon" />
      <View style={{ marginHorizontal: 20, marginBottom: 180 }}>
        <View>
          <Pressable
            onPress={() => router.push(`/admin/coupon/create`)}
            style={{
              backgroundColor: colors.themeColor,
              alignItems: "center",
              padding: 10,
              borderRadius: 5,
            }}
          >
            <Text preset="h5" style={{ color: colors.white }}>
              Create Coupon{" "}
            </Text>
          </Pressable>
        </View>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default Coupon;

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
    paddingVertical: 12,
    paddingBottom: 0,
    flexWrap: "wrap",
  },
  left: {
    flexDirection: "row",
    alignItems: "center",
    width: "55%",
  },
  pImage: {
    width: 50,
  },
  thumbImg: {
    width: 50,
    height: 50,
    borderRadius: 5,
  },
  pContent: {
    marginLeft: 12,
    flex: 1,
  },
  activeBtn: {
    backgroundColor: colors.green,
    paddingHorizontal: 8,
    color: colors.white,
    borderRadius: 2,
    fontSize: 10,
    paddingVertical: 3,
    textTransform: "uppercase",
  },
  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 65,
    height: "100%",
  },
});
