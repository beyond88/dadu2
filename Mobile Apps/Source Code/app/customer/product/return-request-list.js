import React from "react";
import {
  Animated,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  View,
} from "react-native";
import Topbar from "../../../components/Topbar/Topbar";
import { useGetReturnInvoiceRequestQuery } from "../../../redux/features/invoice-return/invoiceReturnApi";
import { LinearGradient } from "expo-linear-gradient";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import Svg, { Path } from "react-native-svg";
import Text from "../../../components/text/Text";
import { colors } from "../../../themes/colors";
import { generate8DigitNumber } from "../../../utils/helper";
import { useSelector } from "react-redux";
import TableLoader from "../../../components/TableLoader/TableLoader";
import { useRouter } from "expo-router";
import ErrorMessage from "../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../components/CustomMessage/NotFoundMessage";

const ReturnRequestList = () => {
  const router = useRouter();
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  //get return request data
  const {
    data: returnRequestData,
    isLoading,
    error,
    isError,
  } = useGetReturnInvoiceRequestQuery();

  //handle navigation

  const handleNavigation = (slug) => {
    router.push(`/customer/product/return-request-details/${slug}`);
  };
  //Swipeable right swipe

  const rightSwipe = (progress, dragX, id) => {
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
        style={{ flexDirection: "row", transform: [{ translateX }, { scale }] }}
      >
        <TouchableOpacity onPress={() => handleNavigation(id)}>
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={styles.swipeBtn}
          >
            <Svg
              xmlns="http://www.w3.org/2000/svg"
              width="17"
              height="12"
              viewBox="0 0 17 12"
              fill="none"
            >
              <Path
                d="M8.5 1C2.78571 1 0.5 6.14286 0.5 6.14286C0.5 6.14286 2.78571 11.2857 8.5 11.2857C14.2143 11.2857 16.5 6.14286 16.5 6.14286C16.5 6.14286 14.2143 1 8.5 1Z"
                stroke="white"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
              <Path
                d="M8.50021 9.00005C10.0782 9.00005 11.3574 7.72087 11.3574 6.14291C11.3574 4.56495 10.0782 3.28577 8.50021 3.28577C6.92225 3.28577 5.64307 4.56495 5.64307 6.14291C5.64307 7.72087 6.92225 9.00005 8.50021 9.00005Z"
                stroke="white"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </Svg>
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Show
            </Text>
          </LinearGradient>
        </TouchableOpacity>
      </Animated.View>
    );
  };

  //Render content
  let content = null;
  if (isLoading) {
    content = <TableLoader />;
  } else if (isError) {
    content = <ErrorMessage message={error?.data?.message} />;
  } else if (returnRequestData?.data?.length === 0) {
    content = <NotFoundMessage message="Return Request Not Found" />;
  } else if (returnRequestData?.data?.length > 0) {
    content = returnRequestData?.data?.map((item, index) => (
      <GestureHandlerRootView>
        <Swipeable
          renderRightActions={(progress, dragX) =>
            rightSwipe(progress, dragX, item?.invoice_id)
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
                  Invoice#{generate8DigitNumber(item?.invoice_id)}
                </Text>{" "}
                / {item?.warehouse?.name}
              </Text>
              <Text preset="h6" style={{ color: colors.fontColor }}>
                {item?.return_date} /{" "}
                <Text preset="h6_m" style={{ color: colors.red }}>
                  Total-p: {item?.sale_return_request_items?.length}
                </Text>
              </Text>
            </View>
            <View>
              {item?.invoice?.delivery_status == "delivered" && (
                <Text
                  preset="h6_m"
                  style={{
                    color: colors.blue,
                    marginBottom: 3,
                    textTransform: "capitalize",
                  }}
                >
                  DS- {item?.invoice?.delivery_status}
                </Text>
              )}
              {item?.invoice?.delivery_status == "canceled" && (
                <Text
                  preset="h6_m"
                  style={{
                    color: colors.red,
                    marginBottom: 3,
                    textTransform: "capitalize",
                  }}
                >
                  DS- {item?.invoice?.delivery_status}
                </Text>
              )}
              {item?.invoice?.delivery_status == "pending" && (
                <Text
                  preset="h6_m"
                  style={{
                    color: colors.yellow,
                    marginBottom: 3,
                    textTransform: "capitalize",
                  }}
                >
                  DS- {item?.invoice?.delivery_status}
                </Text>
              )}
            </View>
            <View style={{ alignItems: "flex-end" }}>
              <Text
                preset="h5"
                style={{ marginBottom: 5, color: colors.themeColor }}
              >
                {currency_symbol} {item?.invoice?.total_paid}
              </Text>
              {item?.status === "accepted" && (
                <LinearGradient
                  colors={["#37DBD9", "#008AA1"]}
                  style={styles.paidBadge}
                >
                  <Text
                    preset="h6"
                    style={{ color: colors.white, textTransform: "capitalize" }}
                  >
                    {item?.status}
                  </Text>
                </LinearGradient>
              )}
              {item?.status === "pending" && (
                <LinearGradient
                  colors={["#FDCC95", "#FF9138"]}
                  style={styles.paidBadge}
                >
                  <Text
                    preset="h6"
                    style={{ color: colors.white, textTransform: "capitalize" }}
                  >
                    {item?.status}
                  </Text>
                </LinearGradient>
              )}
              {item?.status === "rejected" && (
                <LinearGradient
                  colors={["#FFACA2", "#FE5F4B"]}
                  style={styles.paidBadge}
                >
                  <Text
                    preset="h6"
                    style={{ color: colors.white, textTransform: "capitalize" }}
                  >
                    {item?.status}
                  </Text>
                </LinearGradient>
              )}
            </View>
          </View>
        </Swipeable>
      </GestureHandlerRootView>
    ));
  }
  return (
    <>
      <Topbar title="Request List" customer={true} />
      <ScrollView style={{ paddingHorizontal: 20 }}>
        <View style={styles.card}>{content}</View>
      </ScrollView>
    </>
  );
};

export default ReturnRequestList;

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.white,
    borderRadius: 5,
    marginBottom: 80,
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
    textTransform: "capitalize",
  },

  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 65,
    height: "100%",
  },
});
