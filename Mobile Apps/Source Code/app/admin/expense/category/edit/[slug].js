import React, { useEffect } from "react";
import Topbar from "../../../../../components/Topbar/Topbar";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import { Link, router, useLocalSearchParams } from "expo-router";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { colors } from "../../../../../themes/colors";
import Text from "../../../../../components/text/Text";
import { capitalize } from "../../../../../utils/helper";
import FormRadio from "../../../../../components/Form/FormRadio";
import {
  useCreateExpenseCategoryMutation,
  useGetDetailsExpenseCategoryQuery,
  useUpdateExpenseCategoryMutation,
} from "../../../../../redux/features/expense/expenseApi";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../../components/Loading/Loading";

//form validation schema
const schema = yup
  .object({
    name: yup.string().required("First name is required"),
    status: yup.string().required("Status is required"),
  })
  .required();

const EditExpenseCategory = () => {
  const { slug } = useLocalSearchParams();
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
    updateExpenseCategory,
    {
      data: updateData,
      isLoading: updateLoading,
      isSuccess: updateIsSuccess,
      isError: updateIsError,
      error: updateError,
    },
  ] = useUpdateExpenseCategoryMutation();

  const onSubmit = (data) => {
    const updateData = {
      name: data.name,
      desc: data.description,
      status: data.status,
      _method: "PUT",
    };
    updateExpenseCategory({ id: slug, data: updateData });
  };
  useEffect(() => {
    if (updateIsSuccess) {
      showMessage({
        message: updateData.message,
        type: "success",
      });
      router.push("/admin/expense/category");
    }
    if (updateIsError) {
      showMessage({
        message: updateError.data.message,
        type: "danger",
      });
    }
  }, [updateData, updateIsSuccess, updateIsError, updateError]);

  //get single expense category
  const {
    data: singleExpenseCategory,
    isSuccess: isSingleExpenseCategorySuccess,
  } = useGetDetailsExpenseCategoryQuery(slug);

  useEffect(() => {
    if (singleExpenseCategory) {
      setValue("name", singleExpenseCategory.data.name);
      setValue("description", singleExpenseCategory.data.description);
      setValue("status", singleExpenseCategory.data.status);
    }
  }, [singleExpenseCategory, isSingleExpenseCategorySuccess]);
  return (
    <>
      {updateLoading && <Loading />}
      <Topbar title="Edit Expense Category" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Category Name <Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                    value={value || ""}
                  />
                )}
                name="name"
              />
              {errors.name && (
                <Text style={styles.validationError}>
                  {capitalize(errors.name?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Description
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={[styles.textInput, { height: 80, paddingTop: 10 }]}
                    onBlur={onBlur}
                    onChangeText={onChange}
                    multiline={true}
                    value={value || ""}
                  />
                )}
                name="description"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Status<Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormRadio
                    onChange={onChange}
                    value={value}
                    selectedValue={value}
                    items={[
                      {
                        label: "Active",
                        value: "active",
                      },
                      {
                        label: "Inactive",
                        value: "inactive",
                      },
                    ]}
                  />
                )}
                name="status"
              />
              {errors.status && (
                <Text style={styles.validationError}>
                  {capitalize(errors.status?.message)}
                </Text>
              )}
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
              <Link href="/admin/expense/category">
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

export default EditExpenseCategory;

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
});
