import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  useDeleteExpenseCategoryMutation,
  useGetExpensesCategoriesQuery,
} from "../../../../redux/features/expense/expenseApi";
import { colors } from "../../../../themes/colors";
import {
  ActivityIndicator,
  Alert,
  Animated,
  FlatList,
  Pressable,
  StyleSheet,
  TouchableOpacity,
  View,
} from "react-native";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";
import Text from "../../../../components/text/Text";
import { Link, router } from "expo-router";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { LinearGradient } from "expo-linear-gradient";
import { AntDesign } from "@expo/vector-icons";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";

const ExpenseCategory = () => {
  const [page, setPage] = useState(1);
  const [expenseCategoryList, setExpenseCategoryList] = useState([]);

  //handle edit navigation
  const handleEditNavigation = (id) => {
    router.push(`/admin/expense/category/edit/${id}`);
  };
  //handle delete
  const [
    deleteExpenseCategory,
    {
      data: deleteData,
      isLoading: deleteIsLoading,
      isSuccess: deleteIsSuccess,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeleteExpenseCategoryMutation();
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
            deleteExpenseCategory(id);
          },
        },
      ],
      { cancelable: false }
    );
  };

  useEffect(() => {
    if (deleteIsSuccess) {
      setExpenseCategoryList([]);
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
        <View style={styles.itemWrap}>
          <View style={styles.item}>
            <View style={styles.left}>
              <View style={styles.pContent}>
                <Text
                  preset="h5_m"
                  style={{ marginBottom: 3, color: colors.black }}
                >
                  {item?.name}
                </Text>
                <Text preset="h6" style={{ color: colors.fontColor }}>
                  {item?.description}
                </Text>
              </View>
            </View>
            <View style={{ alignItems: "flex-end" }}>
              <Text
                style={[
                  styles.activeBtn,
                  item?.status === "active"
                    ? { backgroundColor: colors.themeColor }
                    : { backgroundColor: colors.red },
                ]}
              >
                {item?.status}
              </Text>
            </View>
          </View>
        </View>
      </Swipeable>
    </GestureHandlerRootView>
  );
  //get product list
  const {
    data: getExpenseCategoryList,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetExpensesCategoriesQuery(page);

  const { current_page, to, total } = getExpenseCategoryList?.data?.meta || {};

  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getExpenseCategoryList?.data?.data) &&
      page == current_page
    ) {
      setExpenseCategoryList((prevData) => [
        ...prevData,
        ...getExpenseCategoryList?.data?.data,
      ]);
    }
  }, [isSuccess, getExpenseCategoryList]);

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
  } else if (getExpenseCategoryList?.length === 0) {
    content = <NotFoundMessage message="Expense category Not Found" />;
  } else if (expenseCategoryList?.length > 0) {
    content = (
      <FlatList
        data={expenseCategoryList}
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
      <Topbar title="Expense Category List" />
      <View style={{ marginHorizontal: 20, marginBottom: 180 }}>
        <View>
          <Pressable
            onPress={() => router.push("/admin/expense/category/create")}
            style={{
              backgroundColor: colors.themeColor,
              alignItems: "center",
              padding: 10,
              borderRadius: 5,
            }}
          >
            <Text preset="h5" style={{ color: colors.white }}>
              Create Expense Category
            </Text>
          </Pressable>
        </View>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default ExpenseCategory;

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
  pImage: {
    width: 50,
  },
  thumbImg: {
    width: 50,
    height: 50,
    borderRadius: 5,
  },
  pContent: {
    marginLeft: 12,
    flex: 1,
  },
  activeBtn: {
    backgroundColor: colors.green,
    paddingHorizontal: 8,
    color: colors.white,
    borderRadius: 2,
    fontSize: 10,
    paddingVertical: 3,
    textTransform: "uppercase",
  },
  tableActionBtn: {
    flexDirection: "row",
    paddingHorizontal: 10,
    gap: 5,
    justifyContent: "space-between",
  },
  actionBtn: {
    backgroundColor: colors.themeColor,
    paddingHorizontal: 6,
    paddingVertical: 5,
    borderRadius: 2,
  },
  btnText: {
    color: colors.white,
    fontSize: 10,
  },
  createBtn: {
    backgroundColor: colors.green,
    textAlign: "center",
    paddingVertical: 10,
    paddingHorizontal: 10,
    color: colors.white,
    borderRadius: 5,
    width: "100%",
    flex: 1,
  },
  createBtnTxt: {
    color: colors.white,
    textAlign: "center",
  },
  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 55,
    height: "100%",
  },
});
