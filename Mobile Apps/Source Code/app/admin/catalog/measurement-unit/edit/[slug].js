import React, { useEffect, useState } from "react";
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
import { Link, router, useLocalSearchParams } from "expo-router";
import { showMessage } from "react-native-flash-message";
import Topbar from "../../../../../components/Topbar/Topbar";
import { capitalize } from "../../../../../utils/helper";
import { colors } from "../../../../../themes/colors";
import Text from "../../../../../components/text/Text";
import Loading from "../../../../../components/Loading/Loading";
import {
  useDetailsMeasurementUnitQuery,
  useUpdateMeasurementUnitMutation,
  useUpdateWeightUnitMutation,
} from "../../../../../redux/features/catalog/catalogApi";

//form validation schema
const schema = yup
  .object({
    name: yup.string().required(),
  })
  .required();

const MeasurementUnitEdit = () => {
  //get slug
  const { slug } = useLocalSearchParams();
  //get category data
  const { data: singleMeasurementUnit, isSuccess: detailsIsSuccess } =
    useDetailsMeasurementUnitQuery(slug);

  //set form data
  useEffect(() => {
    if (singleMeasurementUnit) {
      setValue("name", singleMeasurementUnit?.data?.name);
    }
  }, [singleMeasurementUnit, detailsIsSuccess]);
  //edit category mutation

  const [
    updateMeasurementUnit,
    {
      data: updateMeasurementUnitData,
      isSuccess: updateIsSuccess,
      isLoading: updateIsLoading,
      isError: updateIsError,
      error: updateError,
    },
  ] = useUpdateMeasurementUnitMutation();
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
  const onSubmit = (data) => {
    const formData = new FormData();
    formData.append("name", data.name);
    formData.append("_method", "PUT");
    updateMeasurementUnit({ id: slug, body: formData });
  };

  //show error & success message

  useEffect(() => {
    if (updateIsSuccess) {
      showMessage({
        message: updateMeasurementUnitData.message,
        type: "success",
      });
      router.push("/admin/catalog/measurement-unit");
    }
    if (updateIsError) {
      showMessage({
        message: updateError.data.message,
        type: "danger",
      });
    }
  }, [updateMeasurementUnitData, updateIsSuccess, updateIsError, updateError]);

  return (
    <>
      {updateIsLoading && <Loading />}
      <Topbar title="Edit Measurement Unit" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Weight Unit Name<Text style={{ color: "#ff0000" }}> *</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    value={value || ""}
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
              <Link href="/admin/catalog/measurement-unit">
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

export default MeasurementUnitEdit;

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
    paddingHorizontal: 20,
  },
  btnText: {
    color: colors.white,
  },
  cancelBtn: {
    backgroundColor: colors.red,
  },
});
