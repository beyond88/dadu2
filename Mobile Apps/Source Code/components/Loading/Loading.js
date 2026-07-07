import React from "react";
import { View, Image, StyleSheet } from "react-native";
import Spinner from "react-native-loading-spinner-overlay";

const Loading = () => {
  return (
    <View style={styles.container}>
      {/* Loading Spinner */}
      <Spinner
        visible={true} // Set this to control when to show/hide the spinner
        textContent={"Loading..."} // Loading text message
        textStyle={styles.spinnerText}
        animation="fade"
        overlayColor="rgba(0, 0, 0, 0.6)" // Background color of the overlay
      />

      {/* Logo Animation */}
    </View>
  );
};
export default Loading;
const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    position: "absolute",
  },
  logoContainer: {
    marginBottom: 20,
  },
  spinnerText: {
    color: "#fff", // Text color for the loading spinner
  },
});
