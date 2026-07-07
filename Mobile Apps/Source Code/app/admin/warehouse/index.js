import {
  View,
  StyleSheet,
  FlatList,
  Pressable,
  Alert,
  ActivityIndicator,
  Dimensions,
} from "react-native";
import Text from "../../../components/text/Text";
import Topbar from "../../../components/Topbar/Topbar";
import { colors } from "../../../themes/colors";
import { useEffect, useState } from "react";
import TableLoader from "../../../components/TableLoader/TableLoader";
import ErrorMessage from "../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../components/CustomMessage/NotFoundMessage";
import {
  useDeleteAdminWarehouseMutation,
  useGetAdminWarehouseQuery,
} from "../../../redux/features/warehouse/warehouseApi";
import { Link, router } from "expo-router";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../components/Loading/Loading";

const Warehouse = () => {
  const [page, setPage] = useState(1);
  const [warehouseList, setWarehouseList] = useState([]);
  const [warehouseId, setWarehouseId] = useState(null);

  const windowHeight = Dimensions.get("window").height - 180;
  //delete Warehouse
  const [
    deleteAdminWarehouse,
    {
      data: deleteWarehouseData,
      isLoading: deleteIsLoading,
      isSuccess: deleteIsSuccess,
      isError: deleteIsError,
      error: deleteError,
    },
  ] = useDeleteAdminWarehouseMutation();
  const handleWarehouseDelete = (id) => {
    setWarehouseId(id);

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
            deleteAdminWarehouse(id);
          },
        },
      ],
      { cancelable: false }
    );
  };

  //render
  const renderItem = ({ item }) => (
    <View style={styles.tableCardWrap}>
      <View style={styles.tableCardItem}>
        <Text preset="h5" style={styles.itemLeft}>
          Warehouse Name
        </Text>
        <Text style={styles.itemRight}>{item?.name}</Text>
      </View>
      <View style={styles.tableCardItem}>
        <Text preset="h5" style={styles.itemLeft}>
          Email
        </Text>
        <Text style={styles.itemRight}>{item?.email}</Text>
      </View>
      <View style={styles.tableCardItem}>
        <Text preset="h5" style={styles.itemLeft}>
          Phone
        </Text>
        <Text style={styles.itemRight}>{item?.phone}</Text>
      </View>
      <View style={styles.tableCardItem}>
        <Text preset="h5" style={styles.itemLeft}>
          Company Name
        </Text>
        <Text style={styles.itemRight}>{item?.company_name}</Text>
      </View>
      <View style={styles.tableCardItem}>
        <Text preset="h5" style={styles.itemLeft}>
          Address 1
        </Text>
        <Text style={styles.itemRight}>{item?.address_1}</Text>
      </View>
      <View style={styles.tableCardItem}>
        <Text preset="h5" style={styles.itemLeft}>
          Address 2
        </Text>
        <Text style={styles.itemRight}>{item?.address_2}</Text>
      </View>
      <View style={styles.tableCardItem}>
        <Text preset="h5" style={styles.itemLeft}>
          priority
        </Text>
        <Text style={styles.itemRight}>{item?.priority}</Text>
      </View>
      <View style={styles.tableCardItem}>
        <Text preset="h5" style={styles.itemLeft}>
          Status
        </Text>
        <View style={styles.itemRight}>
          {item?.status == "active" ? (
            <>
              <Text style={styles.activeBtn}>{item?.status}</Text>
              <Text style={{ textAlign: "right", marginTop: 5 }}>
                {item?.is_default === "1" && "Default"}
              </Text>
            </>
          ) : (
            <>
              <Text style={[styles.activeBtn, styles.inactiveBtn]}>
                {item?.status}
              </Text>
              <Text style={{ textAlign: "right", marginTop: 5 }}>
                {item?.is_default === "1" && "Default"}
              </Text>
            </>
          )}
        </View>
      </View>
      <View style={styles.tableCardItem}>
        <Text preset="h5" style={styles.itemLeft}>
          Action
        </Text>
        <View style={[styles.itemRight, styles.actionBtnWrap]}>
          <Link href={`/admin/warehouse/${item?.id}`} asChild>
            <Pressable style={styles.actionBtn}>
              <Text style={{ color: "#fff", fontSize: 12 }}>Show</Text>
            </Pressable>
          </Link>
          <Link href={`/admin/warehouse/edit/${item?.id}`} asChild>
            <Pressable style={styles.actionBtn}>
              <Text style={{ color: "#fff", fontSize: 12 }}>Edit</Text>
            </Pressable>
          </Link>
          <Pressable
            style={[styles.actionBtn, styles.deleteBtn]}
            onPress={() => handleWarehouseDelete(item?.id)}
          >
            <Text style={{ color: "#fff", fontSize: 12 }}>Delete</Text>
          </Pressable>
        </View>
      </View>
    </View>
  );

  //get Warehouse list
  const {
    data: getWarehouseList,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetAdminWarehouseQuery(page);

  const { current_page, to, total } = getWarehouseList?.data?.meta || {};

  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getWarehouseList?.data?.data) &&
      page == current_page
    ) {
      setWarehouseList((prevData) => [
        ...prevData,
        ...getWarehouseList?.data?.data,
      ]);
    }
  }, [isSuccess, getWarehouseList]);

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
  } else if (getWarehouseList?.data?.data?.length === 0) {
    content = <NotFoundMessage message="Product Not Found" />;
  } else if (warehouseList?.length > 0) {
    content = (
      <FlatList
        data={warehouseList}
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
        message: `${deleteWarehouseData?.message}`,
        type: "success",
      });
      const newList = warehouseList.filter((item) => item.id !== warehouseId);
      setWarehouseList(newList);
      setWarehouseId(null);
    } else if (deleteIsError) {
      showMessage({
        message: `${deleteError?.data?.message}`,
        type: "danger",
      });
    }
  }, [deleteIsSuccess, deleteIsError, deleteError, deleteWarehouseData]);
  return (
    <>
      {deleteIsLoading && <Loading />}
      <Topbar title="Warehouse List" />
      <View style={{ marginHorizontal: 20, height: windowHeight }}>
        <View>
          <Pressable onPress={() => router.push("/admin/warehouse/create")}>
            <View style={styles.createBtn}>
              <Text style={styles.createBtnTxt} preset="h5">
                Create
              </Text>
            </View>
          </Pressable>
        </View>
        <View>{content}</View>
      </View>
    </>
  );
};

