import React, { useEffect, useState } from "react";
import Topbar from "../../../components/Topbar/Topbar";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { colors } from "../../../themes/colors";
import Text from "../../../components/text/Text";
import FormDate from "../../../components/FormDate/FormDate";
import * as DocumentPicker from "expo-document-picker";
import FormSelect from "../../../components/Form/FormSelect";
import { useGetUsersListQuery } from "../../../redux/features/user/userApi";
import { Link, router } from "expo-router";
import { capitalize } from "../../../utils/helper";
import { AntDesign } from "@expo/vector-icons";
import {
  useCreateExpenseMutation,
  useGetExpenseListQuery,
  useGetExpensesCategoriesQuery,
} from "../../../redux/features/expense/expenseApi";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../components/Loading/Loading";
//form validation schema
const schema = yup
  .object({
    category: yup.string().required("Category is required"),
    title: yup.string().required("Title is required"),

    notes: yup.string().max(500, "Max 500 chars"),
  })
  .required();
const ExpenseCreate = () => {
  const [selectedStartDate, setSelectedStartDate] = useState(new Date());
  const [fileName, setFileName] = useState(null);
  const [file, setFile] = useState(null);
  const [expensesItem, setExpensesItem] = useState([
    {
      name: "",
      quantity: "",
      amount: "",
      note: "",
    },
  ]);
  //get form data
  const {
    control,
    handleSubmit,
    formState: { errors },
    reset,
    setValue,
    watch,
  } = useForm({
    resolver: yupResolver(schema),
  });

  const [
    createExpense,
    {
      data: createData,
      isLoading: createLoading,
      isSuccess: createIsSuccess,
      isError: createIsError,
      error: createError,
    },
  ] = useCreateExpenseMutation();

  const onSubmit = (data) => {
    const formData = new FormData();
    formData.append("title", data.title);
    formData.append("category", data.category);
    formData.append("date", "2023-12-10");
    const expenseItem = [];
    expensesItem.map((item) => {
      expenseItem.push({
        item_name: item.name,
        item_qty: item.quantity,
        amount: item.amount,
        note: item.note,
      });
    });
    formData.append("data", JSON.stringify(expenseItem));
    formData.append("notes", data.notes);
    formData.append("expense_user", data.expense_user);
    if (file) {
      formData.append("files", {
        uri: file,
        name: fileName,
        type: "application/*",
      });
    }

    createExpense(formData);
  };

  useEffect(() => {
    if (createIsSuccess) {
      showMessage({
        message: createData.message,
        type: "success",
      });
      router.push("/admin/expense");
    }
    if (createIsError) {
      showMessage({
        message: createError.data.message,
        type: "danger",
      });
    }
  }, [createData, createIsSuccess, createIsError, createError]);

  //pick document
  const pickDocument = async () => {
    let result = await DocumentPicker.getDocumentAsync({});
    if (!result.canceled) {
      setFileName(result.assets[0].name);
      setFile(result.assets[0].uri);
    }
  };

  //get category list

  const { data: categoryList } = useGetExpensesCategoriesQuery();
  const categoryListData = [];
  if (categoryList?.data?.data) {
    categoryList?.data?.data.map((item) => {
      categoryListData.push({
        label: item.name,
        value: item.id,
      });
    });
  }

  //get expense user

  const { data: expenseUser } = useGetUsersListQuery();

  const expenseUserList = [];
  if (expenseUser?.data?.data) {
    expenseUser?.data?.data.map((item) => {
      expenseUserList.push({
        label: item.name,
        value: item.id,
      });
    });
  }

  //handle item add
  const handleItemAdd = () => {
    setExpensesItem([
      ...expensesItem,
      {
        name: "",
        quantity: "",
        amount: "",
        note: "",
      },
    ]);
  };

  //handle item remove
  const handleItemRemove = (index) => {
    //index wise specific  item remove not last item remove
    const list = [...expensesItem];
    let indexToRemove = 1;
    if (index !== -1) {
      list.splice(index, indexToRemove);
    }
    setExpensesItem(list);
  };

  //handle item name
  const handleItemName = (text, index) => {
    const list = [...expensesItem];
    list[index].name = text;
    setExpensesItem(list);
  };

  //handle quantity

  const handleQuantity = (text, index) => {
    const list = [...expensesItem];
    list[index].quantity = text;
    setExpensesItem(list);
  };
  //handle amount

  const handleAmount = (text, index) => {
    const list = [...expensesItem];
    list[index].amount = text;
    setExpensesItem(list);
  };

  //handle note
  const handleNote = (text, index) => {
    const list = [...expensesItem];
    list[index].note = text;
    setExpensesItem(list);
  };

  return (
    <>
      {createLoading && <Loading />}
      <Topbar title="Create Expense" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Title <Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                  />
                )}
                name="title"
              />
              {errors.title && (
                <Text style={styles.validationError}>
                  {capitalize(errors.title?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Category <Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select category"
                    items={categoryListData}
                    value={value}
                    onChange={onChange}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="category"
              />
              {errors.category && (
                <Text style={styles.validationError}>
                  {capitalize(errors.category?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Date <Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <FormDate setSelectedDate={setSelectedStartDate} bg="gray" />
            </View>

            <View style={styles.formGroup}>
              <View
                style={{
                  flexDirection: "row",
                  alignItems: "center",
                  justifyContent: "space-between",
                }}
              >
                <Text style={styles.label} preset="h2_sb">
                  Expenses
                </Text>
                <Pressable style={styles.addBtn} onPress={handleItemAdd}>
                  <AntDesign name="plus" size={16} color="white" />
                </Pressable>
              </View>
              {expensesItem?.map((item, index) => (
                <View style={styles.cardTableItemWrap} key={index}>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Item name
                    </Text>
                    <View style={styles.itemRight}>
                      <TextInput
                        style={styles.textInput}
                        value={expensesItem[index].name || ""}
                        onChangeText={(text) => handleItemName(text, index)}
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Quantity
                    </Text>
                    <View style={styles.itemRight}>
                      <TextInput
                        style={styles.textInput}
                        value={expensesItem[index].quantity || ""}
                        onChangeText={(text) => handleQuantity(text, index)}
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Amount(USD)
                    </Text>
                    <View style={styles.itemRight}>
                      <TextInput
                        style={styles.textInput}
                        onChangeText={(text) => handleAmount(text, index)}
                        value={expensesItem[index].amount}
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}>
                      Note
                    </Text>
                    <View style={styles.itemRight}>
                      <TextInput
                        style={styles.textInput}
                        onChangeText={(text) => handleNote(text, index)}
                        value={expensesItem[index].note}
                      />
                    </View>
                  </View>
                  <View style={styles.tableCardItem}>
                    <Text preset="h5" style={styles.itemLeft}></Text>
                    <Text style={styles.itemRight}>
                      <Pressable onPress={() => handleItemRemove(index)}>
                        <AntDesign name="delete" size={20} color="red" />
                      </Pressable>
                    </Text>
                  </View>
                </View>
              ))}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Files
              </Text>
              <Pressable onPress={pickDocument}>
                <Text preset="h3_r" style={styles.chooseFile}>
                  Choose File
                </Text>
              </Pressable>
              {fileName && (
                <Text preset="h3_r" style={{ marginTop: 10 }}>
                  {fileName}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Notes <Text>(Max 500 chars)</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={[styles.textInput, { height: 80, paddingTop: 10 }]}
                    onBlur={onBlur}
                    onChangeText={onChange}
                    multiline={true}
                  />
                )}
                name="notes"
              />
              {errors.notes && (
                <Text style={styles.validationError}>
                  {capitalize(errors.notes?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Expense user
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select Expense user"
                    items={expenseUserList}
                    value={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="expense_user"
              />
            </View>
            <View style={styles.formActionBtn}>
              <View style={{ flexDirection: "row", gap: 10 }}>
                <Pressable
                  onPress={handleSubmit(onSubmit)}
                  style={styles.formBtn}
                >
                  <Text preset="h3" style={styles.btnText}>
                    Submit
                  </Text>
                </Pressable>
              </View>
              <Link href="/admin/expense">
                <View style={[styles.formBtn, styles.cancelBtn]}>
                  <Text preset="h3" style={styles.btnText}>
                    Cancel
                  </Text>
                </View>
              </Link>
            </View>
          </View>
        </ScrollView>
      </View>
    </>
  );
};

export default ExpenseCreate;

const styles = StyleSheet.create({
  formWrap: {
    backgroundColor: "#fff",
    padding: 20,
    borderRadius: 5,
  },
  formGroup: {
    marginBottom: 16,
  },
  label: {
    color: colors.black,
    marginBottom: 10,
  },
  textInput: {
    backgroundColor: colors.grayBg,
    height: 42,
    paddingHorizontal: 15,
    borderRadius: 5,
  },
  validationError: {
    color: "#ff0000",
    marginTop: 5,
  },
  formActionBtn: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  formBtn: {
    backgroundColor: colors.themeColor,
    borderRadius: 5,
    paddingVertical: 10,
    paddingHorizontal: 10,
  },
  btnText: {
    color: colors.white,
    fontSize: 12,
  },
  cancelBtn: {
    backgroundColor: colors.red,
  },
  chooseFile: {
    backgroundColor: colors.themeColor,
    color: colors.white,
    paddingVertical: 12,
    paddingHorizontal: 20,
    borderRadius: 5,
    textAlign: "center",
  },
  addBtn: {
    backgroundColor: colors.themeColor,
    width: 32,
    height: 32,
    borderRadius: 5,
    alignItems: "center",
    justifyContent: "center",
  },
  cardTableItemWrap: {
    borderWidth: 1,
    borderColor: "#E9ECF2",
    borderRadius: 5,
    marginTop: 10,
    marginBottom: 10,
  },
  tableCardItem: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingVertical: 10,
    borderBottomColor: "#E9ECF2",
    paddingHorizontal: 10,
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
});
