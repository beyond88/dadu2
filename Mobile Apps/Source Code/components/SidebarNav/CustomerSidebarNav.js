import { Image, Pressable, ScrollView, StyleSheet, View } from "react-native";
import Text from "../text/Text";
import { Link, useRouter } from "expo-router";
import { colors } from "../../themes/colors";
import { useState } from "react";
import Svg, { Path } from "react-native-svg";
import {
  AntDesign,
  FontAwesome5,
  MaterialCommunityIcons,
} from "@expo/vector-icons";

const CustomerSidebarNav = ({ handleMenuClose }) => {
  const [menuItems, setMenuItems] = useState([
    {
      label: "Home",
      iconSrc: <AntDesign name="home" size={24} color="black" />,
      href: "/customer/dashboard",
    },
    {
      label: "Profile",
      iconSrc: <FontAwesome5 name="user-circle" size={24} color="black" />,
      href: "/customer/profile",
    },
    {
      label: "Invoice",
      iconSrc: <AntDesign name="filetext1" size={24} color="black" />,
      href: "/customer/invoice",
    },

    {
      label: "Draft Invoice",
      iconSrc: <AntDesign name="filetext1" size={24} color="black" />,
      href: "/customer/draft-invoice",
    },

    {
      label: "Product Return",
      iconSrc: <FontAwesome5 name="product-hunt" size={24} color="black" />,
      subMenuItems: [
        {
          label: "Return Request",
          href: "/customer/product/return-request",
        },
        {
          label: "Return Request List",
          href: "/customer/product/return-request-list",
        },
      ],
      isOpen: false,
    },
    {
      label: "Reports",
      iconSrc: (
        <MaterialCommunityIcons
          name="file-multiple-outline"
          size={24}
          color="black"
        />
      ),
      subMenuItems: [
        {
          label: "Purchases Report",
          href: "/customer/report/purchase",
        },
        {
          label: "Payments report",
          href: "/customer/report/payment",
        },
      ],
      isOpen: false,
    },
    // Add more menu items as needed
  ]);
  //toggle menu
  const toggleSubMenu = (index) => {
    const updatedMenuItems = menuItems.map((item, i) => ({
      ...item,
      isOpen: i === index ? !item.isOpen : false,
    }));
    setMenuItems(updatedMenuItems);
  };
  const router = useRouter();
  return (
    <View style={styles.sidebarWrapper}>
      <View style={styles.logoWrap}>
        <Image
          source={require("../../assets/images/logo.png")}
          style={styles.logo}
        />
      </View>
      <ScrollView style={styles.menuWrapper}>
        <View>
          {menuItems?.map((item, index) => (
            <View key={index}>
              <Pressable
                style={styles.menuLink}
                onPress={() => {
                  if (item.href) {
                    router.push(item.href);
                    handleMenuClose();
                  } else {
                    toggleSubMenu(index);
                  }
                }}
              >
                <Text>{item.iconSrc}</Text>
                <Text preset="h2_sb">{item?.label}</Text>
                {item?.subMenuItems && (
                  <View style={styles.subMenuArrow}>
                    <Svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                    >
                      <Path
                        d="M6 9L12 15L18 9"
                        stroke="#4F5B6D"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      />
                    </Svg>
                  </View>
                )}
              </Pressable>

              {item?.isOpen && (
                <View>
                  {item?.subMenuItems?.map((subMenuItem, subIndex) => (
                    <Pressable style={styles.subMenu} key={subIndex}>
                      <Link href={subMenuItem.href} asChild>
                        <Pressable onPress={handleMenuClose}>
                          <Text preset="h3">{subMenuItem.label}</Text>
                        </Pressable>
                      </Link>
                    </Pressable>
                  ))}
                </View>
              )}
            </View>
          ))}
        </View>
      </ScrollView>
    </View>
  );
};

export default CustomerSidebarNav;

const styles = StyleSheet.create({
  sidebarWrapper: {
    flex: 1,
    backgroundColor: colors.white,
    position: "absolute",
    height: "100%",
    zIndex: 2,
    top: 0,
    left: 0,
    width: 275,
  },
  logoWrap: {
    paddingVertical: 30,
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
    width: "100%",
    paddingHorizontal: 20,
  },
  logo: {
    width: 180,
    height: 40,
    objectFit: "contain",
  },
  menuWrapper: {
    paddingHorizontal: 20,
  },
  menuLink: {
    alignItems: "center",
    paddingVertical: 20,
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
    width: "100%",
    flexDirection: "row",
    gap: 12,
    position: "relative",
  },
  subMenuArrow: {
    position: "absolute",
    right: 0,
    top: 20,
  },
  subMenu: {
    paddingLeft: 20,
    paddingVertical: 5,
  },
});
