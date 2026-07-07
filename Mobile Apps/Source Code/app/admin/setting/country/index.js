import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  useDeleteCountryMutation,
  useGetCountriesListQuery,
} from "../../../../redux/features/setting/countryApi";
import { showMessage } from "react-native-flash-message";
import {
  ActivityIndicator,
  Alert,
  Animated,
  Dimensions,
  FlatList,
  Pressable,
  StyleSheet,
  TouchableOpacity,
  View,
} from "react-native";
import { LinearGradient } from "expo-linear-gradient";
import Text from "../../../../components/text/Text";
import { AntDesign } from "@expo/vector-icons";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { colors } from "../../../../themes/colors";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";
import Loading from "../../../../components/Loading/Loading";
import { Link, router } from "expo-router";

const CountryList = () => {
  const [page, setPage] = useState(1);
  const [countryList, setCountryList] = useState([]);

  const height = Dimensions.get("window").height - 250;

  //handle edit navigation
  const handleEditNavigation = (id) => {
    router.push(`/admin/setting/country/edit/${id}`);
  };
  //handle delete
  const [
    deleteCountry,
    {
      data: deleteData,
      isLoading: deleteIsLoading,
      isSuccess: deleteIsSuccess,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeleteCountryMutation();
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
            deleteCountry(id);
          },
        },
      ],
      { cancelable: false }
    );
  };

  useEffect(() => {
    if (deleteIsSuccess) {
      setCountryList([]);
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

  //Swipeable right swipe

  const rightSwipe = (progress, dragX, item) => {
    const scale = dragX.interpolate({
      inputRange: [-100, 0],
      outputRange: [0.9, 0],
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
        <TouchableOpacity onPress={() => handleEditNavigation(item?.id)}>
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={[styles.swipeBtn, !item?.is_received && styles.disabled]}
          >
            <AntDesign name="edit" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Edit
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
  // render item
  const renderItem = ({ item }) => (
    <GestureHandlerRootView>
      <Swipeable
        renderRightActions={(progress, dragX) =>
          rightSwipe(progress, dragX, item)
        }
        key={item?.id}
      >
        <View style={[styles.tableRow, styles.tableBody]}>
          <View style={[styles.tableCol1, styles.tableBodyCol]}>
            <Text preset="h6_m" style={{ color: colors.black }}>
              {item?.id}
            </Text>
          </View>
          <View style={[styles.tableCol2, styles.tableBodyCol]}>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              {item?.name}
            </Text>
          </View>
          <View style={[styles.tableCol3, styles.tableBodyCol]}>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              {item?.shortname}
            </Text>
          </View>
          <View style={styles.tableCol4}>
            <Text preset="h6" style={{ color: colors.pcolor }}>
              {item?.phonecode}
            </Text>
          </View>
        </View>
      </Swipeable>
    </GestureHandlerRootView>
  );
  //get product list
  const {
    data: getCountryList,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetCountriesListQuery(page);

  const { current_page, to, total } = getCountryList?.data?.meta || {};
  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getCountryList?.data?.data) &&
      page == current_page
    ) {
      setCountryList((prevData) => [
        ...prevData,
        ...getCountryList?.data?.data,
      ]);
    }
  }, [isSuccess, getCountryList]);
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
  //render content
  let content = null;
  if (isLoading) {
    content = <TableLoader />;
  } else if (isError) {
    content = <ErrorMessage message={error?.data?.message} />;
  } else if (countryList?.length === 0) {
    content = <NotFoundMessage message="Country Not Found" />;
  } else if (countryList?.length > 0) {
    content = (
      <FlatList
        data={countryList}
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
      <Topbar title="Country List" />
      <View style={{ paddingHorizontal: 20 }}>
        <Pressable onPress={() => router.push("/admin/setting/country/create")}>
          <View style={styles.createBtn}>
            <Text style={styles.createBtnTxt} preset="h5">
              Create
            </Text>
          </View>
        </Pressable>
        <View style={styles.tableWrap}>
          <View style={[styles.tableRow, styles.tableHeader]}>
            <View style={styles.tableCol1}>
              <Text preset="h5_m" style={{ color: colors.white }}>
                SL
              </Text>
            </View>
            <View style={styles.tableCol2}>
              <Text preset="h5_m" style={{ color: colors.white }}>
                Name
              </Text>
            </View>
            <View style={styles.tableCol3}>
              <Text preset="h5_m" style={{ color: colors.white }}>
                Shortname
              </Text>
            </View>
            <View style={styles.tableCol4}>
              <Text preset="h5_m" style={{ color: colors.white }}>
                phonecode
              </Text>
            </View>
          </View>
          <View style={{ height: height }}>{content}</View>
        </View>
      </View>
    </>
  );
};

export default CountryList;

const styles = StyleSheet.create({
  createBtn: {
    backgroundColor: colors.green,
    textAlign: "center",
    paddingVertical: 10,
    paddingHorizontal: 10,
    color: colors.white,
    borderRadius: 5,
    width: "100%",
    marginBottom: 15,
  },
  createBtnTxt: {
    color: colors.white,
    textAlign: "center",
  },
  borderNone: {
    borderBottomWidth: 0,
  },

  tableWrap: {
    backgroundColor: colors.white,
    borderBottomRightRadius: 5,
    borderBottomLeftRadius: 5,
    borderWidth: 1,
    borderColor: "#E9ECF2",
  },
  tableRow: {
    flexDirection: "row",
  },
  tableHeader: {
    backgroundColor: colors.themeColor,
    borderTopRightRadius: 5,
    borderTopLeftRadius: 5,
    paddingVertical: 12,
  },
  tableBody: {
    borderBottomWidth: 1,
    borderBottomColor: "#E9ECF2",
  },
  tableBodyCol: {
    borderRightWidth: 1,
    borderRightColor: "#E9ECF2",
    paddingVertical: 16,
    paddingHorizontal: 16,
  },
  tableCol1: {
    width: "30%",
    paddingLeft: 16,
  },
  tableCol2: {
    width: "25%",
    textAlign: "center",
    alignItems: "center",
    justifyContent: "center",
  },
  tableCol3: {
    width: "22%",
    textAlign: "center",
    alignItems: "center",
    justifyContent: "center",
  },
  tableCol4: {
    width: "22%",
    textAlign: "center",
    alignItems: "center",
    justifyContent: "center",
  },
  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 55,
    height: "100%",
    paddingVertical: 12,
  },
});