export default Warehouse;

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.white,
    borderRadius: 5,
    height: "100%",
  },
  tableCardWrap: {
    backgroundColor: "#fff",
    borderColor: "#E9ECF2",
    borderWidth: 1,
    marginTop: 20,
  },
  tableCardItem: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderBottomColor: "#E9ECF2",
    borderBottomWidth: 1,
    alignItems: "center",
  },
  itemLeft: {
    width: "32%",
  },
  itemRight: {
    flex: 1,
    textAlign: "right",
  },
  activeBtn: {
    backgroundColor: colors.green,
    paddingHorizontal: 8,
    color: colors.white,
    borderRadius: 2,
    fontSize: 10,
    paddingVertical: 3,
    textTransform: "uppercase",
    width: 65,
    textAlign: "center",
    marginLeft: "auto",
  },
  inactiveBtn: {
    backgroundColor: colors.red,
  },
  actionBtnWrap: {
    flexDirection: "row",
    gap: 5,
    justifyContent: "flex-end",
  },
  actionBtn: {
    backgroundColor: colors.green,
    fontSize: 12,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 4,
    color: colors.white,
  },
  deleteBtn: {
    backgroundColor: colors.red,
  },
  createBtn: {
    backgroundColor: colors.green,
    textAlign: "center",
    paddingVertical: 10,
    paddingHorizontal: 10,
    color: colors.white,
    borderRadius: 5,
  },
  createBtnTxt: {
    color: colors.white,
    textAlign: "center",
  },
});
