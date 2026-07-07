import React, { useEffect, useState } from "react";
import Topbar from "../../../../components/Topbar/Topbar";
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
import Text from "../../../../components/text/Text";
import { capitalize } from "../../../../utils/helper";
import { colors } from "../../../../themes/colors";
import { Link, router, useLocalSearchParams } from "expo-router";
import FormSelect from "../../../../components/Form/FormSelect";
import {
  useGetCitiesQuery,
  useGetCountriesQuery,
  useGetStatesQuery,
} from "../../../../redux/features/common/commonApi";
import FormRadio from "../../../../components/Form/FormRadio";
import { Image } from "expo-image";
import * as ImagePicker from "expo-image-picker";
import {
  useGetSingleSupplierQuery,
  useUpdateSupplierMutation,
} from "../../../../redux/features/supplier/supplierApi";
import { showMessage } from "react-native-flash-message";
import Loading from "../../../../components/Loading/Loading";

//form validation schema
const schema = yup
  .object({
    first_name: yup.string().required("First name is required"),
    last_name: yup.string().required("Last name is required"),
    email: yup.string().email().required("Email is required"),
    phone: yup.string().required("Phone is required"),
    status: yup.string().required("Status is required"),
  })
  .required();

const CreateSupplier = () => {
  const [image, setImage] = useState();
  const [fileName, setFileName] = useState(null);

  const { slug } = useLocalSearchParams();

  const { data: supplier, isSuccess: supplierIsSuccess } =
    useGetSingleSupplierQuery(slug);

  //pick an image
  const pickImage = async () => {
    // No permissions request is necessary for launching the image library
    let result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.All,
      allowsEditing: true,
      aspect: [4, 3],
      quality: 1,
    });

    var filename = result.assets[0].uri.substring(
      result.assets[0].uri.lastIndexOf("/") + 1,
      result.assets[0].uri.length
    );

    if (!result.canceled) {
      setImage(result.assets[0].uri);
      setFileName(filename);
    }
  };
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
    updateSupplier,
    {
      data: updateData,
      isLoading: updateLoading,
      isSuccess: updateIsSuccess,
      isError: updateIsError,
      error: updateError,
    },
  ] = useUpdateSupplierMutation();
  const onSubmit = (data) => {
    const formData = new FormData();
    formData.append("first_name", data.first_name);
    formData.append("last_name", data.last_name);
    formData.append("email", data.email);
    formData.append("phone", data.phone);
    formData.append("company", data.company);
    formData.append("designation", data.designation);
    formData.append("address_line_1", data.address_line_1);
    formData.append("address_line_2", data.address_line_2);
    formData.append("country", data.country);
    formData.append("state", data.state);
    formData.append("city", data.city);
    formData.append("zipcode", data.zip_code);
    formData.append("short_address", data.short_address);
    formData.append("status", data.status);
    formData.append("_method", "PUT");
    if (fileName !== null) {
      formData.append("avatar", {
        uri: image,
        name: fileName,
        type: "image/*",
      });
    }
    updateSupplier({ id: slug, data: formData });
  };

  useEffect(() => {
    if (updateIsSuccess) {
      showMessage({
        message: updateData.message,
        type: "success",
      });
      router.push("/admin/supplier");
    }
    if (updateIsError) {
      showMessage({
        message: updateError.data.message,
        type: "danger",
      });
    }
  }, [updateData, updateIsSuccess, updateIsError, updateError]);

  //set values

  useEffect(() => {
    if (supplier) {
      setValue("first_name", supplier?.data.full_name?.split(" ")[0]);
      setValue("last_name", supplier?.data.full_name?.split(" ")[1]);
      setValue("email", supplier?.data.email);
      setValue("phone", supplier?.data.phone);
      setValue("company", supplier?.data.company);
      setValue("designation", supplier?.data.designation);
      setValue("address_line_1", supplier?.data.address_line_1);
      setValue("address_line_2", supplier?.data.address_line_2);
      setValue("country", `${supplier?.data.country.id}`);
      setValue("state", supplier?.data.state.id);
      setValue("city", supplier?.data.city.id);
      setValue("zip_code", supplier?.data.zipcode);
      setValue("short_address", supplier?.data.short_address);
      setValue("status", supplier?.data?.supplier_status?.toLowerCase());
      setImage(supplier?.data.avatar_url);
    }
  }, [supplier, supplierIsSuccess]);

  //get specific form field value

  const country = watch("country");
  const state = watch("state");

  //get country list
  const countryItems = [];
  const { data: countryList, isLoading: countryListLoading } =
    useGetCountriesQuery();

  if (countryList?.data) {
    countryList?.data?.map((item) => {
      countryItems.push({
        label: item.name,
        value: item.id,
      });
    });
  }

  const b_countryItems = [];
  if (countryList?.data) {
    countryList?.data?.map((item) => {
      b_countryItems.push({
        label: item.name,
        value: item.id,
      });
    });
  }

  //get state list

  const stateItems = [];

  const { data: stateList, isLoading: stateListLoading } =
    useGetStatesQuery(country);

  if (stateList?.data) {
    stateList?.data?.map((item) => {
      stateItems.push({
        label: item.name,
        value: item.id,
      });
    });
  }
  //get city list
  const cityItems = [];
  const { data: cityList, isLoading: cityListLoading } =
    useGetCitiesQuery(state);
  if (cityList?.data) {
    cityList?.data?.map((item) => {
      cityItems.push({
        label: item.name,
        value: item.id,
      });
    });
  }
  return (
    <>
      {updateLoading && <Loading />}
      <Topbar title="Edit Supplier" />
      <View style={{ marginHorizontal: 20, marginBottom: 80 }}>
        <ScrollView>
          <View style={styles.formWrap}>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                First Name <Text style={{ color: "#ff0000" }}>*</Text>
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
                name="first_name"
              />
              {errors.first_name && (
                <Text style={styles.validationError}>
                  {capitalize(errors.first_name?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Last Name <Text style={{ color: "#ff0000" }}>*</Text>
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
                name="last_name"
              />
              {errors.last_name && (
                <Text style={styles.validationError}>
                  {capitalize(errors.last_name?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Email <Text style={{ color: "#ff0000" }}>*</Text>
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
              {errors.email && (
                <Text style={styles.validationError}>
                  {capitalize(errors.email?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Phone <Text style={{ color: "#ff0000" }}>*</Text>
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
              {errors.phone && (
                <Text style={styles.validationError}>
                  {capitalize(errors.phone?.message)}
                </Text>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Company
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
                name="company"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Designation
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
                name="designation"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Address Line 1
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
                name="address_line_1"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Address Line 2
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
                name="address_line_2"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Country
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select Country"
                    items={countryItems}
                    value={value}
                    selectedValue={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="country"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                State
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select State"
                    items={stateItems}
                    value={value}
                    selectedValue={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="state"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                City
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <FormSelect
                    placeholder="Select City"
                    items={cityItems}
                    value={value}
                    selectedValue={value}
                    onChange={onChange}
                    searchable={true}
                    height={42}
                    bg={colors.grayBg}
                  />
                )}
                name="city"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Zip Code
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
                name="zip_code"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Short Address
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
                name="short_address"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Avatar{" "}
                <Text>
                  (Supported type: png, jpg, jpeg | Max size: 300kb |
                  Width:500px, Height:500px)
                </Text>
              </Text>
              <Pressable onPress={pickImage}>
                <Text preset="h3_r" style={styles.chooseFile}>
                  Choose File
                </Text>
              </Pressable>
              {image && (
                <Image
                  source={{ uri: image }}
                  style={{
                    width: "100%",
                    height: 150,
                    borderRadius: 5,
                    marginTop: 10,
                    objectFit: "cover",
                  }}
                />
              )}
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
              <Link href="/admin/supplier">
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

export default CreateSupplier;

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
