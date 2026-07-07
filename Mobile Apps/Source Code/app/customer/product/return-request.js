import React from "react";
import {
  Animated,
  ScrollView,
  StyleSheet,
  TouchableOpacity,
  View,
} from "react-native";
import Topbar from "../../../components/Topbar/Topbar";
import { useGetReturnInvoiceListQuery } from "../../../redux/features/invoice-return/invoiceReturnApi";
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

const ReturnRequest = () => {
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
  } = useGetReturnInvoiceListQuery();

  //handle navigation

  const handleNavigation = (slug) => {
    router.push(`/customer/invoice/${slug}`);
  };

  const handleRequestCreateNavigate = (slug) => {
    router.push(`/customer/product/return-request-create/${slug}`);
  };
  //Swipeable right swipe

  const rightSwipe = (progress, dragX, id) => {
    const scale = dragX.interpolate({
      inputRange: [-100, 0],
      outputRange: [0.7, 0],
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
        <TouchableOpacity onPress={() => handleRequestCreateNavigate(id)}>
          <LinearGradient
            colors={["#42CA7F", "#42CA7F"]}
            style={styles.swipeBtn}
          >
            <Svg
              xmlns="http://www.w3.org/2000/svg"
              width="19"
              height="16"
              viewBox="0 0 19 16"
              fill="none"
            >
              <Path
                d="M5.5 9L1.5 5L5.5 1"
                stroke="white"
                strokeWidth="1.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
              <Path
                d="M5.5 14.3333H12.8333C14.071 14.3333 15.258 13.8417 16.1332 12.9665C17.0083 12.0913 17.5 10.9043 17.5 9.66667V9.66667C17.5 8.42899 17.0083 7.242 16.1332 6.36684C15.258 5.49167 14.071 5 12.8333 5H1.5"
                stroke="white"
                strokeWidth="1.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </Svg>
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Return
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
  } else if (returnRequestData?.data?.data?.length === 0) {
    content = <NotFoundMessage message="Return Request  Not Found" />;
  } else if (returnRequestData?.data?.data?.length > 0) {
    content = returnRequestData?.data?.data?.map((item, index) => (
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
                {item?.customer?.full_name}
              </Text>
              <Text preset="h6" style={{ color: colors.fontColor }}>
                <Text preset="h6_m" style={{ color: colors.themeColor }}>
                  Invoice#{generate8DigitNumber(item?.id)}
                </Text>{" "}
                / {item?.warehouse?.name}
              </Text>
              <Text preset="h6" style={{ color: colors.fontColor }}>
                {item?.date} /{" "}
                <Text preset="h6_m" style={{ color: colors.red }}>
                  {item?.payment_type}
                </Text>
              </Text>
            </View>
            <View>
              {item?.delivery_status === "delivered" && (
                <Text
                  preset="h6_m"
                  style={{
                    color: colors.blue,
                    marginBottom: 3,
                    textTransform: "capitalize",
                  }}
                >
                  {item?.delivery_status}
                </Text>
              )}
              {item?.delivery_status === "canceled" && (
                <Text
                  preset="h6_m"
                  style={{
                    color: colors.red,
                    marginBottom: 3,
                    textTransform: "capitalize",
                  }}
                >
                  {item?.delivery_status}
                </Text>
              )}
              {item?.delivery_status === "pending" && (
                <Text
                  preset="h6_m"
                  style={{
                    color: colors.yellow,
                    marginBottom: 3,
                    textTransform: "capitalize",
                  }}
                >
                  {item?.delivery_status}
                </Text>
              )}

              {item?.invoice?.status === "paid" && (
                <Text
                  preset="h6_m"
                  style={{ color: colors.blue, textTransform: "capitalize" }}
                >
                  {item?.invoice?.status}
                </Text>
              )}
              {item?.invoice?.status === "pending" && (
                <Text
                  preset="h6_m"
                  style={{ color: colors.yellow, textTransform: "capitalize" }}
                >
                  {item?.invoice?.status}
                </Text>
              )}
              {item?.invoice?.status === "delivered" && (
                <Text
                  preset="h6_m"
                  style={{ color: colors.blue, textTransform: "capitalize" }}
                >
                  {item?.invoice?.status}
                </Text>
              )}
            </View>
            <View style={{ alignItems: "flex-end" }}>
              <Text
                preset="h5"
                style={{ marginBottom: 5, color: colors.themeColor }}
              >
                {currency_symbol} {item?.total}
              </Text>
              {item?.status === "paid" && (
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
      <Topbar title="Request Creatable" customer={true} />
      <ScrollView style={{ paddingHorizontal: 20 }}>
        <View style={styles.card}>{content}</View>
      </ScrollView>
    </>
  );
};

export default ReturnRequest;

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
