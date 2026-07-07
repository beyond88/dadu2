import {
  Alert,
  Pressable,
  StyleSheet,
  TouchableOpacity,
  View,
  ActivityIndicator,
  FlatList,
} from "react-native";
import Topbar from "../../../components/Topbar/Topbar";
import { colors } from "../../../themes/colors";
import {
  useDeleteDraftInvoiceMutation,
  useGetDraftInvoiceQuery,
} from "../../../redux/features/pos-invoice/posInvoiceApi";
import { generate8DigitNumber } from "../../../utils/helper";
import { useSelector } from "react-redux";
import { LinearGradient } from "expo-linear-gradient";
import Text from "../../../components/text/Text";
import TableLoader from "../../../components/TableLoader/TableLoader";
import RBSheet from "react-native-raw-bottom-sheet";
import { useEffect, useRef } from "react";
import Svg, { Path } from "react-native-svg";
import { useState } from "react";
import { Link } from "expo-router";
import { showMessage } from "react-native-flash-message";
import ErrorMessage from "../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../components/CustomMessage/NotFoundMessage";
import Loading from "../../../components/Loading/Loading";
const DraftInvoice = () => {
  const [invoiceId, setInvoiceId] = useState(null);
  const refRBSheet = useRef();
  const [page, setPage] = useState(1);
  const [draftInvoiceData, setDraftInvoiceData] = useState([]);

  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  //delete invoice
  const [
    deleteDraftInvoice,
    {
      data: deleteDraftData,
      isLoading: deleteIsLoading,
      isSuccess: deleteIsSuccess,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeleteDraftInvoiceMutation();
  const handleInvoiceDelete = () => {
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
            deleteDraftInvoice(invoiceId);
            refRBSheet.current.close();
          },
        },
      ],
      { cancelable: false }
    );
  };
  //set invoice id
  const handleInvoiceId = (id) => {
    setInvoiceId(id);

    refRBSheet.current.open();
  };

  //render Item
  const renderItem = ({ item }) => (
    <Pressable
      style={styles.item}
      key={item?.id}
      onPress={() => handleInvoiceId(item?.id)}
    >
      <View style={styles.left}>
        <Text preset="h5_m" style={{ marginBottom: 3, color: colors.black }}>
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
        <Text preset="h5" style={{ marginBottom: 5, color: colors.themeColor }}>
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
    </Pressable>
  );
  //get draft invoice
  const {
    data: getDraftInvoice,
    isLoading,
    error,
    isError,
    refetch,
    isSuccess,
  } = useGetDraftInvoiceQuery(page);

  const { current_page, to, total } = getDraftInvoice?.data?.meta || {};

  // Update invoiceData when getInvoiceData changes
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getDraftInvoice?.data?.data) &&
      page == current_page
    ) {
      setDraftInvoiceData((prevData) => [
        ...prevData,
        ...getDraftInvoice?.data?.data,
      ]);
    }
  }, [isSuccess, getDraftInvoice]);

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
  } else if (getDraftInvoice?.data?.data?.length === 0) {
    content = <NotFoundMessage message="Draft Invoice Not Found" />;
  } else if (draftInvoiceData.length > 0) {
    content = (
      <FlatList
        data={draftInvoiceData}
        renderItem={renderItem}
        keyExtractor={(item) => item.id}
        ListFooterComponent={renderLoader}
        onEndReached={fetchMoreData}
        onEndReachedThreshold={0}
      />
    );
  }

  //success & error message
  useEffect(() => {
    if (deleteIsSuccess) {
      showMessage({
        message: `${deleteDraftData?.message}`,
        type: "success",
      });
      setDraftInvoiceData([]);
      setPage(1);
    } else if (deleteIsError) {
      showMessage({
        message: `${deleteError?.data?.message}`,
        type: "danger",
      });
    }
  }, [deleteIsSuccess, deleteIsError, deleteError, deleteDraftData]);
  return (
    <>
      {deleteIsLoading && <Loading />}
      <Topbar title="Draft Invoice" customer={true} />
      {/* bottom popup */}
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
        <View style={{ paddingHorizontal: 20, paddingTop: 15 }}>
          <Link
            href={`/customer/draft-invoice/edit/${invoiceId}`}
            style={{
              borderBottomColor: colors.lineBorder,
              borderBottomWidth: 1,
            }}
          >
            <View style={styles.dropupMenuItem}>
              <Svg
                xmlns="http://www.w3.org/2000/svg"
                width="18"
                height="19"
                viewBox="0 0 18 19"
                fill="none"
              >
                <Path
                  d="M11.1771 15.3231L9.00017 17.5L6.82324 15.3231"
                  stroke="#38A4F8"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <Path
                  d="M9 11.9615V17.5"
                  stroke="#38A4F8"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <Path
                  d="M6.82324 3.67692L9.00017 1.5L11.1771 3.67692"
                  stroke="#38A4F8"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <Path
                  d="M9 7.03846V1.5"
                  stroke="#38A4F8"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <Path
                  d="M3.17692 11.6769L1 9.5L3.17692 7.32307"
                  stroke="#38A4F8"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <Path
                  d="M6.53846 9.5H1"
                  stroke="#38A4F8"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <Path
                  d="M14.8232 7.32307L17.0002 9.5L14.8232 11.6769"
                  stroke="#38A4F8"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <Path
                  d="M11.4614 9.5H16.9999"
                  stroke="#38A4F8"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
              </Svg>
              <Text preset="h3_r">Move to Invoice</Text>
            </View>
          </Link>
          <Link
            href={`/customer/draft-invoice/${invoiceId}`}
            style={{
              borderBottomColor: colors.lineBorder,
              borderBottomWidth: 1,
            }}
          >
            <View style={styles.dropupMenuItem}>
              <Svg
                xmlns="http://www.w3.org/2000/svg"
                width="18"
                height="13"
                viewBox="0 0 18 13"
                fill="none"
              >
                <Path
                  d="M9 1.35721C3.28571 1.35721 1 6.50007 1 6.50007C1 6.50007 3.28571 11.6429 9 11.6429C14.7143 11.6429 17 6.50007 17 6.50007C17 6.50007 14.7143 1.35721 9 1.35721Z"
                  stroke="#38A4F8"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
                <Path
                  d="M9.00021 9.3572C10.5782 9.3572 11.8574 8.07801 11.8574 6.50006C11.8574 4.9221 10.5782 3.64291 9.00021 3.64291C7.42225 3.64291 6.14307 4.9221 6.14307 6.50006C6.14307 8.07801 7.42225 9.3572 9.00021 9.3572Z"
                  stroke="#38A4F8"
                  strokeWidth="1.5"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                />
              </Svg>
              <Text preset="h3_r">Show Details</Text>
            </View>
          </Link>

          <TouchableOpacity
            style={[
              styles.dropupMenuItem,
              { borderBottomWidth: 1, borderBottomColor: colors.lineBorder },
            ]}
            onPress={handleInvoiceDelete}
          >
            <Svg
              xmlns="http://www.w3.org/2000/svg"
              width="17"
              height="19"
              viewBox="0 0 17 19"
              fill="none"
            >
              <Path
                d="M15.6667 4.16681H1"
                stroke="#EC4561"
                strokeWidth="1.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
              <Path
                d="M6.3335 8.16681V13.5001"
                stroke="#EC4561"
                strokeWidth="1.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
              <Path
                d="M10.3335 8.16681V13.5001"
                stroke="#EC4561"
                strokeWidth="1.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
              <Path
                d="M14.3335 4.16681V16.8335C14.3335 17.0103 14.2633 17.1799 14.1382 17.3049C14.0132 17.4299 13.8436 17.5001 13.6668 17.5001H3.00016C2.82335 17.5001 2.65378 17.4299 2.52876 17.3049C2.40373 17.1799 2.3335 17.0103 2.3335 16.8335V4.16681"
                stroke="#EC4561"
                strokeWidth="1.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
              <Path
                d="M11.6667 4.16682V2.83349C11.6667 2.47986 11.5262 2.14073 11.2761 1.89068C11.0261 1.64063 10.687 1.50015 10.3333 1.50015H6.33333C5.97971 1.50015 5.64057 1.64063 5.39052 1.89068C5.14048 2.14073 5 2.47986 5 2.83349V4.16682"
                stroke="#EC4561"
                strokeWidth="1.5"
                strokeLinecap="round"
                strokeLinejoin="round"
              />
            </Svg>
            <Text preset="h3_r">Delete Invoice</Text>
          </TouchableOpacity>
        </View>
      </RBSheet>

      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default DraftInvoice;

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
