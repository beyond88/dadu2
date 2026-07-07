import React, { useState } from "react";
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
import {
  useCustomerUpdateUserMutation,
  useGetCustomerLoginUserQuery,
} from "../../../redux/features/user/userApi";
import { Image } from "expo-image";
import { useEffect } from "react";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import FormCheckbox from "../../../components/Form/FormCheckbox";
import FormRadio from "../../../components/Form/FormRadio";
import { LinearGradient } from "expo-linear-gradient";
import FormSelect from "../../../components/Form/FormSelect";
import {
  useGetCitiesQuery,
  useGetCountriesQuery,
  useGetStatesQuery,
} from "../../../redux/features/common/commonApi";
import * as ImagePicker from "expo-image-picker";
import Loading from "../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";

//form validation schema
const schema = yup
  .object({
    first_name: yup.string().required("First Name is required"),
    last_name: yup.string().required("Last Name is required"),
    phone: yup.string().required("Phone Number is required"),
    email: yup.string().email().required("Email is required"),
    status: yup.string().required("Status is required"),
  })
  .required();

const EditProfile = () => {
  const [image, setImage] = useState();
  const [fileName, setFileName] = useState(null);
  const [isBillingSameAsAddress, setIsBillingSameAsAddress] = useState(false);
  //same as address
  const toggleBillingSameAsAddress = () => {
    setIsBillingSameAsAddress(!isBillingSameAsAddress);
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

  //get specific form field value

  const country = watch("country");
  const state = watch("state");
  //get image
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
  //get user Data
  const { data } = useGetCustomerLoginUserQuery();

  const userData = data?.data || {};

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

  //set input values

  useEffect(() => {
    Object.keys(userData)?.map((key) => {
      setValue(key, userData[key]);
    });
    setImage(userData?.avatar_url);
    setIsBillingSameAsAddress(userData?.billing_same == 1 ? true : false);
  }, [data]);

  //get update mutation
  const [
    updateUser,
    { data: updateUserData, isSuccess, isLoading, isError, error },
  ] = useCustomerUpdateUserMutation();
  //Submit update form
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
    formData.append("zipcode", data.zipcode);
    formData.append("short_address", data.short_address);
    formData.append("status", data.status);
    formData.append("billing_same", isBillingSameAsAddress ? 1 : 0);
    if (fileName !== null) {
      formData.append("avatar", {
        uri: image,
        name: fileName,
        type: "image/*",
      });
    }
    if (!isBillingSameAsAddress) {
      formData.append("b_first_name", data.b_first_name);
      formData.append("b_last_name", data.b_last_name);
      formData.append("b_email", data.b_email);
      formData.append("b_phone", data.b_phone);
      formData.append("b_address_line_1", data.b_address_line_1);
      formData.append("b_address_line_2", data.b_address_line_2);
      formData.append("b_country", data.b_country);
      formData.append("b_state", data.b_state);
      formData.append("b_city", data.b_city);
      formData.append("b_zipcode", data.b_zipcode);
      formData.append("b_short_address", data.b_short_address);
    } else {
      formData.append("b_first_name", data.first_name);
      formData.append("b_last_name", data.last_name);
      formData.append("b_email", data.email);
      formData.append("b_phone", data.phone);
      formData.append("b_address_line_1", data.address_line_1);
      formData.append("b_address_line_2", data.address_line_2);
      formData.append("b_country", data.country);
      formData.append("b_state", data.state);
      formData.append("b_city", data.city);
      formData.append("b_zipcode", data.zipcode);
      formData.append("b_short_address", data.short_address);
    }

    updateUser(formData);
  };

  //success error handle
  useEffect(() => {
    if (isSuccess) {
      showMessage({
        message: "User Updated Successfully",
        type: "success",
      });
    } else if (isError) {
      showMessage({
        message: error?.data?.message,
        type: "danger",
      });
    }
  }, [updateUserData, isSuccess, isError]);

  return (
    <View>
      {isLoading && <Loading />}
      <Topbar title="Edit profile" customer={true} />
      <ScrollView style={{ paddingHorizontal: 20 }}>
        <View style={{ marginBottom: 100 }}>
          <View style={styles.viewProfileCard}>
            <View
              style={{
                alignItems: "center",
                marginBottom: 15,
                marginTop: 15,
              }}
            >
              <Image source={image} style={styles.profileImg} />
            </View>
            <View>
              <Text
                preset="h1"
                style={{
                  textAlign: "center",
                  color: colors.black,
                  marginBottom: 5,
                }}
              >
                {userData?.full_name}
              </Text>
              <Pressable onPress={pickImage}>
                <Text
                  preset="h3_r"
                  style={{ color: colors.themeColor, textAlign: "center" }}
                >
                  Change Profile Picture
                </Text>
              </Pressable>
            </View>
          </View>
          <ScrollView style={styles.formWrapper}>
            <View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  First Name
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your first name"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                      value={value}
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
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Last Name
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your last name"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                      value={value}
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
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Email
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your email"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                      value={value}
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
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Mobile Number
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your mobile number"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                      value={value}
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

              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Company
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your company name"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                      value={value}
                    />
                  )}
                  name="company"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Designation
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your designation"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                      value={value}
                    />
                  )}
                  name="designation"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Address Line 1
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your address"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                      value={value}
                    />
                  )}
                  name="address_line_1"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Address Line 2
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your address"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                      value={value}
                    />
                  )}
                  name="address_line_2"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
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
                      searchable={true}
                      selectedValue={userData?.country}
                    />
                  )}
                  name="country"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  State
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <FormSelect
                      placeholder="Select State"
                      items={stateItems}
                      value={value}
                      onChange={onChange}
                      searchable={true}
                      selectedValue={userData?.state}
                    />
                  )}
                  name="state"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  City
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <FormSelect
                      placeholder="Select City"
                      items={cityItems}
                      value={value}
                      onChange={onChange}
                      searchable={true}
                      selectedValue={userData?.city}
                    />
                  )}
                  name="city"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Zip Code
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your designation"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                      value={value}
                    />
                  )}
                  name="zipcode"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Short Address
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your short address"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                      value={value}
                    />
                  )}
                  name="short_address"
                />
              </View>
              <View style={styles.inputWrap}>
                <Text preset="h2">Billing Address</Text>
              </View>
              <View style={styles.inputWrap}>
                <FormCheckbox
                  checked={isBillingSameAsAddress}
                  toggleCheckbox={toggleBillingSameAsAddress}
                  label="Billing address same as address"
                />
              </View>

              {!isBillingSameAsAddress && (
                <>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
                      First Name
                    </Text>
                    <Controller
                      control={control}
                      render={({ field: { onChange, onBlur, value } }) => (
                        <TextInput
                          placeholder="Type your first name"
                          onBlur={onBlur}
                          onChangeText={onChange}
                          style={styles.input}
                          value={value}
                        />
                      )}
                      name="b_first_name"
                    />
                  </View>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
                      Last Name
                    </Text>
                    <Controller
                      control={control}
                      render={({ field: { onChange, onBlur, value } }) => (
                        <TextInput
                          placeholder="Type your last name"
                          onBlur={onBlur}
                          onChangeText={onChange}
                          style={styles.input}
                          value={value}
                        />
                      )}
                      name="b_last_name"
                    />
                  </View>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
                      Email
                    </Text>
                    <Controller
                      control={control}
                      render={({ field: { onChange, onBlur, value } }) => (
                        <TextInput
                          placeholder="Type your email"
                          onBlur={onBlur}
                          onChangeText={onChange}
                          style={styles.input}
                          value={value}
                        />
                      )}
                      name="b_email"
                    />
                  </View>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
                      Mobile Number
                    </Text>
                    <Controller
                      control={control}
                      render={({ field: { onChange, onBlur, value } }) => (
                        <TextInput
                          placeholder="Type your mobile number"
                          onBlur={onBlur}
                          onChangeText={onChange}
                          style={styles.input}
                          value={value}
                        />
                      )}
                      name="b_phone"
                    />
                  </View>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
                      Address Line 1
                    </Text>
                    <Controller
                      control={control}
                      render={({ field: { onChange, onBlur, value } }) => (
                        <TextInput
                          placeholder="Type your address"
                          onBlur={onBlur}
                          onChangeText={onChange}
                          style={styles.input}
                          value={value}
                        />
                      )}
                      name="b_address_line_1"
                    />
                  </View>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
                      Address Line 2
                    </Text>
                    <Controller
                      control={control}
                      render={({ field: { onChange, onBlur, value } }) => (
                        <TextInput
                          placeholder="Type your address"
                          onBlur={onBlur}
                          onChangeText={onChange}
                          style={styles.input}
                          value={value}
                        />
                      )}
                      name="b_address_line_2"
                    />
                  </View>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
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
                          selectedValue={userData?.b_country}
                          searchable={true}
                        />
                      )}
                      name="b_country"
                    />
                  </View>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
                      State
                    </Text>
                    <Controller
                      control={control}
                      render={({ field: { onChange, onBlur, value } }) => (
                        <FormSelect
                          placeholder="Select State"
                          items={stateItems}
                          value={value}
                          onChange={onChange}
                          selectedValue={userData?.b_state}
                          searchable={true}
                        />
                      )}
                      name="b_state"
                    />
                  </View>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
                      City
                    </Text>
                    <Controller
                      control={control}
                      render={({ field: { onChange, onBlur, value } }) => (
                        <FormSelect
                          placeholder="Select City"
                          items={cityItems}
                          value={value}
                          onChange={onChange}
                          selectedValue={userData?.b_city}
                          searchable={true}
                        />
                      )}
                      name="b_city"
                    />
                  </View>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
                      Zip Code
                    </Text>
                    <Controller
                      control={control}
                      render={({ field: { onChange, onBlur, value } }) => (
                        <TextInput
                          placeholder="Type your designation"
                          onBlur={onBlur}
                          onChangeText={onChange}
                          style={styles.input}
                          value={value}
                        />
                      )}
                      name="b_zipcode"
                    />
                  </View>
                  <View style={styles.inputWrap}>
                    <Text preset="h2_sb" style={styles.inputLabel}>
                      Short Address
                    </Text>
                    <Controller
                      control={control}
                      render={({ field: { onChange, onBlur, value } }) => (
                        <TextInput
                          placeholder="Type your short address"
                          onBlur={onBlur}
                          onChangeText={onChange}
                          style={styles.input}
                          value={value}
                        />
                      )}
                      name="b_short_address"
                    />
                  </View>
                </>
              )}

              <View style={styles.inputWrap}>
                <Text preset="h2_sb" style={styles.inputLabel}>
                  Status
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <FormRadio
                      onChange={onChange}
                      value={value}
                      selectedValue={userData?.status}
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
              <View>
                <Pressable onPress={handleSubmit(onSubmit)}>
                  <LinearGradient
                    colors={["#37DBD9", "#008AA1"]}
                    style={styles.authButton}
                  >
                    <Text preset="h3" style={styles.buttonText}>
                      Signup
                    </Text>
                  </LinearGradient>
                </Pressable>
              </View>
            </View>
          </ScrollView>
        </View>
      </ScrollView>
    </View>
  );
};

