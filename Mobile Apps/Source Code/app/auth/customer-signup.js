import {
  Image,
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  View,
} from "react-native";
import Text from "../../components/text/Text";
import { colors } from "../../themes/colors";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import FormSelect from "../../components/Form/FormSelect";
import { LinearGradient } from "expo-linear-gradient";
import { capitalize } from "../../utils/helper";
import { useEffect, useState } from "react";
import FormCheckbox from "../../components/Form/FormCheckbox";
import FormRadio from "../../components/Form/FormRadio";
import {
  useGetCitiesQuery,
  useGetCountriesQuery,
  useGetStatesQuery,
} from "../../redux/features/common/commonApi";
import { useCustomerSignupMutation } from "../../redux/features/auth/authApi";
import { showMessage } from "react-native-flash-message";
import Loading from "../../components/Loading/Loading";
import { useRouter } from "expo-router";
//form validation schema
const schema = yup
  .object({
    first_name: yup.string().required("First Name is required"),
    last_name: yup.string().required("Last Name is required"),
    phone: yup.string().required("Phone Number is required"),
    password: yup
      .string()
      .required("Password is required")
      .min(8, "Password must be at least 8 characters"),
    password_confirmation: yup
      .string()
      .required("Confirm Password is required")
      .oneOf([yup.ref("password"), null], "Passwords must match "),
    email: yup.string().email().required("Email is required"),
    status: yup.string().required("Status is required"),
  })
  .required();
