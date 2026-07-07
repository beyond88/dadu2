import React, { useEffect, useState } from "react";
import {
  ActivityIndicator,
  Alert,
  Animated,
  FlatList,
  Pressable,
  TouchableOpacity,
  View,
} from "react-native";
import Text from "../text/Text";
import { LinearGradient } from "expo-linear-gradient";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { colors } from "../../themes/colors";
import TableLoader from "../TableLoader/TableLoader";
import ErrorMessage from "../CustomMessage/ErrorMessage";
import NotFoundMessage from "../CustomMessage/NotFoundMessage";
import { StyleSheet } from "react-native";
import { AntDesign } from "@expo/vector-icons";
import {
  useDeleteExpenseMutation,
  useGetExpenseListQuery,
} from "../../redux/features/expense/expenseApi";
import { showMessage } from "react-native-flash-message";
import Loading from "../Loading/Loading";
import { Link, router } from "expo-router";

const ExpenseList = () => {
  const [page, setPage] = useState(1);
  const [expenseList, setExpenseList] = useState([]);

  //handle show navigation
  const handleShowNavigation = (id) => {
    router.push(`/admin/expense/${id}`);
  };
  //handle delete
  const [
    deleteExpense,
    {
      data: deleteData,
      isLoading: deleteIsLoading,
      isSuccess: deleteIsSuccess,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeleteExpenseMutation();
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
            deleteExpense(id);
          },
        },
      ],
      { cancelable: false }
    );
  };

  useEffect(() => {
    if (deleteIsSuccess) {
      setExpenseList([]);
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
        <TouchableOpacity onPress={() => handleShowNavigation(item?.id)}>
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={[styles.swipeBtn, !item?.is_received && styles.disabled]}
          >
            <AntDesign name="eyeo" size={20} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Show
            </Text>
          </LinearGradient>
        </TouchableOpacity>
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
        <View style={styles.itemWrap}>
          <View style={styles.item}>
            <View style={styles.left}>
              <View style={styles.pContent}>
                <Text
                  preset="h5_m"
                  style={{ marginBottom: 4, color: colors.black }}
                >
                  {item?.title}
                </Text>
                <Text
                  preset="h6"
                  style={{ color: colors.fontColor, marginBottom: 4 }}
                >
                  {item?.date}
                </Text>
                <Text
                  preset="h6"
                  style={{ color: colors.fontColor, marginBottom: 4 }}
                >
                  {item?.category_name}
                </Text>
                <Text
                  preset="h6"
                  style={{ color: colors.fontColor, marginBottom: 4 }}
                >
                  {item?.expenseBy}
                </Text>
                <Text>{item?.total}</Text>
              </View>
            </View>
          </View>
        </View>
      </Swipeable>
    </GestureHandlerRootView>
  );

  const {
    data: getExpenseList,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetExpenseListQuery(page);

  const { current_page, to, total } = getExpenseList?.data?.meta || {};

  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getExpenseList?.data?.data) &&
      page == current_page
    ) {
      setExpenseList((prevData) => [
        ...prevData,
        ...getExpenseList?.data?.data,
      ]);
    }
  }, [isSuccess, getExpenseList]);
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
  } else if (expenseList?.length === 0) {
    content = <NotFoundMessage message="Expense  Not Found" />;
  } else if (expenseList?.length > 0) {
    content = (
      <FlatList
        data={expenseList}
        renderItem={renderItem}
        keyExtractor={(item) => item.id}
        ListFooterComponent={renderLoader}
        onEndReached={fetchMoreData}
        onEndReachedThreshold={0}
      />
    );
  }
  return (
    <View>
      {deleteIsLoading && <Loading />}
      <View style={styles.card}>
        <Pressable onPress={() => router.push("/admin/expense/create")}>
          <View style={styles.createBtn}>
            <Text style={styles.createBtnTxt} preset="h5">
              Create
            </Text>
          </View>
        </Pressable>
        {content}
      </View>
    </View>
  );
};

export default ExpenseList;

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.white,
    borderRadius: 5,
    height: "100%",
  },
  itemWrap: {
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
    paddingBottom: 10,
  },
  item: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 10,
    paddingVertical: 12,
    paddingBottom: 0,
    flexWrap: "wrap",
  },
  left: {
    flexDirection: "row",
    alignItems: "center",
    width: "75%",
  },
  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 65,
    height: "100%",
  },
  createBtn: {
    backgroundColor: colors.green,
    textAlign: "center",
    paddingVertical: 10,
    paddingHorizontal: 10,
    color: colors.white,
    borderRadius: 5,
    width: "100%",
  },
  createBtnTxt: {
    color: colors.white,
    textAlign: "center",
  },
});