export default EditProfile;

const styles = StyleSheet.create({
  viewProfileCard: {
    backgroundColor: colors.white,
    paddingHorizontal: 20,
    paddingVertical: 20,
    marginBottom: 0,
    borderRadius: 5,
  },
  formGroup: {
    marginBottom: 15,
  },
  label: {
    color: colors.black,
    marginBottom: 10,
  },
  textInput: {
    backgroundColor: colors.white,
    height: 48,
    paddingHorizontal: 15,
    borderRadius: 5,
  },
  mobileWrap: {
    position: "relative",
  },
  countryPicker: {
    flexDirection: "row",
    alignItems: "center",
    position: "absolute",
    zIndex: 1,
    top: 10,
    left: 16,
  },
  updateButton: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    paddingHorizontal: 32,
    borderRadius: 5,
    elevation: 3,
    height: 48,
  },
  buttonText: {
    color: colors.white,
  },
  mobileInput: {
    paddingLeft: 130,
  },
  profileImg: {
    width: 78,
    height: 78,
    borderRadius: 39,
  },
  validationError: {
    color: "#ff0000",
    marginTop: 5,
  },
  formWrapper: {
    marginTop: 40,
    paddingHorizontal: 20,
  },
  inputWrap: {
    marginBottom: 20,
  },
  inputLabel: {
    marginBottom: 10,
    color: colors.black,
  },
  input: {
    height: 48,
    borderColor: colors.border,
    borderWidth: 1,
    borderRadius: 5,
    paddingHorizontal: 20,
  },
  authButton: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 10,
    paddingHorizontal: 32,
    borderRadius: 5,
    elevation: 3,
    height: 48,
  },
  buttonText: {
    color: colors.white,
  },
  validationError: {
    color: "#ff0000",
    marginTop: 5,
  },
});