const CustomerSignup = () => {
  const [isBillingSameAsAddress, setIsBillingSameAsAddress] = useState(false);

  const router = useRouter();
  //Hook form init
  const {
    control,
    handleSubmit,
    formState: { errors },
    reset,
    watch,
  } = useForm({
    resolver: yupResolver(schema),
  });
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

  //same as address
  const toggleBillingSameAsAddress = () => {
    setIsBillingSameAsAddress(!isBillingSameAsAddress);
  };

  //Customer Signup

  const [
    customerSignup,
    {
      data: customerSignupData,
      isLoading: customerSignupLoading,
      isSuccess: customerSignupSuccess,
      isError: customerSignupError,
      error: responseError,
    },
  ] = useCustomerSignupMutation();

  //get form data

  const onSubmit = (data) => {
    const signupData = {
      first_name: data.first_name,
      last_name: data.last_name,
      email: data.email,
      phone: data.phone,
      password: data.password,
      password_confirmation: data.password_confirmation,
      company: data.company,
      designation: data.designation,
      address_line_1: data.address_line_1,
      address_line_2: data.address_line_2,
      country: data.country,
      state: data.state,
      city: data.city,
      zipcode: data.zipcode,
      short_address: data.short_address,
      billing_same: isBillingSameAsAddress ? 1 : 0,
      status: data.status,
    };
    if (!isBillingSameAsAddress) {
      signupData.b_first_name = data.b_first_name;
      signupData.b_last_name = data.b_last_name;
      signupData.b_email = data.b_email;
      signupData.b_phone = data.b_phone;
      signupData.b_address_line_1 = data.b_address_line_1;
      signupData.b_address_line_2 = data.b_address_line_2;
      signupData.b_country = data.b_country;
      signupData.b_state = data.b_state;
      signupData.b_city = data.b_city;
      signupData.b_zipcode = data.b_zipcode;
      signupData.b_short_address = data.b_short_address;
    } else {
      signupData.b_first_name = data.first_name;
      signupData.b_last_name = data.last_name;
      signupData.b_email = data.email;
      signupData.b_phone = data.phone;
      signupData.b_address_line_1 = data.address_line_1;
      signupData.b_address_line_2 = data.address_line_2;
      signupData.b_country = data.country;
      signupData.b_state = data.state;
      signupData.b_city = data.city;
      signupData.b_zipcode = data.zipcode;
      signupData.b_short_address = data.short_address;
    }
    customerSignup(signupData);
  };

  //successfully login
  useEffect(() => {
    if (customerSignupSuccess) {
      reset();
      showMessage({
        message: customerSignupData?.message,
        type: "success",
      });
      router.push("/auth/customer-login");
    } else if (customerSignupError) {
      showMessage({
        message: responseError?.data?.message,
        type: "danger",
      });
    }
  }, [customerSignupSuccess, customerSignupError, responseError]);
  return (
    <>
      {customerSignupLoading && <Loading />}
      <View style={styles.authWrapper}>
        <View style={styles.authTitle}>
          <Image
            source={require("../../assets/images/logo.png")}
            style={styles.logo}
          />
          <Text preset="h3_r" style={styles.title}>
            Signup your Customer account!
          </Text>
        </View>
        <ScrollView style={styles.formWrapper}>
          <View>
            <View style={styles.inputWrap}>
              <Text preset="h2_sb" style={styles.inputLabel}>
                First Name
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your first name"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
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
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your last name"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
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
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your email"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
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
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your mobile number"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
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
                Password
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your password"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
                    secureTextEntry={true}
                  />
                )}
                name="password"
              />
              {errors.password && (
                <Text style={styles.validationError}>
                  {capitalize(errors.password?.message)}
                </Text>
              )}
            </View>
            <View style={styles.inputWrap}>
              <Text preset="h2_sb" style={styles.inputLabel}>
                Confirm Password
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your password"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
                    secureTextEntry={true}
                  />
                )}
                name="password_confirmation"
              />
              {errors.password_confirmation && (
                <Text style={styles.validationError}>
                  {capitalize(errors.password_confirmation?.message)}
                </Text>
              )}
            </View>
            <View style={styles.inputWrap}>
              <Text preset="h2_sb" style={styles.inputLabel}>
                Company
              </Text>
              <Controller
                control={control}
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your company name"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
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
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your designation"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
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
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your address"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
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
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your address"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
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
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your designation"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
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
                render={({ field: { onChange, onBlur } }) => (
                  <TextInput
                    placeholder="Type your short address"
                    onBlur={onBlur}
                    onChangeText={onChange}
                    style={styles.input}
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
                    render={({ field: { onChange, onBlur } }) => (
                      <TextInput
                        placeholder="Type your first name"
                        onBlur={onBlur}
                        onChangeText={onChange}
                        style={styles.input}
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
                    render={({ field: { onChange, onBlur } }) => (
                      <TextInput
                        placeholder="Type your last name"
                        onBlur={onBlur}
                        onChangeText={onChange}
                        style={styles.input}
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
                    render={({ field: { onChange, onBlur } }) => (
                      <TextInput
                        placeholder="Type your email"
                        onBlur={onBlur}
                        onChangeText={onChange}
                        style={styles.input}
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
                    render={({ field: { onChange, onBlur } }) => (
                      <TextInput
                        placeholder="Type your mobile number"
                        onBlur={onBlur}
                        onChangeText={onChange}
                        style={styles.input}
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
                    render={({ field: { onChange, onBlur } }) => (
                      <TextInput
                        placeholder="Type your address"
                        onBlur={onBlur}
                        onChangeText={onChange}
                        style={styles.input}
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
                    render={({ field: { onChange, onBlur } }) => (
                      <TextInput
                        placeholder="Type your address"
                        onBlur={onBlur}
                        onChangeText={onChange}
                        style={styles.input}
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
                    render={({ field: { onChange, onBlur } }) => (
                      <TextInput
                        placeholder="Type your designation"
                        onBlur={onBlur}
                        onChangeText={onChange}
                        style={styles.input}
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
                    render={({ field: { onChange, onBlur } }) => (
                      <TextInput
                        placeholder="Type your short address"
                        onBlur={onBlur}
                        onChangeText={onChange}
                        style={styles.input}
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
    </>
  );
};

export default CustomerSignup;

const styles = StyleSheet.create({
  authWrapper: {
    flex: 1,
    marginTop: 80,
  },
  authTitle: {
    justifyContent: "center",
    textAlign: "center",
  },
  title: {
    textAlign: "center",
    color: colors.fontColor,
  },
  logo: {
    width: 224,
    height: 43,
    alignSelf: "center",
    marginBottom: 20,
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
