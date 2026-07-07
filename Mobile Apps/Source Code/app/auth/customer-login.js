import Text from "../../components/text/Text";
import React, { useEffect } from "react";
import {
  View,
  Image,
  StyleSheet,
  TextInput,
  Pressable,
  ScrollView,
} from "react-native";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { colors } from "../../themes/colors";
import { LinearGradient } from "expo-linear-gradient";
import { capitalize } from "../../utils/helper";
import { Link, useRouter } from "expo-router";
import {
  useCustomerLoginMutation,
  useLoginMutation,
} from "../../redux/features/auth/authApi";
import Loading from "../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";
import Svg, { Path } from "react-native-svg";
import { useState } from "react";

//form validation schema
const schema = yup
  .object({
    email: yup.string().email().required(),
    password: yup.string().min(8).required(),
  })
  .required();
const CustomerLogin = () => {
  const [showPassword, setShowPassword] = useState(false);
  //Router
  const router = useRouter();
  //handle show hide password
  const togglePasswordVisibility = () => {
    setShowPassword(!showPassword);
  };
  //login mutation
  const [
    customerLogin,
    { data: loginData, isLoading, error: responseError, isSuccess, isError },
  ] = useCustomerLoginMutation();
  //get form data
  const {
    control,
    handleSubmit,
    formState: { errors },
    reset,
  } = useForm({
    resolver: yupResolver(schema),
  });
  const onSubmit = (data) => {
    customerLogin({
      email: data.email,
      password: data.password,
    });
  };

  useEffect(() => {
    if (isSuccess) {
      reset();
      router.push("/customer/dashboard");
    } else if (isError) {
      showMessage({
        message: responseError?.data?.message,
        type: "danger",
      });
    }
  }, [isError, responseError, isSuccess, loginData]);

  return (
    <>
      {isLoading && <Loading />}

      <View style={styles.authWrapper}>
        <View style={styles.authTitle}>
          <Image
            source={require("../../assets/images/logo.png")}
            style={styles.logo}
          />
          <Text preset="h3_r" style={styles.title}>
            Login your Customer account!{" "}
          </Text>
        </View>
        <ScrollView style={styles.formWrapper}>
          <View>
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
                Password
              </Text>
              <View style={styles.passwordWrap}>
                <Controller
                  control={control}
                  rules={{
                    required: true,
                  }}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      secureTextEntry={!showPassword}
                      placeholder="Type your password"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.input}
                    />
                  )}
                  name="password"
                />
                <Pressable
                  style={styles.passwordIcon}
                  onPress={togglePasswordVisibility}
                >
                  {showPassword ? (
                    <Svg
                      stroke="currentColor"
                      fill="currentColor"
                      strokeWidth="0"
                      viewBox="0 0 1024 1024"
                      className="eyeOpen"
                      height="1em"
                      width="1em"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <Path d="M942.2 486.2C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 0 0 0 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM512 766c-161.3 0-279.4-81.8-362.7-254C232.6 339.8 350.7 258 512 258c161.3 0 279.4 81.8 362.7 254C791.5 684.2 673.4 766 512 766zm-4-430c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm0 288c-61.9 0-112-50.1-112-112s50.1-112 112-112 112 50.1 112 112-50.1 112-112 112z"></Path>
                    </Svg>
                  ) : (
                    <Svg
                      stroke="currentColor"
                      fill="currentColor"
                      strokeWidth="0"
                      viewBox="0 0 1024 1024"
                      className="eyeClose"
                      height="1em"
                      width="1em"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <Path d="M942.2 486.2Q889.47 375.11 816.7 305l-50.88 50.88C807.31 395.53 843.45 447.4 874.7 512 791.5 684.2 673.4 766 512 766q-72.67 0-133.87-22.38L323 798.75Q408 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 0 0 0-51.5zm-63.57-320.64L836 122.88a8 8 0 0 0-11.32 0L715.31 232.2Q624.86 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 0 0 0 51.5q56.69 119.4 136.5 191.41L112.48 835a8 8 0 0 0 0 11.31L155.17 889a8 8 0 0 0 11.31 0l712.15-712.12a8 8 0 0 0 0-11.32zM149.3 512C232.6 339.8 350.7 258 512 258c54.54 0 104.13 9.36 149.12 28.39l-70.3 70.3a176 176 0 0 0-238.13 238.13l-83.42 83.42C223.1 637.49 183.3 582.28 149.3 512zm246.7 0a112.11 112.11 0 0 1 146.2-106.69L401.31 546.2A112 112 0 0 1 396 512z"></Path>
                      <Path d="M508 624c-3.46 0-6.87-.16-10.25-.47l-52.82 52.82a176.09 176.09 0 0 0 227.42-227.42l-52.82 52.82c.31 3.38.47 6.79.47 10.25a111.94 111.94 0 0 1-112 112z"></Path>
                    </Svg>
                  )}
                </Pressable>
              </View>
              {errors.password && (
                <Text style={styles.validationError}>
                  {capitalize(errors.password?.message)}
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
                    Customer Login
                  </Text>
                </LinearGradient>
              </Pressable>
            </View>
            <Link href="/auth/customer-signup" asChild>
              <View
                style={{
                  flexDirection: "row",
                  justifyContent: "flex-end",
                  marginTop: 10,
                }}
              >
                <Text preset="h3">Don't have an account? </Text>
                <Pressable onPress={() => router.push("/auth/customer-signup")}>
                  <Text preset="h3" style={{ color: colors.themeColor }}>
                    Sign Up
                  </Text>
                </Pressable>
              </View>
            </Link>
          </View>
          <View
            style={{
              flexDirection: "row",
              gap: 20,
              justifyContent: "center",
              marginTop: 30,
            }}
          >
            <Link href="/auth/admin-login">
              <LinearGradient
                colors={["#37DBD9", "#008AA1"]}
                style={styles.authButton}
              >
                <Text preset="h3" style={styles.buttonText}>
                  Admin
                </Text>
              </LinearGradient>
            </Link>
            <Link href="/auth/customer-login">
              <LinearGradient
                colors={["#37DBD9", "#008AA1"]}
                style={styles.authButton}
              >
                <Text preset="h3" style={styles.buttonText}>
                  Customer
                </Text>
              </LinearGradient>
            </Link>
          </View>
        </ScrollView>
      </View>
    </>
  );
};

export default CustomerLogin;

const styles = StyleSheet.create({
  authWrapper: {
    flex: 1,
    marginTop: 100,
  },
  formWrapper: {
    marginTop: 40,
    paddingHorizontal: 20,
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
    minWidth: 130,
  },
  buttonText: {
    color: colors.white,
  },
  validationError: {
    color: "#ff0000",
    marginTop: 5,
  },
  passwordWrap: {
    position: "relative",
  },
  passwordIcon: {
    position: "absolute",
    right: 10,
    top: 16,
  },
});
