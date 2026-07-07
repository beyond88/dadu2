import { useState, useEffect, useRef } from "react";
import {
  StyleSheet,
  ScrollView,
  View,
  Animated,
  ActivityIndicator,
  FlatList,
  Pressable,
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
import {
  useChangeDeliveredStatusMutation,
  useChangeStatusMutation,
  useDeleteInvoiceMutation,
  useGetPosInvoiceQuery,
} from "../../../redux/features/pos-invoice/posInvoiceApi";
import * as FileSystem from "expo-file-system";
import { TouchableOpacity } from "react-native";
import { Link, useRouter } from "expo-router";
import { generate8DigitNumber } from "../../../utils/helper";
import TableLoader from "../../../components/TableLoader/TableLoader";
import { useSelector } from "react-redux";
import ErrorMessage from "../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../components/CustomMessage/NotFoundMessage";
import RBSheet from "react-native-raw-bottom-sheet";
import {
  AntDesign,
  Entypo,
  FontAwesome,
  MaterialCommunityIcons,
  Fontisto,
} from "@expo/vector-icons";
import Loading from "../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";

const PosInvoice = () => {
  const router = useRouter();
  const apiUrl = process.env.EXPO_PUBLIC_API_URL;
  const [page, setPage] = useState(1);
  const [invoiceData, setInvoiceData] = useState([]);
  const [singleInvoice, setSingleInvoice] = useState(null);
  const refRBSheet = useRef();
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  //Download pdf

  const handleDownloadPDF = async (invoiceId) => {
    const filename = "invoice";
    const result = await FileSystem.downloadAsync(
      `${apiUrl}/admin/invoice-download/${invoiceId}`,
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
    router.push(`/admin/pos-invoice/${slug}`);
  };

  //Swipeable right swipe

  const rightSwipe = (progress, dragX, item) => {
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
        <TouchableOpacity onPress={() => handleNavigation(item?.id)}>
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
        <TouchableOpacity onPress={() => handleSlideUp(item)}>
          <LinearGradient
            colors={["#FDCC95", "#FF9138"]}
            style={styles.swipeBtn}
          >
            <Entypo name="dots-three-horizontal" size={24} color="white" />
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
          <View style={styles.middle}>
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
          <View style={styles.right}>
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
            {item?.status === "partially_paid" && (
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
  } = useGetPosInvoiceQuery(page);

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

  //handle handleSlideUp
  const handleSlideUp = (invoice) => {
    setSingleInvoice(invoice);

    refRBSheet.current.open();
  };

  // invoice status change

  const [
    changeStatus,
    {
      data: changeStatusData,
      isSuccess: changeStatusIsSuccess,
      isLoading: changeStatusIsLoading,
      isError: changeStatusIsError,
      error: changeStatusError,
    },
  ] = useChangeStatusMutation();

  const handleStatusChange = (status) => {
    changeStatus({ id: singleInvoice?.id, status });
  };

  // delete invoice
  const [
    deleteInvoice,
    {
      data: deleteData,
      isLoading: deleteIsLoading,
      isSuccess: deleteIsSuccess,
      error: deleteIsError,
      isError: deleteError,
    },
  ] = useDeleteInvoiceMutation();
  const handleDeleteInvoice = () => {
    deleteInvoice(singleInvoice?.id);
  };
  //handle change status success message
  useEffect(() => {
    if (changeStatusIsSuccess) {
      showMessage({
        message: changeStatusData.message,
        type: "success",
      });
      setInvoiceData([]);
      setPage(1);
      refRBSheet.current.close();
    }
    if (changeStatusIsError) {
      showMessage({
        message: changeStatusError.data.message,
        type: "danger",
      });
    }
  }, [
    changeStatusData,
    changeStatusIsSuccess,
    changeStatusIsError,
    changeStatusError,
  ]);
  //handle delete invoice success message
  useEffect(() => {
    if (deleteIsSuccess) {
      showMessage({
        message: deleteData.message,
        type: "success",
      });
      setInvoiceData([]);
      setPage(1);
      refRBSheet.current.close();
    }
    if (deleteIsError) {
      showMessage({
        message: deleteError.data.message,
        type: "danger",
      });
    }
  }, [deleteData, deleteIsSuccess, deleteIsError, deleteError]);
  return (
    <>
      {changeStatusIsLoading && <Loading />}
      {deleteIsLoading && <Loading />}
      <RBSheet
        ref={refRBSheet}
        closeOnDragDown={true}
        closeOnPressMask={false}
        customStyles={{
          wrapper: {
            backgroundColor: "#1a253075",
            borderRadius: 10,
          },
          container: {
            backgroundColor: "#fff",
            borderTopLeftRadius: 20,
            borderTopRightRadius: 20,
          },
          draggableIcon: {
            backgroundColor: "#000",
          },
        }}
      >
        <ScrollView>
          <View style={{ paddingHorizontal: 20, paddingTop: 15 }}>
            <Pressable
              onPress={() =>
                router.push(`/admin/pos-invoice/edit/${singleInvoice?.id}`)
              }
            >
              <View style={styles.dropupMenuItem}>
                <AntDesign name="edit" size={20} color="black" />
                <Text preset="h3_r">Edit</Text>
              </View>
            </Pressable>
            <Pressable
              onPress={() =>
                router.push(
                  `/admin/pos-invoice/view-payment/${singleInvoice?.id}`
                )
              }
              style={[
                styles.dropupMenuItem,
                { borderBottomWidth: 1, borderBottomColor: colors.lineBorder },
              ]}
            >
              <Text>
                <FontAwesome name="money" size={20} color="black" />
              </Text>
              <Text preset="h3_r">View Payment</Text>
            </Pressable>
            {singleInvoice?.status !== "paid" && (
              <TouchableOpacity
                onPress={() =>
                  router.push(
                    `/admin/pos-invoice/make-payment/${singleInvoice?.id}`
                  )
                }
                style={[
                  styles.dropupMenuItem,
                  {
                    borderBottomWidth: 1,
                    borderBottomColor: colors.lineBorder,
                  },
                ]}
              >
                <FontAwesome name="money" size={20} color="black" />
                <Text preset="h3_r">Make Payment</Text>
              </TouchableOpacity>
            )}

            <TouchableOpacity
              style={[
                styles.dropupMenuItem,
                { borderBottomWidth: 1, borderBottomColor: colors.lineBorder },
              ]}
              onPress={() => handleDownloadPDF(singleInvoice?.id)}
            >
              <AntDesign name="download" size={20} color="black" />
              <Text preset="h3_r">Download</Text>
            </TouchableOpacity>
            {singleInvoice?.delivery_status === "pending" &&
              singleInvoice?.status === "paid" && (
                <TouchableOpacity
                  style={[
                    styles.dropupMenuItem,
                    {
                      borderBottomWidth: 1,
                      borderBottomColor: colors.lineBorder,
                    },
                  ]}
                  onPress={() => handleStatusChange("delivered")}
                >
                  <MaterialCommunityIcons
                    name="truck-delivery"
                    size={20}
                    color="black"
                  />
                  <Text preset="h3_r">Make Delivered</Text>
                </TouchableOpacity>
              )}

            {singleInvoice?.delivery_status !== "canceled" && (
              <TouchableOpacity
                style={[
                  styles.dropupMenuItem,
                  {
                    borderBottomWidth: 1,
                    borderBottomColor: colors.lineBorder,
                  },
                ]}
                onPress={() => handleStatusChange("canceled")}
              >
                <MaterialCommunityIcons name="cancel" size={20} color="black" />
                <Text preset="h3_r">Make Cancel</Text>
              </TouchableOpacity>
            )}

            <Pressable
              onPress={() =>
                router.push(`/admin/pos-invoice/send/${singleInvoice?.id}`)
              }
              style={[
                styles.dropupMenuItem,
                { borderBottomWidth: 1, borderBottomColor: colors.lineBorder },
              ]}
            >
              <View style={{ flexDirection: "row", gap: 10 }}>
                <MaterialCommunityIcons
                  name="email-send-outline"
                  size={20}
                  color="black"
                />
                <Text preset="h3_r">Send</Text>
              </View>
            </Pressable>
            <Pressable
              onPress={() =>
                router.push(`/admin/pos-invoice/live-url/${singleInvoice?.id}`)
              }
              style={[
                styles.dropupMenuItem,
                { borderBottomWidth: 1, borderBottomColor: colors.lineBorder },
              ]}
            >
              <View style={{ flexDirection: "row", gap: 10 }}>
                <Fontisto name="world-o" size={16} color="black" />
                <Text preset="h3_r">Link</Text>
              </View>
            </Pressable>
            <TouchableOpacity
              onPress={handleDeleteInvoice}
              style={[styles.dropupMenuItem]}
            >
              <AntDesign name="delete" size={20} color="black" />
              <Text preset="h3_r">Delete</Text>
            </TouchableOpacity>
          </View>
        </ScrollView>
      </RBSheet>
      <Topbar title="Pos/Invoice manager" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <View style={styles.card}>
          <View style={styles.listCreateBtn}>
            <Text preset="h2">Invoice List</Text>
            <Link href="/admin/pos-invoice/create">
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
  middle: {
    width: "25%",
  },
  right: {
    width: "25%",
    alignItems: "flex-end",
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
  dropupMenuItem: {
    flexDirection: "row",
    alignItems: "center",
    gap: 10,
    paddingVertical: 14,
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
    width: "100%",
  },
});
