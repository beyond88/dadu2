import {
  View,
  StyleSheet,
  FlatList,
  ActivityIndicator,
  Pressable,
  Platform,
} from "react-native";
import Text from "../../../components/text/Text";
import Topbar from "../../../components/Topbar/Topbar";
import { colors } from "../../../themes/colors";
import { useGetProductsQuery } from "../../../redux/features/product/productApi";
import { Image } from "expo-image";
import TableLoader from "../../../components/TableLoader/TableLoader";
import { useSelector } from "react-redux";
import ErrorMessage from "../../../components/CustomMessage/ErrorMessage";
import NotFoundMessage from "../../../components/CustomMessage/NotFoundMessage";
import { useEffect, useState } from "react";
import { Link, router } from "expo-router";
import { Entypo } from "@expo/vector-icons";
import * as FileSystem from "expo-file-system";

const Product = () => {
  const [page, setPage] = useState(1);
  const [productList, setProductList] = useState([]);
  const [action, setAction] = useState(null);
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  //api url
  const apiUrl = process.env.EXPO_PUBLIC_API_URL;
  //Handle action
  const handleAction = (id) => {
    if (action == id) {
      setAction(null);
    } else {
      setAction(id);
    }
  };

  //Handle download barcode
  const handleDownloadBarcode = async (id) => {
    const filename = "barcode";
    const result = await FileSystem.downloadAsync(
      `${apiUrl}/admin/products/${id}/barcode-download`,
      FileSystem.documentDirectory + filename
    );
    save(result.uri, filename, result.headers["Content-Type"]);
  };

  //Handle download all barcode
  const handleDownloadAllBarcode = async () => {
    const filename = "barcode";
    const result = await FileSystem.downloadAsync(
      `${apiUrl}/admin/products/barcodes-download`,
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

  // render item
  const renderItem = ({ item }) => (
    <View style={styles.itemWrap}>
      <View style={styles.item}>
        <View style={styles.left}>
          <View style={styles.pImage}>
            <Image source={item?.image} style={styles.thumbImg} />
          </View>
          <View style={styles.pContent}>
            <Text
              preset="h5_m"
              style={{ marginBottom: 3, color: colors.black }}
            >
              {item?.name}
            </Text>
            <Text preset="h6" style={{ color: colors.fontColor }}>
              {item?.sku} / {item?.category?.name}{" "}
            </Text>
            <Text preset="h6_m" style={{ color: colors.black }}>
              Stock-{item?.stock} /{" "}
              <Text style={{ color: colors.themeColor }}>
                Variant-{item?.is_variant}
              </Text>
            </Text>
          </View>
        </View>
        <View style={{ alignItems: "flex-end" }}>
          <Text
            preset="h5"
            style={{ marginBottom: 5, color: colors.themeColor }}
          >
            {currency_symbol}
            {item?.price}
          </Text>
          <Text style={styles.activeBtn}>{item?.status}</Text>
          <Pressable
            style={{ width: "100%", alignItems: "center" }}
            onPress={() => handleAction(item?.id)}
          >
            <Entypo name="dots-three-horizontal" size={24} color="black" />
          </Pressable>
        </View>
      </View>
      {action == item?.id && (
        <View style={styles.tableActionBtn}>
          <Pressable
            style={styles.actionBtn}
            onPress={() => router.push(`/admin/product/${item?.id}`)}
          >
            <Text style={styles.btnText}>Show</Text>
          </Pressable>

          <Pressable
            onPress={() => router.push(`/admin/product/edit/${item?.id}`)}
            style={styles.actionBtn}
          >
            <Text style={styles.btnText}>Edit</Text>
          </Pressable>

          <Pressable
            style={styles.actionBtn}
            onPress={() =>
              router.push(`/admin/product/stock-price/${item?.id}`)
            }
          >
            <Text style={styles.btnText}>Stock</Text>
          </Pressable>

          <Pressable
            style={styles.actionBtn}
            onPress={() =>
              router.push(`/admin/product/stock-update/${item?.id}`)
            }
          >
            <Text style={styles.btnText}>Update Stock</Text>
          </Pressable>

          <Pressable
            style={styles.actionBtn}
            onPress={() => handleDownloadBarcode(item?.id)}
          >
            <Text style={styles.btnText}>Download Barcode</Text>
          </Pressable>
        </View>
      )}
    </View>
  );
  //get product list
  const {
    data: getProductList,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetProductsQuery(page);

  const { current_page, to, total } = getProductList?.data?.meta || {};

  // page & data set
  useEffect(() => {
    if (
      isSuccess &&
      Array.isArray(getProductList?.data?.data) &&
      page == current_page
    ) {
      setProductList((prevData) => [
        ...prevData,
        ...getProductList?.data?.data,
      ]);
    }
  }, [isSuccess, getProductList]);

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
  } else if (getProductList?.length === 0) {
    content = <NotFoundMessage message="Product Not Found" />;
  } else if (productList?.length > 0) {
    content = (
      <FlatList
        data={productList}
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
      <Topbar title="Product List" />
      <View style={{ marginHorizontal: 20, marginBottom: 180 }}>
        <View
          style={{
            justifyContent: "space-between",
            flexDirection: "row",
            marginBottom: 15,
          }}
        >
          <View style={styles.createBtn}>
            <Pressable onPress={() => router.push("/admin/product/create")}>
              <Text style={styles.createBtnTxt} preset="h5">
                Create
              </Text>
            </Pressable>
          </View>
          <Pressable
            style={styles.createBtn}
            onPress={handleDownloadAllBarcode}
          >
            <Text style={styles.createBtnTxt} preset="h5">
              Download All Barcode
            </Text>
          </Pressable>
        </View>
        <View style={styles.card}>{content}</View>
      </View>
    </>
  );
};

export default Product;

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
    width: "20%",
    alignItems: "center",
    justifyContent: "center",
    textAlign: "center",
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
    width: "48%",
    justifyContent: "center",
    alignItems: "center",
  },
  createBtnTxt: {
    color: colors.white,
    textAlign: "center",
  },
});
