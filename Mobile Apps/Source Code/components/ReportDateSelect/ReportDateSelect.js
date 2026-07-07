import {
  Pressable,
  StyleSheet,
  TextInput,
  View,
  ScrollView,
} from "react-native";
import DateTimePicker from "@react-native-community/datetimepicker";
import { colors } from "../../themes/colors";
import { useState } from "react";
import Svg, { Path } from "react-native-svg";
import DropDownPicker from "react-native-dropdown-picker";
import { useEffect } from "react";
import { useGetWarehouseQuery } from "../../redux/features/common/commonApi";

const ReportDateSelect = ({
  setSelectedFromDate,
  setSelectedToDate,
  wareHouseShow,
  setSelectedWareHouse,
}) => {
  //Component  state
  const [fromDate, setFromDate] = useState();
  const [formatedFromDate, setFormatedFromDate] = useState("");
  const [showFromPicker, setShowFromPicker] = useState(false);
  const [toDate, setToDate] = useState();
  const [formatedToDate, setFormatedToDate] = useState("");
  const [showToPicker, setShowToPicker] = useState(false);
  const [open, setOpen] = useState(false);
  const [value, setValue] = useState(null);
  const [items, setItems] = useState([
    { label: "Warehouse 1", value: "warehouse 1" },
    { label: "Warehouse 2", value: "warehouse 2" },
  ]);

  //get warehouse list

  const {
    data: warehouseList,
    isLoading,
    error,
    isError,
    isSuccess,
  } = useGetWarehouseQuery();

  //set warehouse list
  useEffect(() => {
    if (isSuccess) {
      let warehouseListArray = [];
      warehouseList?.data?.map((item) => {
        warehouseListArray.push({
          label: item?.name,
          value: item?.id,
        });
      });
      setItems(warehouseListArray);
    }
  }, [isSuccess, warehouseList?.data]);
  //select warehouse
  useEffect(() => {
    if (value) {
      setSelectedWareHouse(value);
    }
  }, [value]);
  //date handler & picker
  const toggleFromDatePicker = () => {
    setShowFromPicker(!showFromPicker);
  };

  const fromDateChange = (event, selectedDate) => {
    const currentDate = selectedDate || fromDate;
    setShowFromPicker(false);
    setFromDate(currentDate);
    setFormatedFromDate(new Date(currentDate).toISOString().slice(0, 10));
    setSelectedFromDate(new Date(currentDate).toISOString().slice(0, 10));
  };

  const toggleToDatePicker = () => {
    setShowToPicker(!showToPicker);
  };

  const toDateChange = (event, selectedDate) => {
    const currentDate = selectedDate || fromDate;
    setShowToPicker(false);
    setToDate(currentDate);
    setFormatedToDate(new Date(currentDate).toISOString().slice(0, 10));
    setSelectedToDate(new Date(currentDate).toISOString().slice(0, 10));
  };
  return (
    <>
      {wareHouseShow && (
        <View style={{ position: "relative", zIndex: 1 }}>
          <DropDownPicker
            open={open}
            value={value}
            items={items}
            setOpen={setOpen}
            setValue={setValue}
            setItems={setItems}
            listMode="SCROLLVIEW"
            placeholder="Select Warehouse"
            placeholderStyle={{
              color: "#727F8B",
              fontWeight: "regular",
            }}
            zIndex={1}
            zIndexInverse={1000}
            style={styles.dropdown}
          />
        </View>
      )}

      <View>
        <View>
          <Pressable onPress={toggleFromDatePicker} style={styles.dateWrap}>
            <TextInput
              style={styles.dateInput}
              placeholder="From Date"
              editable={false}
              value={`${formatedFromDate}`}
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
          {showFromPicker && (
            <DateTimePicker
              mode="date"
              display="spinner"
              value={fromDate ? fromDate : new Date()}
              onChange={fromDateChange}
            />
          )}
        </View>
        <View>
          <Pressable onPress={toggleToDatePicker} style={styles.dateWrap}>
            <TextInput
              style={styles.dateInput}
              placeholder="To Date"
              editable={false}
              value={`${formatedToDate}`}
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
          {showToPicker && (
            <DateTimePicker
              mode="date"
              display="spinner"
              value={toDate ? toDate : new Date()}
              onChange={toDateChange}
            />
          )}
        </View>
      </View>
    </>
  );
};

export default ReportDateSelect;

const styles = StyleSheet.create({
  dateWrap: {
    position: "relative",
  },
  dateInput: {
    width: "100%",
    height: 48,
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
