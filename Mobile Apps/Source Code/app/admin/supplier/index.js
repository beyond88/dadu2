import React, { useEffect, useState } from "react";
import Topbar from "../../../components/Topbar/Topbar";
import {
  ActivityIndicator,
  Animated,
  FlatList,
  Pressable,
  StyleSheet,
  TouchableOpacity,
  View,
} from "react-native";
import Text from "../../../components/text/Text";
import { Link, router } from "expo-router";
import { colors } from "../../../themes/colors";
import {
  useDeleteSupplierMutation,
  useGetSuppliersQuery,
} from "../../../redux/features/supplier/supplierApi";
import NotFoundMessage from "../../../components/CustomMessage/NotFoundMessage";
import ErrorMessage from "../../../components/CustomMessage/ErrorMessage";
import TableLoader from "../../../components/TableLoader/TableLoader";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { LinearGradient } from "expo-linear-gradient";
import { AntDesign } from "@expo/vector-icons";
import { Image } from "expo-image";
import { showMessage } from "react-native-flash-message";
import { Alert } from "react-native";
import Loading from "../../../components/Loading/Loading";

const Supplier = () => {
  const [page, setPage] = useState(1);
  const [supplierList, setSupplierList] = useState([]);
  //handle show navigation
  const handleShowNavigation = (id) => {
    router.push(`/admin/supplier/${id}`);
  };
  //handle edit navigation
  const handleEditNavigation = (id) => {
    router.push(`/admin/supplier/edit/${id}`);
  };
  //handle delete
  const [
    deleteSupplier,
    {
      data: deleteData,
      isLoading: deleteIsLoading,
      isSuccess: deleteIsSuccess,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeleteSupplierMutation();
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
            deleteSupplier(id);
          },
        },
      ],
      { cancelable: false }
    );
  };

  useEffect(() => {
    if (deleteIsSuccess) {
      setSupplierList([]);
      setPage(1);
      showMessage({
        message: deleteData.message,
        type: "success",
      });
    }
    if (deleteIsError) {
      showMessage({
        message: deleteError.data.message,
        type: "danger",
      });
    }
  }, [deleteData, deleteIsSuccess, deleteIsError, deleteError]);

  //Swipeable right swipe

  const rightSwipe = (progress, dragX, item) => {
    const scale = dragX.interpolate({
      inputRange: [-100, 0],
      outputRange: [0.6, 0],
    });

    // Interpolate the translation based on dragX
    const translateX = dragX.interpolate({
      inputRange: [0, 100],
      outputRange: [0, 100],
      extrapolate: "clamp",
    });

    return (
      <Animated.View
        style={{ flexDirection: "row", transform: [{ translateX }, { scale }] }}
      >
        <TouchableOpacity onPress={() => handleShowNavigation(item?.id)}>
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={styles.swipeBtn}
          >
            <AntDesign name="eyeo" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Show
            </Text>
          </LinearGradient>
        </TouchableOpacity>

        <TouchableOpacity onPress={() => handleEditNavigation(item?.id)}>
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={[styles.swipeBtn, !item?.is_received && styles.disabled]}
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
                <Image source={item?.avatar_url} style={styles.thumbImg} />
              </View>
              <View style={styles.pContent}>
                <Text
                  preset="h5_m"
                  style={{ marginBottom: 3, color: colors.black }}
                >
                  {item?.fist_name} {item?.last_name}
                </Text>
                <Text preset="h6" style={{ color: colors.fontColor }}>
                  {item?.email}
                </Text>
                <Text preset="h6_m" style={{ color: colors.black }}>
                  {item?.phone}
                </Text>
              </View>
            </View>
            <View style={{ alignItems: "flex-end" }}>
              <Text
                preset="h5"
                style={{ marginBottom: 5, color: colors.themeColor }}
              >
                {item?.price}
              </Text>
              <Text
                style={[
                  styles.activeBtn,
                  item?.status === "active"
                    ? { backgroundColor: colors.themeColor }
                    : { backgroundColor: colors.red },
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

  //get product list
  const {
    data: getSupplierList,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetSuppliersQuery(page);

  const { current_page, to, total } = getSupplierList?.data?.meta || {};

  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getSupplierList?.data?.data) &&
      page == current_page
    ) {
      setSupplierList((prevData) => [
        ...prevData,
        ...getSupplierList?.data?.data,
      ]);
    }
  }, [isSuccess, getSupplierList]);

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
  } else if (getSupplierList?.length === 0) {
    content = <NotFoundMessage message="Suppliers Not Found" />;
  } else if (supplierList?.length > 0) {
    content = (
      <FlatList
        data={supplierList}
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
      <Topbar title="Supplier List" />
      <View style={{ marginHorizontal: 20, marginBottom: 180 }}>
        <View>
          <Pressable
            onPress={() => router.push(`/admin/supplier/create`)}
            style={{
              backgroundColor: colors.themeColor,
              alignItems: "center",
              padding: 10,
              borderRadius: 5,
            }}
          >
            <Text preset="h5" style={{ color: colors.white }}>
              Create Supplier
            </Text>
          </Pressable>
        </View>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default Supplier;

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
    width: "75%",
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
  tableActionBtn: {
    flexDirection: "row",
    paddingHorizontal: 10,
    gap: 5,
    justifyContent: "space-between",
  },
  actionBtn: {
    backgroundColor: colors.themeColor,
    paddingHorizontal: 6,
    paddingVertical: 5,
    borderRadius: 2,
  },
  btnText: {
    color: colors.white,
    fontSize: 10,
  },
  createBtn: {
    backgroundColor: colors.green,
    textAlign: "center",
    paddingVertical: 10,
    paddingHorizontal: 10,
    color: colors.white,
    borderRadius: 5,
    width: "100%",
    flex: 1,
    flexDirection: "row",
  },
  createBtnTxt: {
    color: colors.white,
    textAlign: "center",
  },
  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 55,
    height: "100%",
  },
});
