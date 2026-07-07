import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  View,
  StyleSheet,
  FlatList,
  Animated,
  TouchableOpacity,
  Alert,
  ActivityIndicator,
  Pressable,
} from "react-native";
import { Link, useRouter } from "expo-router";
import Text from "../../../../components/text/Text";
import { colors } from "../../../../themes/colors";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { LinearGradient } from "expo-linear-gradient";
import { AntDesign } from "@expo/vector-icons";
import { showMessage } from "react-native-flash-message";
import {
  useDeleteMeasurementUnitMutation,
  useGetMeasurementUnitsQuery,
} from "../../../../redux/features/catalog/catalogApi";
import Loading from "../../../../components/Loading/Loading";

const MeasurementList = () => {
  const [page, setPage] = useState(1);
  const [measurementUnitList, setMeasurementUnitList] = useState([]);

  //router
  const router = useRouter();

  //delete category mutation
  const [
    deleteMeasurementUnit,
    {
      data: deleteData,
      isLoading: deleteIsLoading,
      isSuccess: deleteIsSuccess,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeleteMeasurementUnitMutation();
  //handle navigation
  const handleNavigation = (id) => {
    router.push(`/admin/catalog/measurement-unit/edit/${id}`);
  };
  //handle delete
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
            deleteMeasurementUnit(id);
          },
        },
      ],
      { cancelable: false }
    );
  };
  //Swipeable right swipe

  const rightSwipe = (progress, dragX, id) => {
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
        <TouchableOpacity onPress={() => handleNavigation(id)}>
          <LinearGradient
            colors={["#37DBD9", "#008AA1"]}
            style={styles.swipeBtn}
          >
            <AntDesign name="edit" size={16} color="white" />
            <Text preset="h6" style={{ color: colors.white, marginTop: 3 }}>
              Edit
            </Text>
          </LinearGradient>
        </TouchableOpacity>
        <TouchableOpacity onPress={() => handleDelete(id)}>
          <LinearGradient
            colors={["#FDCC95", "#FF9138"]}
            style={styles.swipeBtn}
          >
            <AntDesign name="delete" size={16} color="white" />
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
          rightSwipe(progress, dragX, item?.id)
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
                  <Text preset="h5">Measurement unit: </Text>
                  {item?.name}
                </Text>
              </View>
            </View>
          </View>
        </View>
      </Swipeable>
    </GestureHandlerRootView>
  );

  //get category list from api
  const {
    data: measurementUnitData,
    isSuccess,
    isLoading,
    isError,
    error,
  } = useGetMeasurementUnitsQuery();

  const { current_page, to, total } = measurementUnitData?.data?.meta || {};

  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(measurementUnitData?.data?.data) &&
      page == current_page
    ) {
      setMeasurementUnitList((prevData) => [
        ...prevData,
        ...measurementUnitData?.data?.data,
      ]);
    }
  }, [isSuccess, measurementUnitData]);

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

  //delete error success message

  useEffect(() => {
    if (deleteIsSuccess) {
      setMeasurementUnitList([]);
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

  //render content
  let content = null;
  if (isLoading) {
    content = <TableLoader />;
  } else if (isError) {
    content = <ErrorMessage message={error?.data?.message} />;
  } else if (measurementUnitData?.data?.data?.length === 0) {
    content = <NotFoundMessage message="Measurement Unit Not Found" />;
  } else if (measurementUnitList?.length > 0) {
    content = (
      <FlatList
        data={measurementUnitList}
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
      <Topbar title="Measurement Unit List" />
      <View style={{ marginHorizontal: 20, marginBottom: 180 }}>
        <View
          style={{
            justifyContent: "space-between",
            flexDirection: "row",
            marginBottom: 15,
          }}
        >
          <Pressable
            style={[styles.createBtn, { flex: 1 }]}
            onPress={() =>
              router.push("/admin/catalog/measurement-unit/create")
            }
          >
            <Text style={styles.createBtnTxt} preset="h5">
              Create
            </Text>
          </Pressable>
        </View>

        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default MeasurementList;

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
    width: "78%",
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
  inActiveBtn: {
    backgroundColor: colors.red,
  },

  createBtn: {
    backgroundColor: colors.green,
    textAlign: "center",
    paddingVertical: 10,
    paddingHorizontal: 20,
    color: colors.white,
    borderRadius: 5,
  },
  createBtnTxt: {
    color: colors.white,
    textAlign: "center",
  },
  swipeBtn: {
    alignItems: "center",
    justifyContent: "center",
    width: 65,
    height: "100%",
  },
});
