import {
  StyleSheet,
  View,
  Animated,
  FlatList,
  ActivityIndicator,
  Platform,
} from "react-native";

import Topbar from "../../../components/Topbar/Topbar";
import { colors } from "../../../themes/colors";
import Text from "../../../components/text/Text";
import { LinearGradient } from "expo-linear-gradient";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import Svg, { Path } from "react-native-svg";
import { useGetCustomerInvoiceQuery } from "../../../redux/features/pos-invoice/posInvoiceApi";
import * as FileSystem from "expo-file-system";
import { TouchableOpacity } from "react-native";
import { Link, useRouter } from "expo-router";
import { generate8DigitNumber } from "../../../utils/helper";
import TableLoader from "../../../components/TableLoader/TableLoader";
import { useSelector } from "react-redux";
import ErrorMessage from "../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../components/CustomMessage/NotFoundMessage";
import { useEffect, useState } from "react";

const PosInvoice = () => {
  const router = useRouter();
  const apiUrl = process.env.EXPO_PUBLIC_API_URL;
  const [page, setPage] = useState(1);
  const [invoiceData, setInvoiceData] = useState([]);

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  //Download pdf

  const handleDownloadPDF = async (invoiceId) => {
    const filename = "invoice";
    const result = await FileSystem.downloadAsync(
      `${apiUrl}/customer/invoice-download/${invoiceId}`,
      FileSystem.documentDirectory + filename
    );
    save(result.uri, filename, result.headers["Content-Type"]);
  };
  const save = async (uri, filename, mimetype) => {
    if (Platform.OS === "android") {
      const permissions =
        await FileSystem.StorageAccessFramework.requestDirectoryPermissionsAsync();
      if (permissions.granted) {
        const base64 = await FileSystem.readAsStringAsync(uri, {
          encoding: FileSystem.EncodingType.Base64,
        });
        await FileSystem.StorageAccessFramework.createFileAsync(
          permissions.directoryUri,
          filename,
          mimetype
        )
          .then(async (uri) => {
            await FileSystem.writeAsStringAsync(uri, base64, {
              encoding: FileSystem.EncodingType.Base64,
            });
          })
          .catch((e) => console.log(e));
      } else {
        shareAsync(uri);
      }
    } else {
      shareAsync(uri);
    }
  };
  //handle navigation

  const handleNavigation = (slug) => {
    router.push(`/customer/invoice/${slug}`);
  };
  //Swipeable right swipe

  const rightSwipe = (progress, dragX, id) => {
    const scale = dragX.interpolate({
      inputRange: [-100, 0],
      outputRange: [0.73, 0],
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
        <TouchableOpacity onPress={() => handleDownloadPDF(id)}>
          <LinearGradient
            colors={["#FDCC95", "#FF9138"]}
            style={styles.swipeBtn}
          >
            <Svg
              xmlns="http://www.w3.org/2000/svg"
              width="18"
              height="16"
              viewBox="0 0 18 16"
              fill="none"
            >
              <Path
                d="M12.6923 9H16.3846C16.5478 9 16.7044 9.06483 16.8198 9.18024C16.9352 9.29565 17 9.45217 17 9.61538V14.5385C17 14.7017 16.9352 14.8582 16.8198 14.9736C16.7044 15.089 16.5478 15.1538 16.3846 15.1538H1.61538C1.45217 15.1538 1.29565 15.089 1.18024 14.9736C1.06483 14.8582 1 14.7017 1 14.5385V9.61538C1 9.45217 1.06483 9.29565 1.18024 9.18024C1.29565 9.06483 1.45217 9 1.61538 9H5.30769"
                stroke="white"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
              <Path
                d="M9 1V9"
                stroke="white"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
              <Path
                d="M5.30811 5.30774L9.00041 9.00005L12.6927 5.30774"
                stroke="white"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
              <Path
                d="M14.0385 12.0769C14.0385 12.3105 13.8491 12.5 13.6155 12.5C13.3818 12.5 13.1924 12.3105 13.1924 12.0769C13.1924 11.8432 13.3818 11.6538 13.6155 11.6538C13.8491 11.6538 14.0385 11.8432 14.0385 12.0769Z"
                fill="#42CA7F"
                stroke="white"
              />
            </Svg>
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Download
            </Text>
          </LinearGradient>
        </TouchableOpacity>
      </Animated.View>
    );
  };
  //render Item
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
              {item?.customer?.first_name} {item?.customer?.last_name}
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

            <Text preset="h6_m" style={{ color: colors.black }}>
              Paid:{" "}
              <Text preset="h6" style={{ color: colors.yellow }}>
                {currency_symbol} {item?.total_paid}
              </Text>
            </Text>
          </View>
          <View style={{ alignItems: "flex-end" }}>
            <Text
              preset="h5"
              style={{ marginBottom: 5, color: colors.themeColor }}
            >
              {currency_symbol}
              {item?.total}
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
  );

  //Get data from API
  const {
    data: getInvoiceData,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetCustomerInvoiceQuery(page);

  const { current_page, to, total } = getInvoiceData?.data?.meta || {};

  // Update invoiceData when getInvoiceData changes
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getInvoiceData?.data?.data) &&
      page == current_page
    ) {
      setInvoiceData((prevData) => [
        ...prevData,
        ...getInvoiceData?.data?.data,
      ]);
    }
  }, [isSuccess, getInvoiceData]);

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
  } else if (getInvoiceData?.data?.data?.length === 0) {
    content = <NotFoundMessage message="Invoice Not Found" />;
  } else if (invoiceData?.length > 0) {
    content = (
      <FlatList
        data={invoiceData}
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
      <Topbar title="Invoice" customer={true} />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <View style={styles.card}>
          <View style={styles.listCreateBtn}>
            <Text preset="h2">Invoice List</Text>
            <Link href="/customer/invoice/create">
              <View>
                <LinearGradient
                  colors={["#37DBD9", "#008AA1"]}
                  style={styles.createBtn}
                >
                  <Text preset="h3" style={styles.buttonText}>
                    Create
                  </Text>
                </LinearGradient>
              </View>
            </Link>
          </View>
          {content}
        </View>
      </View>
    </>
  );
};

export default PosInvoice;

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
  listCreateBtn: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 10,
    marginTop: 20,
    paddingBottom: 20,
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
  },
  createBtn: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    paddingHorizontal: 32,
    borderRadius: 5,
    elevation: 3,
    height: 40,
  },
  buttonText: {
    color: colors.white,
  },
});
