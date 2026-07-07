import {
  StyleSheet,
  View,
  FlatList,
  Animated,
  ActivityIndicator,
  TouchableOpacity,
} from "react-native";

import Text from "../../../../components/text/Text";
import { colors } from "../../../../themes/colors";
import { LinearGradient } from "expo-linear-gradient";
import { useGetSaleReturnQuery } from "../../../../redux/features/sales-return/salesReturnApi";
import { generate8DigitNumber } from "../../../../utils/helper";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import { useSelector } from "react-redux";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";
import { useState } from "react";
import { useEffect } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { AntDesign } from "@expo/vector-icons";
import { useRouter } from "expo-router";

const SaleReturnList = () => {
  const [page, setPage] = useState(1);
  const [salesReturnList, setSalesReturnList] = useState([]);
  const router = useRouter();
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );

  //handle navigation

  const handleShowNavigation = (id) => {
    router.push(`/admin/sales/return-list/show/${id}`);
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
      </Animated.View>
    );
  };

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
              {item?.invoice?.customer?.full_name || "Walk-In Customer"}
            </Text>
            <Text preset="h6" style={{ color: colors.fontColor }}>
              <Text preset="h6_m" style={{ color: colors.themeColor }}>
                Invoice #{generate8DigitNumber(item?.invoice?.id || 0)}
              </Text>{" "}
              / Total Product-{item?.invoice?.items_data?.length}
            </Text>
            <Text preset="h6" style={{ color: colors.fontColor }}>
              date- {item?.return_date}
            </Text>
          </View>
          <View style={{ alignItems: "flex-end" }}>
            <Text
              preset="h5"
              style={{ marginBottom: 5, color: colors.themeColor }}
            >
              {currency_symbol} {item?.invoice?.total}
            </Text>
            <LinearGradient
              colors={["#37DBD9", "#008AA1"]}
              style={styles.paidBadge}
            >
              <Text preset="h6" style={{ color: colors.white }}>
                {item?.invoice?.status}
              </Text>
            </LinearGradient>
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
  } = useGetSaleReturnQuery(page);

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
      <Topbar title="Sales Return List" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default SaleReturnList;

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
