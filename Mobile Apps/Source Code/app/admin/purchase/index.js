import {
  ActivityIndicator,
  FlatList,
  StyleSheet,
  View,
  Animated,
  TouchableOpacity,
  Alert,
  Pressable,
} from "react-native";
import Text from "../../../components/text/Text";
import Topbar from "../../../components/Topbar/Topbar";
import { colors } from "../../../themes/colors";
import {
  useCancelPurchaseMutation,
  useDeletePurchaseMutation,
  useGetPurchaseQuery,
} from "../../../redux/features/purchase/purchaseApi";
import TableLoader from "../../../components/TableLoader/TableLoader";
import { useSelector } from "react-redux";
import ErrorMessage from "../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../components/CustomMessage/NotFoundMessage";
import { useState } from "react";
import { useEffect } from "react";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { LinearGradient } from "expo-linear-gradient";
import {
  AntDesign,
  Feather,
  MaterialCommunityIcons,
  Ionicons,
} from "@expo/vector-icons";
import { Link, router } from "expo-router";
import Loading from "../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";

const PurchaseList = () => {
  const [page, setPage] = useState(1);
  const [purchaseList, setPurchaseList] = useState([]);
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  //handle show navigation
  const handleShowNavigation = (id) => {
    router.push(`/admin/purchase/${id}`);
  };
  //handle return purchase
  const handleReturnPurchase = (id) => {
    router.push(`/admin/purchase/return/${id}`);
  };
  //handle delete
  const [
    deletePurchase,
    {
      data: deleteData,
      isSuccess: deleteIsSuccess,
      isLoading: deleteIsLoading,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeletePurchaseMutation();
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
            deletePurchase(id);
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
      setPurchaseList([]);
    }

    if (deleteIsError) {
      showMessage({
        message: deleteError?.data?.message,
        type: "danger",
      });
    }
  }, [deleteData, deleteIsSuccess, deleteError, deleteIsError]);

  //handle cancel

  const handleCancel = (id) => {
    router.push(`/admin/purchase/cancel/${id}`);
  };

  //handle receive
  const handleReceiveNavigation = (id) => {
    router.push(`/admin/purchase/receive/${id}`);
  };
  //Swipeable right swipe

  const rightSwipe = (progress, dragX, item) => {
    const scale = dragX.interpolate({
      inputRange: [-100, 0],
      outputRange: [0.32, 0],
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

        <TouchableOpacity
          onPress={() => router.push(`/admin/purchase/edit/${item?.id}`)}
          disabled={item?.is_received !== "requested" ? true : false}
        >
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={[
              styles.swipeBtn,
              item?.is_received !== "requested" && styles.disabled,
            ]}
          >
            <AntDesign name="edit" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Edit
            </Text>
          </LinearGradient>
        </TouchableOpacity>
        <TouchableOpacity
          onPress={() => handleCancel(item?.id)}
          disabled={
            item?.is_received == "cancel" || item?.is_received == "Received"
              ? true
              : false
          }
        >
          <LinearGradient
            colors={["#EC4561", "#EC4561"]}
            style={[
              styles.swipeBtn,
              (item?.is_received === "cancel" ||
                item?.is_received === "Received") &&
                styles.disabled,
            ]}
          >
            <Ionicons name="remove-circle-outline" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Cancel
            </Text>
          </LinearGradient>
        </TouchableOpacity>
        <TouchableOpacity
          onPress={() => handleReceiveNavigation(item?.id)}
          disabled={item?.is_received == "cancel" ? true : false}
        >
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={[
              styles.swipeBtn,
              item?.is_received == "cancel" && styles.disabled,
            ]}
          >
            <Feather name="arrow-down-circle" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Receive
            </Text>
          </LinearGradient>
        </TouchableOpacity>
        <TouchableOpacity
          onPress={() => handleReturnPurchase(item?.id)}
          disabled={item?.is_received == "cancel" ? true : false}
        >
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={[
              styles.swipeBtn,
              item?.is_received == "cancel" && styles.disabled,
            ]}
          >
            <MaterialCommunityIcons name="reload" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Return
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
  const renderItem = ({ item }) => (
    <GestureHandlerRootView>
      <Swipeable
        renderRightActions={(progress, dragX) =>
          rightSwipe(progress, dragX, item)
        }
        key={item?.id}
      >
        <View style={styles.item}>
          <View style={styles.left}>
            <Text
              preset="h5_m"
              style={{ marginBottom: 3, color: colors.black }}
            >
              {item?.supplier_name}
            </Text>
            <Text preset="h6" style={{ color: colors.fontColor }}>
              <Text preset="h6_m" style={{ color: colors.themeColor }}>
                P#{item?.purchase_number}
              </Text>{" "}
              / {item?.warehouse_name}
            </Text>
            <Text preset="h6" style={{ color: colors.fontColor }}>
              {item?.date.split(" ")[0]} / total product: {item?.total_product}
            </Text>
          </View>
          <View style={styles.middle}>
            {item?.is_received == "Received" && (
              <Text style={[styles.statusBadge]} preset="h6">
                {item?.is_received}
              </Text>
            )}
            {item?.is_received == "cancel" && (
              <Text style={[styles.statusBadge, styles.cancel]} preset="h6">
                {item?.is_received}
              </Text>
            )}

            {item?.is_received == "requested" && (
              <Text
                style={[styles.statusBadge, styles.notReceived]}
                preset="h6"
              >
                {item?.is_received}
              </Text>
            )}
          </View>
          <View style={styles.right}>
            <Text
              preset="h5"
              style={{ marginBottom: 5, color: colors.themeColor }}
            >
              {currency_symbol} {item?.total}
            </Text>
            {item?.is_missing?.status && (
              <Text style={styles.missingBadge} preset="h6">
                {item?.is_missing?.text} {item?.is_missing?.quantity}
              </Text>
            )}
          </View>
        </View>
      </Swipeable>
    </GestureHandlerRootView>
  );
  const {
    data: getPurchaseData,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetPurchaseQuery(page);

  const { current_page, to, total } = getPurchaseData?.data?.meta || {};

  // Update invoiceData when getInvoiceData changes
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getPurchaseData?.data?.data) &&
      page == current_page
    ) {
      setPurchaseList((prevData) => [
        ...prevData,
        ...getPurchaseData?.data?.data,
      ]);
    }
  }, [isSuccess, getPurchaseData]);

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

  //Render content
  let content = null;
  if (isLoading) {
    content = <TableLoader />;
  } else if (isError) {
    content = <ErrorMessage message={error?.data?.message} />;
  } else if (purchaseList?.length === 0) {
    content = <NotFoundMessage message="Purchase Not Found" />;
  } else if (purchaseList?.length > 0) {
    content = (
      <FlatList
        data={purchaseList}
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

      <Topbar title="Purchase List" />
      <View style={{ marginHorizontal: 20, marginBottom: 180 }}>
        <View>
          <Pressable
            onPress={() => router.push("/admin/purchase/create")}
            style={{
              backgroundColor: colors.themeColor,
              alignItems: "center",
              padding: 10,
              borderRadius: 5,
            }}
          >
            <Text style={{ color: colors.white }} preset="h5">
              Create Purchase
            </Text>
          </Pressable>
        </View>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default PurchaseList;

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.white,
    borderRadius: 5,
    height: "100%",
  },
  item: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 10,
    paddingVertical: 12,
    flexWrap: "wrap",
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
  },
  left: {
    width: "40%",
  },
  middle: {
    width: "34%",
    justifyContent: "center",
    flexDirection: "row",
    justifyContent: "center",
  },
  right: {
    flex: 1,
  },
  statusBadge: {
    backgroundColor: colors.green,
    paddingHorizontal: 8,
    color: colors.white,
    borderRadius: 2,
    paddingVertical: 3,
    textTransform: "capitalize",
  },

  cancel: {
    backgroundColor: colors.red,
  },
  notReceived: {
    backgroundColor: colors.yellow,
  },
  missingBadge: {
    backgroundColor: "#EC4561",
    paddingHorizontal: 5,
    paddingVertical: 2,
    color: colors.white,
    borderRadius: 2,
    textAlign: "center",
    flexDirection: "row",
  },
  noMissingBadge: {
    backgroundColor: colors.themeColor,
  },
  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 50,
    height: "100%",
  },
  disabled: {
    opacity: 0.5,
  },
});
