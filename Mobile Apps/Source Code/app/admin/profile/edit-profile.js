import {
  Pressable,
  StyleSheet,
  TextInput,
  View,
  ScrollView,
  Dimensions,
} from "react-native";
import { useForm, Controller } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import Text from "../../../components/text/Text";
import Topbar from "../../../components/Topbar/Topbar";
import { colors } from "../../../themes/colors";
import * as ImagePicker from "expo-image-picker";
import { useState } from "react";
import { LinearGradient } from "expo-linear-gradient";
// import CountryPicker from "react-native-country-picker-modal";
import Svg, { Path } from "react-native-svg";
import {
  useGetUserQuery,
  useUpdateUserMutation,
} from "../../../redux/features/user/userApi";

import { useEffect } from "react";
import { Image } from "expo-image";
import { capitalize } from "../../../utils/helper";
import Loading from "../../../components/Loading/Loading";
import { showMessage } from "react-native-flash-message";
//form validation schema
const schema = yup
  .object({
    name: yup.string().required(),
    email: yup.string().email().required(),
  })
  .required();
const EditProfile = () => {
  const [image, setImage] = useState();
  const [fileName, setFileName] = useState(null);

  const windowHeight = Dimensions.get("window").height;

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

  //Update user mutation

  const [
    updateUser,
    { data: updatedUserData, isLoading, isSuccess, error, isError },
  ] = useUpdateUserMutation();

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
    const formData = new FormData();

    formData.append("name", data?.name);
    formData.append("status", 1);
    formData.append("email", data?.email);
    formData.append("role", 1);
    formData.append("phone", data?.phone);
    if (fileName !== null) {
      formData.append("avatar", {
        uri: image,
        name: fileName,
        type: "image/*",
      });
    }
    if (data?.password) {
      formData.append("password", data?.password);
    }
    if (data?.confirmPassword) {
      formData.append("password_confirmation", data?.confirmPassword);
    }

    updateUser(formData);
  };

  //get user Data
  const { data } = useGetUserQuery();

  const userData = data?.data || {};

  //set input values

  useEffect(() => {
    Object.keys(userData)?.map((key) => {
      setValue(key, userData[key]);
    });
    setImage(userData?.avatar_url);
  }, [data, setValue]);

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
  }, [updatedUserData, isSuccess, isError]);

  return (
    <>
      {isLoading && <Loading />}
      <View>
        <Topbar title="Edit profile" />
        <ScrollView style={{ paddingHorizontal: 20 }}>
          <View style={{ height: windowHeight }}>
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
                  {userData?.name}
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
            <View>
              <View style={styles.formGroup}>
                <Text style={styles.label} preset="h2_sb">
                  Name
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      placeholder="Type your name"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.textInput}
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
                      placeholder="Type your email"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.textInput}
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
                  Mobile
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur, value } }) => (
                    <TextInput
                      maxLength={10}
                      placeholder="Type your mobile number"
                      keyboardType="number-pad"
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={[styles.textInput]}
                      value={value || ""}
                    />
                  )}
                  name="phone"
                />
              </View>
              <View style={styles.formGroup}>
                <Text style={styles.label} preset="h2_sb">
                  Password
                </Text>
                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      placeholder="********"
                      secureTextEntry={true}
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.textInput}
                    />
                  )}
                  name="password"
                />
              </View>
              <View style={styles.formGroup}>
                <Text style={styles.label} preset="h2_sb">
                  Confirm Password
                </Text>

                <Controller
                  control={control}
                  render={({ field: { onChange, onBlur } }) => (
                    <TextInput
                      placeholder="********"
                      secureTextEntry={true}
                      onBlur={onBlur}
                      onChangeText={onChange}
                      style={styles.textInput}
                    />
                  )}
                  name="confirmPassword"
                />
              </View>
              <View>
                <Pressable onPress={handleSubmit(onSubmit)}>
                  <LinearGradient
                    colors={["#37DBD9", "#008AA1"]}
                    style={styles.updateButton}
                  >
                    <Text preset="h3" style={styles.buttonText}>
                      Update
                    </Text>
                  </LinearGradient>
                </Pressable>
              </View>
            </View>
          </View>
        </ScrollView>
      </View>
    </>
  );
};

export default EditProfile;

const styles = StyleSheet.create({
  viewProfileCard: {
    backgroundColor: colors.white,
    paddingHorizontal: 20,
    paddingVertical: 20,
    marginBottom: 20,
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
});
