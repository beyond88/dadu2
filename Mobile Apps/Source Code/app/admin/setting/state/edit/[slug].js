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
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../../components/Loading/Loading";
import {
  useCreateStateMutation,
  useUpdateStateMutation,
} from "../../../../../redux/features/setting/stateApi";
import { useGetCountriesQuery } from "../../../../../redux/features/common/commonApi";
import FormSelect from "../../../../../components/Form/FormSelect";
import { useGetSingleStateQuery } from "../../../../../redux/features/setting/stateApi";

//form validation schema
const schema = yup
  .object({
    country: yup.string().required("Country is required"),
    name: yup.string().required("Name is required"),
  })
  .required();

const StateEdit = () => {
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
    updateState,
    {
      data: updateData,
      isLoading: updateLoading,
      isSuccess: updateIsSuccess,
      isError: updateIsError,
      error: updateError,
    },
  ] = useUpdateStateMutation();

  const onSubmit = (data) => {
    const updateData = {
      name: data.name,
      country_id: data.country,
      _method: "PUT",
    };
    updateState({ id: slug, data: updateData });
  };

  useEffect(() => {
    if (updateIsSuccess) {
      showMessage({
        message: updateData.message,
        type: "success",
      });
      router.push("/admin/setting/state");
    }
    if (updateIsError) {
      showMessage({
        message: updateError.data.message,
        type: "danger",
      });
    }
  }, [updateData, updateIsSuccess, updateIsError, updateError]);

  //get country
  const { data: getCountry } = useGetCountriesQuery();

  const countryList = [];
  if (getCountry) {
    getCountry.data.map((item) => {
      countryList.push({ label: item.name, value: item.id });
    });
  }

  //get single state
  const { data: singleState, isSuccess: singleStateIsSuccess } =
    useGetSingleStateQuery(slug);

  useEffect(() => {
    if (singleState) {
      setValue("name", singleState?.data?.name);
      setValue("country", `${singleState?.data?.country?.id}`);
    }
  }, [singleState, singleStateIsSuccess]);
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
                Country <Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select country"
                    items={countryList}
                    value={value}
                    selectedValue={value}
                    onChange={onChange}
                    height={42}
                    bg={colors.grayBg}
                    searchable={true}
                  />
                )}
                name="country"
              />
              {errors.country && (
                <Text style={styles.validationError}>
                  {errors.country?.message}
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
              <Link href="/admin/setting/state">
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

export default StateEdit;

const styles = StyleSheet.create({
  formWrap: {
    backgroundColor: "#fff",
    padding: 20,
    paddingVertical: 40,
    borderRadius: 5,
  },
  formGroup: {
    marginBottom: 20,
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
