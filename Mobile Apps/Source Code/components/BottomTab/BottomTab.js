import { Pressable, StyleSheet, View, Platform } from "react-native";
import Text from "../text/Text";
import Svg, { Path, Rect, Circle, Mask } from "react-native-svg";
import { colors } from "../../themes/colors";
import { Link } from "expo-router";

const BottomTab = ({ handleMenuOpen, role }) => {
  return (
    <View style={styles.tabWrapper}>
      <Link href={`/${role}/dashboard`} asChild>
        <Pressable style={styles.tabItem}>
          <Svg
            xmlns="http://www.w3.org/2000/svg"
            width="19"
            height="21"
            viewBox="0 0 19 21"
            fill="none"
          >
            <Path
              d="M0.864868 7.62162L9.37838 1L17.8919 7.62162V18.027C17.8919 18.5288 17.6926 19.01 17.3378 19.3648C16.983 19.7196 16.5018 19.9189 16 19.9189H2.75676C2.255 19.9189 1.77379 19.7196 1.41899 19.3648C1.06419 19.01 0.864868 18.5288 0.864868 18.027V7.62162Z"
              stroke="#727F8B"
              strokeWidth="1.62162"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
            <Path
              d="M6.54056 19.9189V10.4595H12.2162V19.9189"
              stroke="#727F8B"
              strokeWidth="1.62162"
              strokeLinecap="round"
              strokeLinejoin="round"
            />
          </Svg>
          <Text preset="h3_r" style={styles.tabText}>
            Home
          </Text>
        </Pressable>
      </Link>
      <Link
        href={`/${role}/${role === "admin" ? "pos-invoice" : "invoice"}`}
        asChild
      >
        <Pressable style={styles.tabItem}>
          <Svg
            xmlns="http://www.w3.org/2000/svg"
            width="22"
            height="20"
            viewBox="0 0 22 20"
            fill="none"
          >
            <Rect
              x="0.243256"
              width="21.0811"
              height="19.4595"
              rx="4.86486"
              fill="#727F8B"
            />
            <Rect
              x="12.4054"
              y="7.29736"
              width="7.2973"
              height="4.86486"
              rx="2.43243"
              fill="white"
            />
            <Rect
              x="14.027"
              y="8.91895"
              width="2.43243"
              height="1.62162"
              rx="0.810811"
              fill="#10A0B1"
            />
          </Svg>
          <Text preset="h3_r" style={styles.tabText}>
            Invoice
          </Text>
        </Pressable>
      </Link>
      <Pressable style={styles.menuTabItem} onPress={handleMenuOpen}>
        <Svg
          width="20"
          height="16"
          viewBox="0 0 20 16"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <Rect width="20" height="2" rx="1" fill="white" />
          <Rect y="7" width="20" height="2" rx="1" fill="white" />
          <Rect y="14" width="12" height="2" rx="1" fill="white" />
        </Svg>
      </Pressable>
      <Link href={`/${role}/report/purchase`} asChild>
        <Pressable style={styles.tabItem}>
          <Svg
            width="15"
            height="16"
            viewBox="0 0 15 16"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <Path
              d="M6.4 4H8V12H6.4V4ZM9.6 7.2H11.2V12H9.6V7.2ZM3.2 8.8H4.8V12H3.2V8.8ZM9.6 1.6H1.6V14.4H12.8V4.8H9.6V1.6ZM0 0.79344C0 0.35524 0.357992 0 0.7988 0H10.4L14.3998 4L14.4 15.194C14.4 15.6391 14.0441 16 13.6053 16H0.79472C0.355808 16 0 15.6358 0 15.2066V0.79344Z"
              fill="#727F8B"
            />
          </Svg>

          <Text preset="h3_r" style={styles.tabText}>
            Report
          </Text>
        </Pressable>
      </Link>
      <Link href={`/${role}/profile`} asChild>
        <Pressable style={styles.tabItem}>
          <Svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
          >
            <Path
              d="M18.5588 19.5488C17.5654 16.8918 15.0036 15 12 15C8.99638 15 6.4346 16.8918 5.44117 19.5488M18.5588 19.5488C20.6672 17.7154 22 15.0134 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 15.0134 3.33285 17.7154 5.44117 19.5488M18.5588 19.5488C16.8031 21.0756 14.5095 22 12 22C9.49052 22 7.19694 21.0756 5.44117 19.5488"
              stroke="#4F5B6D"
              strokeWidth="1.5"
              strokeLinejoin="round"
            />
            <Circle
              cx="3"
              cy="3"
              r="3"
              transform="matrix(1 0 0 -1 9 12)"
              stroke="#4F5B6D"
              strokeWidth="1.5"
              strokeLinejoin="round"
            />
          </Svg>
          <Text preset="h3_r" style={styles.tabText}>
            Profile
          </Text>
        </Pressable>
      </Link>
    </View>
  );
};

export default BottomTab;

const styles = StyleSheet.create({
  tabWrapper: {
    flexDirection: "row",
    justifyContent: "space-between",
    backgroundColor: colors.white,
    paddingHorizontal: 20,
    position: "absolute",
    bottom: 0,
    width: "100%",
    ...Platform.select({
      ios: {
        shadowColor: "rgba(40, 196, 201, 0.20)",
        shadowOffset: { width: 0, height: 0 },
        shadowOpacity: 1,
        shadowRadius: 20,
      },
      android: {
        elevation: 20,
      },
    }),
  },

  tabItem: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 15,
  },
  menuTabItem: {
    width: 50,
    height: 50,
    borderRadius: 50,
    backgroundColor: "#10A0B1",
    alignItems: "center",
    justifyContent: "center",
    marginTop: -20,
  },
  tabText: {
    color: colors.fontColor,
    marginTop: 3,
  },
});
