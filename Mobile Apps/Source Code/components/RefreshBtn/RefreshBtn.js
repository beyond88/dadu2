import React from "react";
import { Pressable, StyleSheet } from "react-native";
import { colors } from "../../themes/colors";
import { EvilIcons } from "@expo/vector-icons";

const RefreshBtn = ({ refetch, listData }) => {
  const handleRefetch = () => {
    listData([]);
    refetch();
  };
  return (
    <Pressable style={styles.refreshBtn} onPress={handleRefetch}>
      <EvilIcons name="refresh" size={24} color="white" />
    </Pressable>
  );
};

export default RefreshBtn;

const styles = StyleSheet.create({
  refreshBtn: {
    width: 34,
    height: 34,
    borderRadius: 4,
    backgroundColor: colors.green,
    justifyContent: "center",
    alignItems: "center",
  },
});
