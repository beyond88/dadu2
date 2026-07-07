import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import {
  View,
  StyleSheet,
  FlatList,
  Pressable,
  Animated,
  TouchableOpacity,
  Alert,
} from "react-native";
import { Link, useRouter } from "expo-router";
import Text from "../../../../components/text/Text";
import { colors } from "../../../../themes/colors";
import TableLoader from "../../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../../components/CustomMessage/NotFoundMessage";
import RefreshBtn from "../../../../components/RefreshBtn/RefreshBtn";
import { Image } from "expo-image";
import {
  GestureHandlerRootView,
  Swipeable,
} from "react-native-gesture-handler";
import { LinearGradient } from "expo-linear-gradient";
import { AntDesign } from "@expo/vector-icons";
import { showMessage } from "react-native-flash-message";
import {
  useDeleteBrandMutation,
  useGetBrandsQuery,
} from "../../../../redux/features/brandApi/brandApi";
import Loading from "../../../../components/Loading/Loading";

const BrandList = () => {
  const [page, setPage] = useState(1);
  const [brandList, setBrandList] = useState([]);

  //router
  const router = useRouter();

  //delete category mutation
  const [
    deleteBrand,
    {
      data: deleteData,
      isSuccess: deleteIsSuccess,
      isLoading: deleteIsLoading,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeleteBrandMutation();
  //handle navigation
  const handleNavigation = (id) => {
    router.push(`/admin/product/brand/edit/${id}`);
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
            deleteBrand(id);
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
            <AntDesign name="edit" size={20} color="white" />
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
          rightSwipe(progress, dragX, item?.id)
        }
        key={item?.id}
      >
        <View style={styles.itemWrap}>
          <View style={styles.item}>
            <View style={styles.left}>
              <View style={styles.pImage}>
                <Image source={item?.file_url} style={styles.thumbImg} />
              </View>
              <View style={styles.pContent}>
                <Text
                  preset="h5_m"
                  style={{ marginBottom: 3, color: colors.black }}
                >
                  <Text preset="h5">Brand: </Text>
                  {item?.name}
                </Text>
                <Text preset="h6" style={{ color: colors.fontColor }}>
                  {item?.desc}
                </Text>
              </View>
            </View>
            <View style={{ alignItems: "flex-end" }}>
              {item?.status === "active" ? (
                <Text style={styles.activeBtn}>{item?.status}</Text>
              ) : (
                <Text style={[styles.activeBtn, styles.inActiveBtn]}>
                  {item?.status}
                </Text>
              )}
            </View>
          </View>
        </View>
      </Swipeable>
    </GestureHandlerRootView>
  );

  //get category list from api
  const {
    data: brandsData,
    isSuccess,
    isLoading,
    isError,
    error,
    refetch,
  } = useGetBrandsQuery();

  const { current_page, to, total } = brandsData?.data?.meta || {};

  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(brandsData?.data?.data) &&
      page == current_page
    ) {
      setBrandList((prevData) => [...prevData, ...brandsData?.data?.data]);
    }
  }, [isSuccess, brandsData]);

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
      setBrandList([]);
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
  } else if (brandsData?.data?.data?.length === 0) {
    content = <NotFoundMessage message="Category Not Found" />;
  } else if (brandList?.length > 0) {
    content = (
      <FlatList
        data={brandList}
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
      <Topbar title="Brands List" />
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
            onPress={() => router.push("/admin/product/brand/create")}
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

export default BrandList;

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
