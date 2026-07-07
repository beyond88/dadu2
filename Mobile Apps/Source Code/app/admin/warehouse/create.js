import React, { useEffect, useState } from "react";
import {
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  View,
} from "react-native";
import Text from "../../../components/text/Text";
import Topbar from "../../../components/Topbar/Topbar";
import { colors } from "../../../themes/colors";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { capitalize } from "../../../utils/helper";
import FormCheckbox from "../../../components/Form/FormCheckbox";
import FormRadio from "../../../components/Form/FormRadio";
import { useCreateAdminWarehouseMutation } from "../../../redux/features/warehouse/warehouseApi";
import { Link, useRouter } from "expo-router";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../components/Loading/Loading";

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

const WarehouseCreate = () => {
  const [defaultWarehouse, setDefaultWarehouse] = useState(false);

  //router
  const router = useRouter();
  //handle default warehouse
  const toggleDefaultWarehouse = () => {
    setDefaultWarehouse(!defaultWarehouse);
  };

  //create warehouse
  const [
    createAdminWarehouse,
    { data: warehouseData, isLoading, isSuccess, isError, error },
  ] = useCreateAdminWarehouseMutation();
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
    const warehouseData = {
      ...data,
      is_default: defaultWarehouse === true ? 1 : 0,
    };
    createAdminWarehouse(warehouseData);
  };
  //show success & error message
  useEffect(() => {
    if (isSuccess) {
      reset();
      showMessage({
        message: warehouseData.message,
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
  }, [isSuccess, isError, warehouseData]);
  return (
    <>
      {isLoading && <Loading />}
      <Topbar title="Create Warehouse" />
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

export default WarehouseCreate;

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
