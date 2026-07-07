import React, { useEffect, useState } from "react";

import {
  ScrollView,
  View,
  StyleSheet,
  TextInput,
  Pressable,
} from "react-native";
import { colors } from "../../../../themes/colors";
import { capitalize } from "../../../../utils/helper";
import { useForm, Controller, set } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import Text from "../../../../components/text/Text";
import { Link, router, useLocalSearchParams } from "expo-router";
import {
  useGetCitiesQuery,
  useGetCountriesQuery,
  useGetStatesQuery,
} from "../../../../redux/features/common/commonApi";
import FormSelect from "../../../../components/Form/FormSelect";
import * as ImagePicker from "expo-image-picker";
import FormCheckbox from "../../../../components/Form/FormCheckbox";
import FormRadio from "../../../../components/Form/FormRadio";
import {
  useCreateCustomerMutation,
  useGetSingleCustomerQuery,
  useUpdateCustomerMutation,
} from "../../../../redux/features/customer/customerApi";
import Loading from "../../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";
import { Image } from "expo-image";
import Topbar from "../../../../components/Topbar/Topbar";
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

const EditCustomer = () => {
  const [billingSameAsAddress, setBillingSameAsAddress] = useState(false);
  const [image, setImage] = useState();
  const [fileName, setFileName] = useState(null);

  //get customer id from params
  const { slug } = useLocalSearchParams();

  //billing address toggle
  const billingAddressToggle = () => {
    setBillingSameAsAddress(!billingSameAsAddress);
  };
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

  //get customer details
  const { data: customerDetails, isSuccess: customerDetailsSuccess } =
    useGetSingleCustomerQuery(slug);

  useEffect(() => {
    if (customerDetails) {
      setValue("first_name", customerDetails?.data?.full_name.split(" ")[0]);
      setValue("last_name", customerDetails?.data?.full_name?.split(" ")[1]);
      setValue("email", customerDetails?.data?.email);
      setValue("phone", customerDetails?.data?.phone);
      setValue("company", customerDetails?.data?.company);
      setValue("designation", customerDetails?.data?.designation);
      setValue("address_line_1", customerDetails?.data?.address_line_1);
      setValue("address_line_2", customerDetails?.data?.address_line_2);
      setValue("country", `${customerDetails?.data?.country?.id}`);
      setValue("state", customerDetails?.data?.state?.id);
      setValue("city", customerDetails?.data?.city?.id);
      setValue("zip_code", customerDetails?.data?.zipcode);
      setValue("short_address", customerDetails?.data?.short_address);
      setValue("b_first_name", customerDetails?.data?.b_first_name);
      setValue("b_last_name", customerDetails?.data?.b_last_name);
      setValue("b_email", customerDetails?.data?.b_email);
      setValue("b_phone", customerDetails?.data?.b_phone);
      setValue("b_address_line_1", customerDetails?.data?.b_address_line_1);
      setValue("b_address_line_2", customerDetails?.data?.b_address_line_2);
      setValue("b_country", `${customerDetails?.data?.b_country?.id}`);
      setValue("b_state", customerDetails?.data?.b_state?.id);
      setValue("b_city", customerDetails?.data?.b_city?.id);
      setValue("b_zipcode", customerDetails?.data?.b_zipcode);
      setValue("b_short_address", customerDetails?.data?.b_short_address);
      setImage(customerDetails?.data?.avatar_url);
      setValue("status", customerDetails?.data?.status);
      setBillingSameAsAddress(
        customerDetails?.data?.is_billing_same == "1" ? true : false
      );
    }
  }, [customerDetails, customerDetailsSuccess]);

  const [
    updateCustomer,
    { data: updateCustomerData, isLoading, isError, error, isSuccess },
  ] = useUpdateCustomerMutation();

  const onSubmit = (data) => {
    const formData = new FormData();
    formData.append("first_name", data.first_name);
    formData.append("last_name", data.last_name);
    formData.append("email", data.email);
    formData.append("phone", data.phone);
    formData.append("password", data.password);
    formData.append("password_confirmation", data.confirmPassword);
    formData.append("company", data.company || "");
    formData.append("designation", data.designation || "");
    formData.append("address_line_1", data.address_line_1 || "");
    formData.append("address_line_2", data.address_line_2 || "");
    formData.append("country", data.country || "");
    formData.append("state", data.state || "");
    formData.append("city", data.city || "");
    formData.append("zipcode", data.zip_code || "");
    formData.append("short_address", data.short_address || "");
    formData.append("billing_same", billingSameAsAddress ? 1 : 0);
    formData.append("b_first_name", data.b_first_name || data.first_name);
    formData.append("b_last_name", data.b_last_name || data.last_name);
    formData.append("b_email", data.b_email || data.email);
    formData.append("b_phone", data.b_phone || data.phone);
    formData.append(
      "b_address_line_1",
      data.b_address_line_1 || data.address_line_1
    );
    formData.append(
      "b_address_line_2",
      data.b_address_line_2 || data.address_line_2
    );
    formData.append("b_country", data.b_country || data.country);
    formData.append("b_state", data.b_state || data.state);
    formData.append("b_city", data.b_city || data.city);
    formData.append("b_zipcode", data.b_zipcode || data.zip_code);
    formData.append("_method", "PUT");
    formData.append(
      "b_short_address",
      data.b_short_address || data.short_address
    );
    formData.append("status", data.status);
    if (fileName !== null) {
      formData.append("avatar", image);
    }
    updateCustomer({ id: slug, data: formData });
  };

  useEffect(() => {
    if (isSuccess) {
      showMessage({
        message: updateCustomerData.message,
        type: "success",
      });
      router.push("/admin/customer");
    }
    if (isError) {
      showMessage({
        message: error.data.message,
        type: "danger",
      });
    }
  }, [updateCustomerData, isSuccess, isError, error]);

  //get specific form field value

  const country = watch("country");
  const state = watch("state");
  const b_country = watch("b_country");
  const b_state = watch("b_state");

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

  const b_stateItems = [];
  const { data: b_stateList, isLoading: b_stateListLoading } =
    useGetStatesQuery(b_country);
  if (b_stateList?.data) {
    b_stateList?.data?.map((item) => {
      b_stateItems.push({
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

  const b_cityItems = [];
  const { data: b_cityList, isLoading: b_cityListLoading } =
    useGetCitiesQuery(b_state);
  if (b_cityList?.data) {
    b_cityList?.data?.map((item) => {
      b_cityItems.push({
        label: item.name,
        value: item.id,
      });
    });
  }

  return (
    <>
      {isLoading && <Loading />}
      <Topbar title="Create Customer" />
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
                Password <Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                    secureTextEntry={true}
                    value={value || ""}
                  />
                )}
                name="password"
              />
            </View>
            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Confirm Password <Text style={{ color: "#ff0000" }}>*</Text>
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={styles.textInput}
                    onBlur={onBlur}
                    onChangeText={onChange}
                    secureTextEntry={true}
                    value={value || ""}
                  />
                )}
                name="confirmPassword"
              />
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
                    onChange={onChange}
                    selectedValue={value}
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
                Short address
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur, value } }) => (
                  <TextInput
                    style={[styles.textInput, { height: 80 }]}
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
              <Text preset="h2_sb">Billing address</Text>
            </View>
            <View style={styles.formGroup}>
              <FormCheckbox
                checked={billingSameAsAddress}
                toggleCheckbox={billingAddressToggle}
                label="Billing address same as address"
              />
            </View>
            {!billingSameAsAddress && (
              <>
                <View style={styles.formGroup}>
                  <Text style={styles.label} preset="h2_sb">
                    First Name
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
                    name="b_first_name"
                  />
                </View>
                <View style={styles.formGroup}>
                  <Text style={styles.label} preset="h2_sb">
                    Last Name
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
                    name="b_last_name"
                  />
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
                    name="b_email"
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
                    name="b_phone"
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
                    name="b_address_line_1"
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
                    name="b_address_line_2"
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
                        items={b_countryItems}
                        value={value}
                        selectedValue={value}
                        onChange={onChange}
                        searchable={true}
                        height={42}
                        bg={colors.grayBg}
                      />
                    )}
                    name="b_country"
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
                        items={b_stateItems}
                        value={value}
                        selectedValue={value}
                        onChange={onChange}
                        searchable={true}
                        height={42}
                        bg={colors.grayBg}
                      />
                    )}
                    name="b_state"
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
                        items={b_cityItems}
                        value={value}
                        selectedValue={value}
                        onChange={onChange}
                        searchable={true}
                        height={42}
                        bg={colors.grayBg}
                      />
                    )}
                    name="b_city"
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
                    name="b_zip_code"
                  />
                </View>
                <View style={styles.formGroup}>
                  <Text style={styles.label} preset="h2_sb">
                    Short address
                  </Text>
                  <Controller
                    control={control}
                    render={({ field: { onChange, onBlur, value } }) => (
                      <TextInput
                        style={[styles.textInput, { height: 80 }]}
                        onBlur={onBlur}
                        onChangeText={onChange}
                        multiline={true}
                        value={value || ""}
                      />
                    )}
                    name="b_short_address"
                  />
                </View>
              </>
            )}

            <View style={styles.formGroup}>
              <Text style={styles.label} preset="h2_sb">
                Banner{" "}
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
              <Link href="/admin/customer">
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

export default EditCustomer;

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
