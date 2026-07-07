import React, { useEffect } from "react";
import { View, StyleSheet, Pressable, TextInput } from "react-native";
import DateTimePicker from "@react-native-community/datetimepicker";
import { colors } from "../../themes/colors";
import { useState } from "react";
import Svg, { Path } from "react-native-svg";

const FormDate = ({ setSelectedDate, bg, selectedDate }) => {
  const [date, setDate] = useState();
  const [formatedDate, setFormatedDate] = useState("");
  const [showPicker, setShowPicker] = useState(false);
  const toggleDatePicker = () => {
    setShowPicker(!showPicker);
  };
  const dateChange = (event, selectedDate) => {
    const currentDate = selectedDate;
    setShowPicker(false);
    setDate(currentDate);
    setFormatedDate(new Date(currentDate).toISOString().slice(0, 10));
    setSelectedDate(new Date(currentDate).toISOString().slice(0, 10));
  };
  useEffect(() => {
    if (selectedDate) {
      setFormatedDate(selectedDate);
    } else {
      setFormatedDate(new Date().toISOString().slice(0, 10));
    }
  }, [setSelectedDate, selectedDate]);
  return (
    <View>
      <Pressable onPress={toggleDatePicker} style={styles.dateWrap}>
        <TextInput
          style={[styles.dateInput, bg && { backgroundColor: colors.grayBg }]}
          placeholder="Select Date"
          editable={false}
          value={`${formatedDate}`}
        />
        <View style={styles.calenderIcon}>
          <Svg
            xmlns="http://www.w3.org/2000/svg"
            width="17"
            height="18"
            viewBox="0 0 17 18"
            fill="none"
          >
            <Path
              d="M15 2.33337H1.66667C1.29848 2.33337 1 2.63185 1 3.00004V16.3334C1 16.7016 1.29848 17 1.66667 17H15C15.3682 17 15.6667 16.7016 15.6667 16.3334V3.00004C15.6667 2.63185 15.3682 2.33337 15 2.33337Z"
              stroke="#727F8B"
              strokeWidth="1.5"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
            <Path
              d="M12.3335 1V3.66667"
              stroke="#727F8B"
              strokeWidth="1.5"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
            <Path
              d="M4.3335 1V3.66667"
              stroke="#727F8B"
              strokeWidth="1.5"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
            <Path
              d="M1 6.33337H15.6667"
              stroke="#727F8B"
              strokeWidth="1.5"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
          </Svg>
        </View>
      </Pressable>
      {showPicker && (
        <DateTimePicker
          mode="date"
          display="spinner"
          value={date ? date : new Date()}
          onChange={dateChange}
        />
      )}
    </View>
  );
};

export default FormDate;

const styles = StyleSheet.create({
  dateWrap: {
    position: "relative",
  },
  dateInput: {
    width: "100%",
    height: 42,
    paddingHorizontal: 16,
    borderColor: colors.lineBorder,
    borderWidth: 1,
    marginBottom: 15,
    backgroundColor: colors.white,
    borderRadius: 5,
  },
  calenderIcon: {
    position: "absolute",
    right: 16,
    top: 14,
  },
  dropdown: {
    marginBottom: 15,
    backgroundColor: colors.white,
    borderColor: colors.lineBorder,
    borderWidth: 1,
    borderRadius: 5,
  },
});
