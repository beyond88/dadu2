import { useLocalSearchParams } from "expo-router";
import Topbar from "../../../components/Topbar/Topbar";
import {
  useExpenseDeleteFileMutation,
  useGetSingleExpenseQuery,
} from "../../../redux/features/expense/expenseApi";
import {
  Alert,
  FlatList,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  View,
} from "react-native";
import { colors } from "../../../themes/colors";
import Text from "../../../components/text/Text";
import { useSelector } from "react-redux";
import { FontAwesome, MaterialIcons } from "@expo/vector-icons";
import * as FileSystem from "expo-file-system";
import { shareAsync } from "expo-sharing";
import { useEffect } from "react";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../components/Loading/Loading";

const ExpenseShow = () => {
  const { slug } = useLocalSearchParams();
  //get currency symbol
  const { currency_symbol } = useSelector(
    (state) => state?.settings?.generalSettings
  );
  const { data: expense } = useGetSingleExpenseQuery(slug);

  const { category_name, date, title, items, files, notes } =
    expense?.data || {};

  //expense file delete

  const [
    expenseDeleteFile,
    {
      data: deleteData,
      isLoading: deleteIsLoading,
      isError: deleteIsError,
      isSuccess: deleteIsSuccess,
      error: deleteError,
    },
  ] = useExpenseDeleteFileMutation();

  const handleFileDelete = (id) => {
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
            expenseDeleteFile(id);
          },
        },
      ]
    );
  };

  useEffect(() => {
    if (deleteIsSuccess) {
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

  //download file
  const downloadFile = async (fileName, file) => {
    const result = await FileSystem.downloadAsync(
      file,
      FileSystem.documentDirectory + fileName
    );

    save(result.uri, fileName, result.headers["Content-Type"]);
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
  return (
    <>
      {deleteIsLoading && <Loading />}
      <Topbar title="Expense Show" />
      <ScrollView style={{ paddingHorizontal: 20 }}>
        <View style={styles.card}>
          <View style={{ marginBottom: 20 }}>
            <Text preset="h5" style={{ color: "#142A3E", marginBottom: 4 }}>
              Title:{" "}
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {title}
              </Text>
            </Text>
            <Text preset="h5" style={{ color: "#142A3E", marginBottom: 4 }}>
              Category:{" "}
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {category_name}
              </Text>
            </Text>
            <Text preset="h5" style={{ color: "#142A3E", marginBottom: 4 }}>
              Date:{" "}
              <Text preset="h6" style={{ color: "#727F8B" }}>
                {date}
              </Text>
            </Text>
          </View>
          <View style={styles.tableWrap}>
            <View style={[styles.tableRow, styles.tableHeader]}>
              <View style={styles.tableCol1}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Item Name
                </Text>
              </View>
              <View style={styles.tableCol2}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Item Qty
                </Text>
              </View>
              <View style={styles.tableCol3}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Amount
                </Text>
              </View>
              <View style={styles.tableCol4}>
                <Text preset="h5_m" style={{ color: colors.white }}>
                  Note
                </Text>
              </View>
            </View>
            <View>
              {items?.data?.map((item, index) => (
                <View style={[styles.tableRow, styles.tableBody]} key={index}>
                  <View style={[styles.tableCol1, styles.tableBodyCol]}>
                    <Text preset="h6_m" style={{ color: colors.black }}>
                      {item?.name}
                    </Text>
                  </View>
                  <View style={[styles.tableCol2, styles.tableBodyCol]}>
                    <Text preset="h6" style={{ color: colors.pcolor }}>
                      {item?.quantity}
                    </Text>
                  </View>
                  <View style={[styles.tableCol3, styles.tableBodyCol]}>
                    <Text preset="h6" style={{ color: colors.pcolor }}>
                      {currency_symbol} {item?.amount}
                    </Text>
                  </View>
                  <View style={[styles.tableCol4]}>
                    <Text preset="h6" style={{ color: colors.pcolor }}>
                      {item?.note}
                    </Text>
                  </View>
                </View>
              ))}
            </View>
          </View>
          {files?.data?.length > 0 && (
            <View style={{ marginTop: 20 }}>
              <Text
                preset="h3"
                style={{
                  borderBottomColor: "#E9ECF2",
                  borderBottomWidth: 1,
                  paddingBottom: 10,
                }}
              >
                Files
              </Text>
              {files?.data?.map((file, index) => (
                <View style={styles.filesItem} key={index}>
                  <View style={styles.filesLeft}>
                    <Text>{file.file_name}</Text>
                  </View>
                  <View style={styles.filesRight}>
                    <Pressable
                      style={[
                        styles.fileBtn,
                        { backgroundColor: colors.green },
                      ]}
                      onPress={() =>
                        downloadFile(file.original_name, file.full_path)
                      }
                    >
                      <FontAwesome name="download" size={16} color="white" />
                    </Pressable>
                    <Pressable
                      style={[styles.fileBtn, { backgroundColor: colors.red }]}
                      onPress={() => handleFileDelete(file.id)}
                    >
                      <MaterialIcons name="delete" size={16} color="white" />
                    </Pressable>
                  </View>
                </View>
              ))}
            </View>
          )}

          <View style={{ marginTop: 20 }}>
            <Text
              preset="h3"
              style={{
                borderBottomColor: "#E9ECF2",
                borderBottomWidth: 1,
                paddingBottom: 10,
              }}
            >
              Notes
            </Text>
            <Text style={{ marginTop: 5 }}>{notes}</Text>
          </View>
        </View>
      </ScrollView>
    </>
  );
};

export default ExpenseShow;

const styles = StyleSheet.create({
  card: {
    backgroundColor: colors.white,
    borderRadius: 5,
    height: "100%",
    padding: 20,
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
    paddingVertical: 9,
    paddingHorizontal: 8,
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
  filesItem: {
    borderBottomWidth: 1,
    borderBottomColor: "#E9ECF2",
    paddingVertical: 10,
    flexDirection: "row",
    flexWrap: "wrap",
    justifyContent: "space-between",
    alignItems: "center",
  },
  fileBtn: {
    width: 30,
    height: 30,
    borderRadius: 4,
    justifyContent: "center",
    alignItems: "center",
    marginLeft: 10,
  },
  filesLeft: {
    width: "70%",
  },
  filesRight: {
    flexDirection: "row",
    flex: 1,
  },
});
