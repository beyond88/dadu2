import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
import { Pressable, ScrollView, TextInput, View } from "react-native";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { Link, useLocalSearchParams, useRouter } from "expo-router";
import Text from "../../../../components/text/Text";
import { capitalize } from "../../../../utils/helper";
import FormRadio from "../../../../components/Form/FormRadio";
import FormCheckbox from "../../../../components/Form/FormCheckbox";
import { StyleSheet } from "react-native";
import { colors } from "../../../../themes/colors";
import {
  useGetAdminSingleWarehouseQuery,
  useUpdateAdminWarehouseMutation,
} from "../../../../redux/features/warehouse/warehouseApi";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";
//form validation schema
const schema = yup
  .object({
    name: yup.string().required(),
    priority: yup
      .number()
      .required("Priority is required")
      .typeError("Priority must be a number"),
    status: yup.string().required("Status is required"),
  })
  .required();

const WarehouseEdit = () => {
  const [defaultWarehouse, setDefaultWarehouse] = useState(false);
  //get slug from router
  const { slug } = useLocalSearchParams();
  const router = useRouter();

  //get warehouse details
  const { data: warehouseDetailsData } = useGetAdminSingleWarehouseQuery(slug);

  const warehouseDetails = warehouseDetailsData?.data || {};
  //handle default warehouse
  const toggleDefaultWarehouse = () => {
    setDefaultWarehouse(!defaultWarehouse);
  };

  //get updated warehouse api

  const [
    updateAdminWarehouse,
    {
      data: updatedWarehouseData,
      isLoading: updatedIsLoading,
      isError,
      error,
      isSuccess,
    },
  ] = useUpdateAdminWarehouseMutation();

  //get form data
  const {
    control,
    handleSubmit,
    formState: { errors },
    reset,
    setValue,
  } = useForm({
    resolver: yupResolver(schema),
  });
  const onSubmit = (data) => {
    updateAdminWarehouse(data);
  };
  //set form data
  useEffect(() => {
    Object.keys(warehouseDetails)?.map((key) => {
      setValue(key, warehouseDetails[key] || " ");
    });
    setDefaultWarehouse(warehouseDetails?.is_default == 1 ? true : false);
  }, [warehouseDetailsData]);

  //show success & error message
  useEffect(() => {
    if (isSuccess) {
      reset();
      showMessage({
        message: updatedWarehouseData.message,
        type: "success",
      });
      router.push("/admin/warehouse");
    }
    if (isError) {
      showMessage({
        message: error.data.message,
        type: "danger",
      });
    }
  }, [updatedWarehouseData, isSuccess, isError, error]);
  return (
    <>
      {updatedIsLoading && <Loading />}
      <Topbar title="Edit Warehouse" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Warehouse Name
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
                Email
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
                name="email"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Phone
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
                name="phone"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Company Name
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
                name="company_name"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Address 1
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
                name="address_1"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Address 2
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
                name="address_2"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Priority
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
                name="priority"
              />
              {errors.priority && (
                <Text style={styles.validationError}>
                  {capitalize(errors.priority?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <FormCheckbox
                checked={defaultWarehouse}
                toggleCheckbox={toggleDefaultWarehouse}
                label="Is Default Warehouse"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Status
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormRadio
                    onChange={onChange}
                    value={value}
                    selectedValue={warehouseDetails?.status}
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
              <Pressable
                onPress={handleSubmit(onSubmit)}
                style={styles.formBtn}
              >
                <Text preset="h3" style={styles.btnText}>
                  Submit
                </Text>
              </Pressable>
              <Link href="/admin/warehouse">
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

export default WarehouseEdit;

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
    paddingHorizontal: 25,
  },
  btnText: {
    color: colors.white,
  },
  cancelBtn: {
    backgroundColor: colors.red,
  },
});
