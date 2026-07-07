import {
  StyleSheet,
  View,
  FlatList,
  ActivityIndicator,
  TouchableOpacity,
  Animated,
} from "react-native";
import Topbar from "../../../components/Topbar/Topbar";
import Text from "../../../components/text/Text";
import { colors } from "../../../themes/colors";
import { LinearGradient } from "expo-linear-gradient";
import { useGetSaleReturnCreateListQuery } from "../../../redux/features/sales-return/salesReturnApi";
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
import { AntDesign } from "@expo/vector-icons";
import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
const SaleReturn = () => {
  const [page, setPage] = useState(1);
  const [salesReturnList, setSalesReturnList] = useState([]);
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  //handle navigation
  const handleShowNavigation = (id) => {
    router.push(`/admin/pos-invoice/${id}`);
  };
  const handleReturnNavigation = (id) => {
    router.push(`/admin/sales/sales-return-create/${id}`);
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
        <TouchableOpacity onPress={() => handleShowNavigation(id)}>
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
        <TouchableOpacity onPress={() => handleReturnNavigation(id)}>
          <LinearGradient
            colors={["#FDCC95", "#FF9138"]}
            style={styles.swipeBtn}
          >
            <Ionicons name="return-down-back" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Return
            </Text>
          </LinearGradient>
        </TouchableOpacity>
      </Animated.View>
    );
  };

  //render item

  const renderItem = ({ item }) => (
    <GestureHandlerRootView>
      <Swipeable
        renderRightActions={(progress, dragX) =>
          rightSwipe(progress, dragX, item?.id)
        }
        key={item?.id}
      >
        <View style={styles.item}>
          <View style={styles.left}>
            <Text
              preset="h5_m"
              style={{ marginBottom: 3, color: colors.black }}
            >
              {item?.customer || "Walk-In Customer"}
            </Text>
            <Text
              preset="h6"
              style={{ color: colors.fontColor, marginBottom: 2 }}
            >
              <Text preset="h6_m" style={{ color: colors.themeColor }}>
                Invoice #{item?.invoice_id}
              </Text>{" "}
              / Total paid-{item?.total_paid}
            </Text>
            <Text
              preset="h6"
              style={{ color: colors.fontColor, marginBottom: 2 }}
            >
              Warehouse- {item?.warehouse}
            </Text>
            <Text
              preset="h6"
              style={{ color: colors.fontColor, marginBottom: 2 }}
            >
              Delivery Status-{" "}
              {item?.delivery_status == "pending" ? (
                <Text style={{ color: "#f8b425", textTransform: "capitalize" }}>
                  {item?.delivery_status}
                </Text>
              ) : (
                <Text
                  style={{
                    color: colors.themeColor,
                    textTransform: "capitalize",
                  }}
                >
                  {item?.delivery_status}
                </Text>
              )}
            </Text>
            <Text
              preset="h6"
              style={{ color: colors.fontColor, marginBottom: 2 }}
            >
              Date- {item?.date}
            </Text>
          </View>
          <View style={{ alignItems: "flex-end" }}>
            <Text
              preset="h5"
              style={{ marginBottom: 5, color: colors.themeColor }}
            >
              {item?.total}
            </Text>
            {item?.status == "paid" ? (
              <LinearGradient
                colors={["#37DBD9", "#008AA1"]}
                style={[styles.paidBadge, { textTransform: "capitalize" }]}
              >
                <Text preset="h6" style={{ color: colors.white }}>
                  {item?.status}
                </Text>
              </LinearGradient>
            ) : (
              <LinearGradient
                colors={["#38a4f8", "#38a4f8"]}
                style={[styles.paidBadge, { textTransform: "capitalize" }]}
              >
                <Text preset="h6" style={{ color: colors.white }}>
                  {item?.status}
                </Text>
              </LinearGradient>
            )}
          </View>
        </View>
      </Swipeable>
    </GestureHandlerRootView>
  );
  //get data sales return list
  const {
    data: getSalesReturn,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetSaleReturnCreateListQuery(page);

  const { current_page, to, total } = getSalesReturn?.data?.meta || {};

  // Update invoiceData when getInvoiceData changes
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getSalesReturn?.data?.data) &&
      page == current_page
    ) {
      setSalesReturnList((prevData) => [
        ...prevData,
        ...getSalesReturn?.data?.data,
      ]);
    }
  }, [isSuccess, getSalesReturn]);

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
  } else if (getSalesReturn?.data?.length === 0) {
    content = <NotFoundMessage message="Return Not Found" />;
  } else if (salesReturnList?.length > 0) {
    content = (
      <FlatList
        data={salesReturnList}
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
      <Topbar title="Sales Return" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default SaleReturn;

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.white,
    borderRadius: 5,
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
    width: "75%",
  },
  paidBadge: {
    backgroundColor: colors.themeColor,
    paddingHorizontal: 8,
    paddingVertical: 4,
    color: colors.white,
    borderRadius: 2,
    textAlign: "center",
  },
  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 65,
    height: "100%",
  },
});
