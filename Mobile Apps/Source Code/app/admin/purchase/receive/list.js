import React, { useEffect, useState } from "react";

import {
  View,
  StyleSheet,
  FlatList,
  Animated,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
} from "react-native";
import { useRouter } from "expo-router";
import Text from "../../../../components/text/Text";
import { colors } from "../../../../themes/colors";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { LinearGradient } from "expo-linear-gradient";
import { AntDesign } from "@expo/vector-icons";
import { showMessage } from "react-native-flash-message";
import {
  useDeleteWeightUnitMutation,
  useGetWeightUnitsQuery,
} from "../../../../redux/features/catalog/catalogApi";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  useGetPurchaseReceiveListQuery,
  usePurchaseReceivedDeleteMutation,
} from "../../../../redux/features/purchase/purchaseApi";
import Loading from "../../../../components/Loading/Loading";

const PurchaseReceiveListPage = () => {
  const [page, setPage] = useState(1);
  const [receiveList, setReceiveList] = useState([]);

  //router
  const router = useRouter();

  //delete category mutation
  const [
    purchaseReceivedDelete,
    {
      data: deleteData,
      isLoading: deleteIsLoading,
      isSuccess: deleteIsSuccess,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = usePurchaseReceivedDeleteMutation();
  //handle navigation
  const handleNavigation = (id) => {
    router.push(`/admin/purchase/receive/show/${id}`);
  };
  //handle delete
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
            purchaseReceivedDelete(id);
          },
        },
      ],
      { cancelable: false }
    );
  };
  //Swipeable right swipe

  const rightSwipe = (progress, dragX, id) => {
    const scale = dragX.interpolate({
      inputRange: [-100, 0],
      outputRange: [0.75, 0],
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
        <TouchableOpacity onPress={() => handleNavigation(id)}>
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={styles.swipeBtn}
          >
            <AntDesign name="eyeo" size={16} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Show
            </Text>
          </LinearGradient>
        </TouchableOpacity>
        <TouchableOpacity onPress={() => handleDelete(id)}>
          <LinearGradient
            colors={["#EC4561", "#EC4561"]}
            style={styles.swipeBtn}
          >
            <AntDesign name="delete" size={16} color="white" />
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
          rightSwipe(progress, dragX, item?.id)
        }
        key={item?.id}
      >
        <View style={styles.itemWrap}>
          <View style={styles.item}>
            <View style={styles.left}>
              <View>
                <Text
                  preset="h5_m"
                  style={{ marginBottom: 3, color: colors.black }}
                >
                  PN: {item?.purchase_number}
                </Text>
                <Text
                  preset="h6"
                  style={{ color: colors.fontColor, marginBottom: 2 }}
                >
                  Supplier- {item?.supplier_name}
                </Text>
                <Text
                  preset="h6"
                  style={{ color: colors.fontColor, marginBottom: 2 }}
                >
                  Warehouse- {item?.warehouse_name}
                </Text>
                <Text
                  preset="h6"
                  style={{ color: colors.fontColor, marginBottom: 2 }}
                >
                  Date- {item?.receive_date}
                </Text>
                <Text
                  preset="h6"
                  style={{ color: colors.fontColor, marginBottom: 2 }}
                >
                  Total product- {item?.total_received_product}
                </Text>
              </View>
            </View>
            <View style={{ alignItems: "flex-end" }}>
              <Text
                preset="h5"
                style={{ marginBottom: 5, color: colors.themeColor }}
              >
                {item?.total}
              </Text>
            </View>
          </View>
        </View>
      </Swipeable>
    </GestureHandlerRootView>
  );

  //get category list from api
  const {
    data: receiveListData,
    isSuccess,
    isLoading,
    isError,
    error,
  } = useGetPurchaseReceiveListQuery(page);

  const { current_page, to, total } = receiveListData?.data?.meta || {};

  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(receiveListData?.data?.data) &&
      page == current_page
    ) {
      setReceiveList((prevData) => [
        ...prevData,
        ...receiveListData?.data?.data,
      ]);
    }
  }, [isSuccess, receiveListData]);

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

  //delete error success message

  useEffect(() => {
    if (deleteIsSuccess) {
      setReceiveList([]);
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

  //render content
  let content = null;
  if (isLoading) {
    content = <TableLoader />;
  } else if (isError) {
    content = <ErrorMessage message={error?.data?.message} />;
  } else if (receiveList?.length === 0) {
    content = <NotFoundMessage message="Purchase Receive Not Found" />;
  } else if (receiveList?.length > 0) {
    content = (
      <FlatList
        data={receiveList}
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
      <Topbar title="Purchase Receive List" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default PurchaseReceiveListPage;

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
    width: "78%",
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
  inActiveBtn: {
    backgroundColor: colors.red,
  },

  createBtn: {
    backgroundColor: colors.green,
    textAlign: "center",
    paddingVertical: 10,
    paddingHorizontal: 20,
    color: colors.white,
    borderRadius: 5,
  },
  createBtnTxt: {
    color: colors.white,
    textAlign: "center",
  },
  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 65,
    height: "100%",
  },
});
