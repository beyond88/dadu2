import React, { useEffect } from "react";
import Topbar from "../../../../../components/Topbar/Topbar";
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
import Text from "../../../../../components/text/Text";
import { colors } from "../../../../../themes/colors";
import { Link, router, useLocalSearchParams } from "expo-router";
import {
  useCreateCountryMutation,
  useGetSingleCountryQuery,
  useUpdateCountryMutation,
} from "../../../../../redux/features/setting/countryApi";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../../components/Loading/Loading";

//form validation schema
const schema = yup
  .object({
    name: yup.string().required("Name is required"),
    shortname: yup.string().required("Shortname is required"),
    phonecode: yup.string().required("Phonecode is required"),
  })
  .required();

const CountryEdit = () => {
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
    updateCountry,
    {
      data: updateData,
      isLoading: updateLoading,
      isSuccess: updateIsSuccess,
      isError: updateIsError,
      error: updateError,
    },
  ] = useUpdateCountryMutation();

  const onSubmit = (data) => {
    const updateData = {
      name: data.name,
      shortname: data.shortname,
      phonecode: data.phonecode,
      _method: "PUT",
    };
    updateCountry({ id: slug, data: updateData });
  };

  useEffect(() => {
    if (updateIsSuccess) {
      showMessage({
        message: updateData.message,
        type: "success",
      });
      router.push("/admin/setting/country");
    }
    if (updateIsError) {
      showMessage({
        message: updateError.data.message,
        type: "danger",
      });
    }
  }, [updateData, updateIsSuccess, updateIsError, updateError]);

  //get single country
  const { data: getSingleCountry, isSuccess: getSingleCountryIsSuccess } =
    useGetSingleCountryQuery(slug);

  useEffect(() => {
    if (getSingleCountry) {
      setValue("name", getSingleCountry?.data?.name);
      setValue("shortname", getSingleCountry?.data?.shortname);
      setValue("phonecode", getSingleCountry?.data?.phonecode);
    }
  }, [getSingleCountryIsSuccess, getSingleCountry]);
  return (
    <>
      {updateLoading && <Loading />}
      <Topbar title="Edit Country" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Name <Text style={{ color: "#ff0000" }}>*</Text>
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
                  {errors.name?.message}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Shortname <Text style={{ color: "#ff0000" }}>*</Text>
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
                name="shortname"
              />
              {errors.shortname && (
                <Text style={styles.validationError}>
                  {errors.shortname?.message}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Phonecode <Text style={{ color: "#ff0000" }}>*</Text>
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
                name="phonecode"
              />
              {errors.phonecode && (
                <Text style={styles.validationError}>
                  {errors.phonecode?.message}
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
              <Link href="/admin/setting/country">
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

export default CountryEdit;

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
});
