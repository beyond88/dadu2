import {
  ScrollView,
  StyleSheet,
  View,
  FlatList,
  ActivityIndicator,
  Animated,
  TouchableOpacity,
} from "react-native";

import { LinearGradient } from "expo-linear-gradient";
import { useSelector } from "react-redux";
import { useState, useEffect } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import { colors } from "../../../../themes/colors";
import Text from "../../../../components/text/Text";
import {
  useGetSalesReturnRequestQuery,
  useSalesReturnRequestAcceptMutation,
  useSalesReturnRequestRejectMutation,
} from "../../../../redux/features/sales-return/salesReturnApi";
import { generate8DigitNumber } from "../../../../utils/helper";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";
import { router } from "expo-router";
import { AntDesign } from "@expo/vector-icons";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";

const SaleReturnRequest = () => {
  const [page, setPage] = useState(1);
  const [salesReturnRequest, setSalesReturnRequest] = useState([]);
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  //accept & reject sales return request
  const [
    salesReturnRequestAccept,
    {
      data: acceptData,
      isLoading: acceptIsLoading,
      isSuccess: acceptIsSuccess,
      isError: acceptIsError,
      error: acceptError,
    },
  ] = useSalesReturnRequestAcceptMutation();
  const [
    salesReturnRequestReject,
    {
      data: rejectData,
      isLoading: rejectIsLoading,
      isSuccess: rejectIsSuccess,
      isError: rejectIsError,
      error: rejectError,
    },
  ] = useSalesReturnRequestRejectMutation();
  //handle navigation

  const handleShowNavigation = (id) => {
    router.push(`/admin/sales/return-request/show/${id}`);
  };

  //handle accept
  const handleAccept = (id) => {
    salesReturnRequestAccept(id);
  };

  //handle reject
  const handleReject = (id) => {
    salesReturnRequestReject(id);
  };

  useEffect(() => {
    if (acceptIsSuccess) {
      showMessage({
        message: acceptData?.message,
        type: "success",
      });
      setSalesReturnRequest([]);
    }
    if (acceptIsError) {
      showMessage({
        message: acceptError?.data?.message,
        type: "danger",
      });
    }
  }, [acceptData, acceptIsSuccess, acceptIsError, acceptError]);

  useEffect(() => {
    if (rejectIsSuccess) {
      showMessage({
        message: rejectData?.message,
        type: "success",
      });
      setSalesReturnRequest([]);
    }
    if (rejectIsError) {
      showMessage({
        message: rejectError?.data?.message,
        type: "danger",
      });
    }
  }, [rejectData, rejectIsSuccess, rejectIsError, rejectError]);

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
        style={{ flexDirection: "row", transform: [{ translateX }, { scale }] }}
      >
        <TouchableOpacity
          onPress={() => handleAccept(item?.id)}
          disabled={item?.status === "accepted"}
        >
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={[
              styles.swipeBtn,
              item?.status === "accepted" && { opacity: 0.5 },
            ]}
          >
            <AntDesign name="eyeo" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Accept
            </Text>
          </LinearGradient>
        </TouchableOpacity>

        <TouchableOpacity
          onPress={() => handleReject(item?.id)}
          disabled={item?.status === "rejected" || item?.status === "accepted"}
        >
          <LinearGradient
            colors={["#EC4561", "#EC4561"]}
            style={[
              styles.swipeBtn,
              item?.status === "rejected" ||
                (item?.status === "accepted" && { opacity: 0.5 }),
            ]}
          >
            <AntDesign name="eyeo" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Reject
            </Text>
          </LinearGradient>
        </TouchableOpacity>

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
              {item?.invoice?.customer?.full_name}
            </Text>
            <Text preset="h6" style={{ color: colors.fontColor }}>
              <Text preset="h6_m" style={{ color: colors.themeColor }}>
                Invoice#{generate8DigitNumber(item?.invoice?.id)}
              </Text>{" "}
              / {item?.warehouse?.name}
            </Text>
            <Text preset="h6" style={{ color: colors.fontColor }}>
              {item?.return_date} / Total Product-
              {item?.invoice?.items_data?.length}
            </Text>
          </View>
          <View>
            {item?.status === "pending" && (
              <Text
                preset="h6_m"
                style={{ color: colors.yellow, textTransform: "capitalize" }}
              >
                {item?.status}
              </Text>
            )}
            {item?.status === "accepted" && (
              <Text
                preset="h6_m"
                style={{ color: colors.blue, textTransform: "capitalize" }}
              >
                {item?.status}
              </Text>
            )}
            {item?.status === "rejected" && (
              <Text
                preset="h6_m"
                style={{ color: colors.red, textTransform: "capitalize" }}
              >
                {item?.status}
              </Text>
            )}
          </View>
          <View style={{ alignItems: "flex-end" }}>
            <Text
              preset="h5"
              style={{ marginBottom: 5, color: colors.themeColor }}
            >
              {currency_symbol}
              {item?.invoice?.total}
            </Text>
            {item?.invoice?.status === "paid" && (
              <LinearGradient
                colors={["#37DBD9", "#008AA1"]}
                style={styles.paidBadge}
              >
                <Text preset="h6" style={{ color: colors.white }}>
                  {item?.invoice?.status}
                </Text>
              </LinearGradient>
            )}
            {item?.invoice?.status === "unpaid" && (
              <LinearGradient
                colors={["#FDCC95", "#FF9138"]}
                style={styles.paidBadge}
              >
                <Text preset="h6" style={{ color: colors.white }}>
                  {item?.invoice?.status}
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
    data: getSalesReturnRequest,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetSalesReturnRequestQuery(page);

  const { current_page, to, total } = getSalesReturnRequest?.data?.meta || {};

  // Update invoiceData when getInvoiceData changes
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getSalesReturnRequest?.data?.data) &&
      page == current_page
    ) {
      setSalesReturnRequest((prevData) => [
        ...prevData,
        ...getSalesReturnRequest?.data?.data,
      ]);
    }
  }, [isSuccess, getSalesReturnRequest]);

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
  } else if (getSalesReturnRequest?.data?.data?.length === 0) {
    content = <NotFoundMessage message="Product Not Found" />;
  } else if (salesReturnRequest.length > 0) {
    content = (
      <FlatList
        data={salesReturnRequest}
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
      {acceptIsLoading && <Loading />}
      {rejectIsLoading && <Loading />}
      <Topbar title="Sale Return Request" />
      <View style={{ paddingHorizontal: 20, marginBottom: 80 }}>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default SaleReturnRequest;

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
    width: "48%",
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
